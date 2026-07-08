#!/usr/bin/env python3
"""
Build the SQLite test database from InvoicePlane MySQL migration files.

Converts MySQL DDL/DML to SQLite-compatible syntax.
"""

import re
import os
import sys
import glob
import sqlite3

DB_PATH = sys.argv[1] if len(sys.argv) > 1 else os.path.join(
    os.path.dirname(__file__), '..', '..', 'storage', 'test.sqlite'
)

SQL_DIR = os.path.join(os.path.dirname(__file__), '..', '..', 'application', 'modules', 'setup', 'sql')


def mysql_to_sqlite(sql: str) -> list[str]:
    """Convert a block of MySQL SQL to a list of SQLite-compatible statements."""

    # Remove /* ... */ block comments FIRST (before # removal, since # can appear inside them)
    sql = re.sub(r'/\*.*?\*/', '', sql, flags=re.DOTALL)
    # Remove MySQL-style comments (# ...)
    sql = re.sub(r'#[^\n]*', '', sql)

    # Replace backtick identifiers with plain names (SQLite uses them without quotes)
    sql = re.sub(r'`([^`]+)`', r'\1', sql)

    # -----------------------------------------------------------------------
    # CREATE TABLE transformations
    # -----------------------------------------------------------------------

    # Remove table-level KEY / INDEX lines inside CREATE TABLE (keep PRIMARY KEY)
    sql = re.sub(r',\s*(?:UNIQUE\s+)?(?:KEY|INDEX)\s+\w+\s*\([^)]+\)', '', sql, flags=re.IGNORECASE)

    # Convert AUTO_INCREMENT col type → INTEGER (SQLite INTEGER PK is autoincrement)
    sql = re.sub(r'INT\(\d+\)\s+NOT NULL\s+AUTO_INCREMENT', 'INTEGER NOT NULL', sql, flags=re.IGNORECASE)
    sql = re.sub(r'INT\(\d+\)\s+AUTO_INCREMENT', 'INTEGER', sql, flags=re.IGNORECASE)

    # Strip closing ENGINE/CHARSET/etc. options from CREATE TABLE
    sql = re.sub(
        r'\)\s*(?:ENGINE\s*=\s*\S+\s*)?(?:AUTO_INCREMENT\s*=\s*\d+\s*)?(?:DEFAULT\s+CHARSET\s*=\s*\S+\s*)?(?:COLLATE\s*=\s*\S+\s*)?;',
        ');',
        sql,
        flags=re.IGNORECASE
    )

    # -----------------------------------------------------------------------
    # Column type conversions
    # -----------------------------------------------------------------------
    sql = re.sub(r'\bLONGTEXT\b', 'TEXT', sql, flags=re.IGNORECASE)
    sql = re.sub(r'\bMEDIUMTEXT\b', 'TEXT', sql, flags=re.IGNORECASE)
    sql = re.sub(r'\bTINYTEXT\b', 'TEXT', sql, flags=re.IGNORECASE)
    sql = re.sub(r'\bVARCHAR\(\d+\)', 'TEXT', sql, flags=re.IGNORECASE)
    sql = re.sub(r'\bINT\(\d+\)', 'INTEGER', sql, flags=re.IGNORECASE)
    sql = re.sub(r'\bTINYINT\(\d+\)', 'INTEGER', sql, flags=re.IGNORECASE)
    sql = re.sub(r'\bSMALLINT\(\d+\)', 'INTEGER', sql, flags=re.IGNORECASE)
    sql = re.sub(r'\bBIGINT\(\d+\)', 'INTEGER', sql, flags=re.IGNORECASE)
    sql = re.sub(r'\bDECIMAL\(\d+,\s*\d+\)', 'REAL', sql, flags=re.IGNORECASE)
    sql = re.sub(r'\bFLOAT\(\d+,\s*\d+\)', 'REAL', sql, flags=re.IGNORECASE)
    sql = re.sub(r'\bDOUBLE\(\d+,\s*\d+\)', 'REAL', sql, flags=re.IGNORECASE)

    # Convert ENUM(...) → TEXT
    sql = re.sub(r"\bENUM\s*\([^)]+\)", 'TEXT', sql, flags=re.IGNORECASE)

    # -----------------------------------------------------------------------
    # ALTER TABLE transformations — split multi-ADD / multi-DROP into individual
    # -----------------------------------------------------------------------

    # Skip complex ALTER statements mixing ADD PRIMARY KEY with MODIFY (unsupported)
    sql = re.sub(
        r'ALTER TABLE\s+\w+\s+ADD\s+PRIMARY KEY\s*\([^)]+\)\s*,\s*MODIFY[^;]+;',
        '-- complex PK+MODIFY skipped;',
        sql,
        flags=re.IGNORECASE
    )

    # Remove ADD [UNIQUE] INDEX / ADD PRIMARY KEY clauses BEFORE multi-ADD split
    # so they don't interfere with the ADD COLUMN expansion
    sql = re.sub(r',\s*ADD\s+(?:UNIQUE\s+)?INDEX\s+\w+\s*\([^)]+\)', '', sql, flags=re.IGNORECASE)
    sql = re.sub(r',\s*ADD\s+PRIMARY KEY\s*\([^)]+\)', '', sql, flags=re.IGNORECASE)
    # Standalone ALTER TABLE ... ADD PRIMARY KEY ...
    sql = re.sub(r'ALTER TABLE\s+\w+\s+ADD\s+PRIMARY KEY\s*\([^)]+\)\s*;', '-- ADD PRIMARY KEY skipped;', sql, flags=re.IGNORECASE)

    sql = _expand_multi_add_column(sql)

    # Convert ALTER TABLE ... DROP col1, DROP col2, ... → individual DROPs
    sql = _expand_multi_drop_column(sql)

    # Remove AFTER <col> clause (SQLite doesn't support column ordering)
    sql = re.sub(r'\s+AFTER\s+\w+', '', sql, flags=re.IGNORECASE)

    # SQLite: ALTER TABLE ADD COLUMN col TEXT NOT NULL (without DEFAULT) is illegal.
    # Add a DEFAULT '' for TEXT NOT NULL and DEFAULT 0 for INTEGER NOT NULL.
    def add_default(m: re.Match) -> str:
        col_def = m.group(1)
        if re.search(r'\bDEFAULT\b', col_def, re.IGNORECASE):
            return m.group(0)  # already has DEFAULT
        if re.search(r'\bTEXT\b', col_def, re.IGNORECASE):
            return m.group(0).rstrip() + " DEFAULT ''"
        if re.search(r'\bINTEGER\b|\bREAL\b', col_def, re.IGNORECASE):
            return m.group(0).rstrip() + ' DEFAULT 0'
        return m.group(0) + " DEFAULT ''"

    sql = re.sub(
        r'(ALTER TABLE\s+\w+\s+ADD\s+COLUMN\s+\w+\s+\S+\s+NOT\s+NULL(?:\s+NOT\s+NULL)?)',
        add_default,
        sql,
        flags=re.IGNORECASE
    )

    # Convert ALTER TABLE ... CHANGE [COLUMN] old new ... → skip (unsupported)
    sql = re.sub(
        r'ALTER TABLE\s+\w+\s+CHANGE\s+(?:COLUMN\s+)?\w+\s+\w+\s+[^;]+;',
        '-- CHANGE COLUMN skipped;',
        sql,
        flags=re.IGNORECASE
    )

    # Convert ALTER TABLE ... MODIFY [COLUMN] ... → skip (unsupported)
    sql = re.sub(
        r'ALTER TABLE\s+\w+\s+MODIFY\s+(?:COLUMN\s+)?\w+\s+[^;]+;',
        '-- MODIFY COLUMN skipped;',
        sql,
        flags=re.IGNORECASE
    )

    # Convert INSERT ... ON DUPLICATE KEY UPDATE → INSERT OR REPLACE
    sql = re.sub(
        r'\bINSERT\s+INTO\b',
        'INSERT OR IGNORE INTO',
        sql,
        flags=re.IGNORECASE
    )
    sql = re.sub(
        r'\s+ON\s+DUPLICATE\s+KEY\s+UPDATE\s+[^;]+;',
        ';',
        sql,
        flags=re.IGNORECASE
    )

    # -----------------------------------------------------------------------
    # UPDATE: skip MySQL-specific function forms
    # -----------------------------------------------------------------------
    # Must run AFTER ALTER TABLE expansion so DOTALL doesn't consume preceding DDL
    sql = re.sub(r'UPDATE\s+\w+\s+SET\s+\w+\s*=\s*CONCAT\s*\([^;]+;', '-- CONCAT update skipped;', sql, flags=re.IGNORECASE | re.DOTALL)
    sql = re.sub(r'UPDATE\s+\w+\s+SET\s+\w+\s*=\s*CASE\s[^;]+;', '-- CASE update skipped;', sql, flags=re.IGNORECASE | re.DOTALL)

    # Split into statements
    parts = re.split(r';\s*\n', sql)
    return [p.strip() for p in parts if p.strip() and p.strip() != '--']


def _expand_multi_drop_column(sql: str) -> str:
    """
    SQLite only supports one DROP COLUMN per ALTER TABLE (SQLite 3.35+).
    Split: ALTER TABLE t DROP a, DROP b; → two statements.
    """
    def expand_drop(m: re.Match) -> str:
        table = m.group(1)
        drops = re.findall(r'DROP\s+(?:COLUMN\s+)?(\w+)', m.group(2), re.IGNORECASE)
        return ';\n'.join(f'ALTER TABLE {table} DROP COLUMN {col}' for col in drops)

    return re.sub(
        r'ALTER TABLE\s+(\w+)\s+(DROP\s+\w+(?:\s*,\s*DROP\s+\w+)+)',
        expand_drop,
        sql,
        flags=re.IGNORECASE
    )


def _expand_multi_add_column(sql: str) -> str:
    """
    SQLite only supports one ADD COLUMN per ALTER TABLE.
    Split: ALTER TABLE t ADD COL a ..., ADD COL b ...; → two statements.
    Also handles: ALTER TABLE t ADD a INT, ADD b INT; (without COLUMN keyword).
    """

    def expand_match(m: re.Match) -> str:
        table = m.group(1)
        adds_block = m.group(2)
        # Split on commas that precede ADD
        parts = re.split(r',\s*(?=ADD\b)', adds_block, flags=re.IGNORECASE)
        stmts = []
        for part in parts:
            part = part.strip()
            if re.match(r'^ADD\b', part, re.IGNORECASE):
                # Ensure ADD COLUMN keyword
                part = re.sub(r'^ADD\s+(?!COLUMN\b)', 'ADD COLUMN ', part, flags=re.IGNORECASE)
                stmts.append(f'ALTER TABLE {table} {part}')
        return ';\n'.join(stmts) if stmts else m.group(0)

    sql = re.sub(
        r'ALTER TABLE\s+(\w+)\s+(ADD(?:\s+COLUMN)?\s+\w[^;]+(?:,\s*ADD[^;]+)+)',
        expand_match,
        sql,
        flags=re.IGNORECASE
    )
    return sql


def build_db(db_path: str) -> None:
    os.makedirs(os.path.dirname(os.path.abspath(db_path)), exist_ok=True)
    if os.path.exists(db_path):
        os.unlink(db_path)

    con = sqlite3.connect(db_path)
    con.execute('PRAGMA journal_mode=WAL')
    con.execute('PRAGMA foreign_keys=OFF')

    files = sorted(glob.glob(os.path.join(SQL_DIR, '*.sql')))

    for filepath in files:
        with open(filepath) as f:
            raw = f.read()

        statements = mysql_to_sqlite(raw)

        for stmt in statements:
            if not stmt or stmt.startswith('--'):
                continue
            try:
                con.execute(stmt)
            except sqlite3.OperationalError as e:
                msg = str(e)
                # Tolerate idempotency errors from re-running migrations
                if any(k in msg for k in ('duplicate column', 'already exists', 'no such column', 'no such table')):
                    pass
                else:
                    print(f'ERROR in {os.path.basename(filepath)}:', file=sys.stderr)
                    print(f'  stmt: {stmt[:150]}', file=sys.stderr)
                    print(f'  err:  {msg}', file=sys.stderr)

    con.commit()
    seed_defaults(con)
    con.commit()
    con.close()
    print(f'Test database built: {db_path}')


def seed_defaults(con: sqlite3.Connection) -> None:
    count = con.execute('SELECT COUNT(*) FROM ip_settings').fetchone()[0]
    if count > 0:
        return

    settings = [
        ('default_language',        'english'),
        ('currency_symbol',         '$'),
        ('currency_symbol_placement','before'),
        ('date_format',             'Y-m-d'),
        ('time_format',             'H:i'),
        ('pdf_engine',              'pdfmake'),
        ('pdf_paper_size',          'a4'),
        ('pdf_paper_orientation',   'portrait'),
        ('read_only_toggle',        'paid'),
        ('next_invoice_number',     '1'),
        ('next_quote_number',       '1'),
        ('invoicenumber_prefix',    ''),
        ('quotenumber_prefix',      ''),
        ('disable_setup',           '1'),
        ('sumex',                   '0'),
    ]
    for key, val in settings:
        con.execute(
            'INSERT OR IGNORE INTO ip_settings (setting_key, setting_value) VALUES (?,?)',
            (key, val)
        )

    import hashlib, hmac
    # Simple password hash placeholder — tests won't actually authenticate through CI3 login
    con.execute(
        "INSERT OR IGNORE INTO ip_users (user_name,user_email,user_password,user_type,user_active) "
        "VALUES ('Admin','admin@test.local','$2y$10$testhashedpassword.notreal',1,1)"
    )

    con.execute(
        "INSERT OR IGNORE INTO ip_invoice_groups "
        "(invoice_group_name,invoice_group_identifier_format,invoice_group_next_id,invoice_group_left_pad) "
        "VALUES ('Default','{{{id}}}',1,0)"
    )


if __name__ == '__main__':
    build_db(DB_PATH)
