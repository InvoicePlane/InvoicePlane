<?php

defined('BASEPATH') || exit('No direct script access allowed');

if ( ! function_exists('split_sql_statements')) {
    /**
     * Split a SQL script into individual statements on the top-level `;`
     * terminator only.
     *
     * A naive explode(';', $sql) breaks whenever a semicolon appears inside a
     * string literal (e.g. COMMENT 'FK to x; NULL for legacy rows') or a
     * comment, sending invalid fragments to the database. This walker tracks
     * string literals ('...', "...", `...`), line comments (-- , #) and block
     * comments (/* ... *\/) so semicolons inside them are never treated as
     * statement terminators.
     *
     * @return string[] trimmed statements, in order, with empty ones removed
     */
    function split_sql_statements(string $sql): array
    {
        $statements = [];
        $buffer     = '';
        $length     = mb_strlen($sql);

        $in_single = false; // '...'
        $in_double = false; // "..."
        $in_ident  = false; // `...`
        $in_line   = false; // -- or #  until end of line
        $in_block  = false; // /* ... */

        for ($i = 0; $i < $length; $i++) {
            $ch   = $sql[$i];
            $next = $i + 1 < $length ? $sql[$i + 1] : '';

            if ($in_line) {
                $buffer .= $ch;
                if ($ch === "\n") {
                    $in_line = false;
                }
                continue;
            }

            if ($in_block) {
                $buffer .= $ch;
                if ($ch === '*' && $next === '/') {
                    $buffer .= $next;
                    $i++;
                    $in_block = false;
                }
                continue;
            }

            if ($in_single) {
                $buffer .= $ch;
                if ($ch === '\\' && $next !== '') {
                    $buffer .= $next;
                    $i++;
                } elseif ($ch === "'" && $next === "'") { // doubled '' escape
                    $buffer .= $next;
                    $i++;
                } elseif ($ch === "'") {
                    $in_single = false;
                }
                continue;
            }

            if ($in_double) {
                $buffer .= $ch;
                if ($ch === '\\' && $next !== '') {
                    $buffer .= $next;
                    $i++;
                } elseif ($ch === '"' && $next === '"') {
                    $buffer .= $next;
                    $i++;
                } elseif ($ch === '"') {
                    $in_double = false;
                }
                continue;
            }

            if ($in_ident) {
                $buffer .= $ch;
                if ($ch === '`') {
                    $in_ident = false;
                }
                continue;
            }

            // Not inside any string/comment: detect openers and the terminator.
            $after = $i + 2 < $length ? $sql[$i + 2] : '';

            if ($ch === '-' && $next === '-' && ($after === '' || ctype_space($after))) {
                $in_line = true;
                $buffer .= $ch;
                continue;
            }
            if ($ch === '#') {
                $in_line = true;
                $buffer .= $ch;
                continue;
            }
            if ($ch === '/' && $next === '*') {
                $in_block = true;
                $buffer .= $ch . $next;
                $i++;
                continue;
            }
            if ($ch === "'") {
                $in_single = true;
                $buffer .= $ch;
                continue;
            }
            if ($ch === '"') {
                $in_double = true;
                $buffer .= $ch;
                continue;
            }
            if ($ch === '`') {
                $in_ident = true;
                $buffer .= $ch;
                continue;
            }
            if ($ch === ';') {
                $statement = trim($buffer);
                if ($statement !== '') {
                    $statements[] = $statement;
                }
                $buffer = '';
                continue;
            }

            $buffer .= $ch;
        }

        $tail = trim($buffer);
        if ($tail !== '') {
            $statements[] = $tail;
        }

        return $statements;
    }
}
