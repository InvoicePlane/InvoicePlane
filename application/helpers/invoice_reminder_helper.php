<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

/*
 * Payment reminder scheduling.
 *
 * Deliberately free of CodeIgniter, database and global state: every decision about
 * *which* reminder is due today is made here from plain integers, so it can be unit
 * tested without a request or a database. The caller supplies today's day offset and
 * the slots already recorded; this file never sends, reads or writes anything.
 *
 * NOTE: this helper is loaded directly by tests/ (which defines BASEPATH itself), so
 * it must not call get_instance() or touch any CodeIgniter state.
 */

if ( ! defined('REMINDER_TYPE_BEFORE_DUE')) {
    define('REMINDER_TYPE_BEFORE_DUE', 'before_due');
}

if ( ! defined('REMINDER_TYPE_OVERDUE')) {
    define('REMINDER_TYPE_OVERDUE', 'overdue');
}

/*
 * Upper bound on generated repeat offsets. Without it a repeat interval of 1 day on a
 * years-old invoice would build an unbounded candidate list on every cron run.
 */
if ( ! defined('REMINDER_MAX_GENERATED_OFFSETS')) {
    define('REMINDER_MAX_GENERATED_OFFSETS', 100);
}

if ( ! function_exists('parse_reminder_offsets')) {
    /**
     * Parse an admin-entered day list such as "7, 3, 1" into a clean list of offsets.
     *
     * Junk, negative and duplicate entries are dropped rather than rejected: this runs
     * unattended from cron, so a typo in one entry must not take the whole schedule down.
     *
     * @return int[] ascending, unique, >= 0
     */
    function parse_reminder_offsets(?string $raw): array
    {
        if ($raw === null || trim($raw) === '') {
            return [];
        }

        $offsets = [];
        foreach (explode(',', $raw) as $piece) {
            $piece = trim($piece);
            if ($piece === '' || ! ctype_digit($piece)) {
                continue;
            }

            $offsets[] = (int) $piece;
        }

        $offsets = array_values(array_unique($offsets));
        sort($offsets, SORT_NUMERIC);

        return $offsets;
    }
}

if ( ! function_exists('reminder_slot_key')) {
    /**
     * Canonical identity of one reminder slot, matching the (invoice_id, reminder_type,
     * reminder_offset) unique key on ip_invoice_reminders.
     */
    function reminder_slot_key(string $type, int $offset): string
    {
        return $type . ':' . $offset;
    }
}

if ( ! function_exists('reminder_overdue_offsets')) {
    /**
     * Every overdue offset that has been reached by day $days_overdue: the configured
     * fixed offsets, plus the repeating tail past the last configured one.
     *
     * @param int[] $after       configured days-after-due offsets
     * @param int   $repeat_days repeat interval past the last configured offset, 0 = off
     *
     * @return int[] ascending, unique
     */
    function reminder_overdue_offsets(array $after, int $repeat_days, int $days_overdue): array
    {
        $reached = array_values(array_filter($after, static fn ($offset): bool => $offset <= $days_overdue));

        if ($repeat_days > 0) {
            // The tail continues from the last configured offset, or from the due date
            // itself when no fixed offsets are configured at all.
            $cursor    = $after === [] ? 0 : max($after);
            $generated = 0;
            while ($generated < REMINDER_MAX_GENERATED_OFFSETS) {
                $cursor += $repeat_days;
                if ($cursor > $days_overdue) {
                    break;
                }

                $reached[] = $cursor;
                $generated++;
            }
        }

        $reached = array_values(array_unique($reached));
        sort($reached, SORT_NUMERIC);

        return $reached;
    }
}

if ( ! function_exists('reminder_schedule_for_invoice')) {
    /**
     * Decide what to do for one invoice today.
     *
     * At most one reminder is ever sent per invoice per run. When cron has been down and
     * several offsets were missed, only the most recent one is sent and the rest are
     * returned as skips, so a multi-day outage produces one catch-up email rather than a
     * burst of stale ones. The skips are still recorded by the caller, which is what keeps
     * the decision idempotent and auditable.
     *
     * @param int      $days_relative DATEDIFF(CURDATE(), invoice_date_due): negative before due
     * @param int[]    $before        configured days-before-due offsets
     * @param int[]    $after         configured days-after-due offsets
     * @param int      $repeat_days   repeat interval past the last after offset, 0 = off
     * @param string[] $logged        slot keys already recorded, from reminder_slot_key()
     *
     * @return array{send: ?array{type: string, offset: int}, skip: list<array{type: string, offset: int}>}
     */
    function reminder_schedule_for_invoice(
        int $days_relative,
        array $before,
        array $after,
        int $repeat_days,
        array $logged
    ): array {
        $logged = array_flip($logged);

        // Slots that may be sent today, oldest trigger first.
        $candidates = [];
        // Slots whose window has closed unsent. These are recorded so they are never
        // reconsidered, but they can never be chosen as today's send.
        $expired = [];

        if ($days_relative <= 0) {
            // Not yet due. A before-due offset has been reached once the invoice is that
            // many days (or fewer) away, so the reachable set shrinks as the date nears
            // and the *smallest* reached offset is the most recent trigger.
            $days_until = -$days_relative;
            foreach ($before as $offset) {
                if ($offset >= $days_until) {
                    $candidates[] = [REMINDER_TYPE_BEFORE_DUE, $offset];
                }
            }

            // Oldest trigger first, most recent last: the caller takes the last pending
            // entry as the one to send and records the rest as skipped.
            usort($candidates, static fn (array $a, array $b): int => $b[1] <=> $a[1]);
        } else {
            // Past due. Every before-due offset's window has closed, so any still unsent
            // expire here. They must not be sendable: mailing "your invoice is due in
            // 7 days" for an invoice that is already overdue contradicts itself, and
            // would be the only thing to send whenever the invoice is past due but has
            // not yet reached its first overdue offset.
            foreach ($before as $offset) {
                $expired[] = [REMINDER_TYPE_BEFORE_DUE, $offset];
            }

            foreach (reminder_overdue_offsets($after, $repeat_days, $days_relative) as $offset) {
                $candidates[] = [REMINDER_TYPE_OVERDUE, $offset];
            }

            // Largest reached overdue offset is the most recent trigger, so it sorts
            // last and is the one picked below.
            usort($candidates, static fn (array $a, array $b): int => $a[1] <=> $b[1]);
        }

        $unlogged = static function (array $slots) use ($logged): array {
            $out = [];
            foreach ($slots as [$type, $offset]) {
                if ( ! isset($logged[reminder_slot_key($type, $offset)])) {
                    $out[] = ['type' => $type, 'offset' => $offset];
                }
            }

            return $out;
        };

        $skip    = $unlogged($expired);
        $pending = $unlogged($candidates);

        if ($pending === []) {
            return ['send' => null, 'skip' => $skip];
        }

        // The most recent trigger sorts last; everything before it was missed.
        $send = array_pop($pending);

        return ['send' => $send, 'skip' => array_merge($skip, $pending)];
    }
}
