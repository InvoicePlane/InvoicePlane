<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../../application/helpers/invoice_reminder_helper.php';

/**
 * The reminder scheduler decides, for one invoice on one day, which single reminder
 * to send. Everything that can go wrong here is silent in production — a missed
 * reminder looks identical to a client who simply paid — so the edge cases around
 * missed cron runs and repeat offsets are covered explicitly.
 */
class InvoiceReminderScheduleTest extends TestCase
{
    private const BEFORE = [1, 3, 7];

    private const AFTER = [1, 7, 14];

    public static function beforeDueOffsets(): array
    {
        return [
            'exactly 7 days out' => [-7, 'before_due:7'],
            'exactly 3 days out' => [-3, 'before_due:3'],
            'exactly 1 day out'  => [-1, 'before_due:1'],
            'due today'          => [0, 'before_due:1'],
        ];
    }

    #[Test]
    public function it_parses_a_comma_separated_offset_list(): void
    {
        $this->assertSame([1, 3, 7], parse_reminder_offsets('7,3,1'));
    }

    #[Test]
    public function it_ignores_blank_negative_and_non_numeric_offsets(): void
    {
        $this->assertSame([3, 7], parse_reminder_offsets(' 7, ,-2,abc,3 '));
    }

    #[Test]
    public function it_deduplicates_offsets(): void
    {
        $this->assertSame([3, 7], parse_reminder_offsets('3,7,3'));
    }

    #[Test]
    public function it_returns_no_offsets_for_an_empty_setting(): void
    {
        $this->assertSame([], parse_reminder_offsets(''));
        $this->assertSame([], parse_reminder_offsets(null));
    }

    #[Test]
    public function it_sends_nothing_before_the_first_before_due_offset(): void
    {
        [$send, $skip] = $this->resolve(-10);

        $this->assertNull($send);
        $this->assertSame([], $skip);
    }

    #[Test]
    #[DataProvider('beforeDueOffsets')]
    public function it_sends_the_current_before_due_offset(int $days_relative, string $expected): void
    {
        [$send] = $this->resolve($days_relative);

        $this->assertSame($expected, $send);
    }

    #[Test]
    public function it_sends_only_the_latest_missed_before_due_offset_and_skips_the_rest(): void
    {
        // Cron was down; the invoice is now 1 day from due with nothing sent yet.
        // The client should get one reminder, not three.
        [$send, $skip] = $this->resolve(-1);

        $this->assertSame('before_due:1', $send);
        $this->assertEqualsCanonicalizing(['before_due:7', 'before_due:3'], $skip);
    }

    #[Test]
    public function it_sends_the_latest_missed_overdue_offset_and_skips_the_rest(): void
    {
        // Twenty days overdue on a cold start: only the 14-day notice goes out.
        [$send, $skip] = $this->resolve(20);

        $this->assertSame('overdue:14', $send);
        $this->assertEqualsCanonicalizing(
            ['before_due:7', 'before_due:3', 'before_due:1', 'overdue:1', 'overdue:7'],
            $skip
        );
    }

    #[Test]
    public function it_skips_unsent_before_due_offsets_once_the_invoice_is_overdue(): void
    {
        [$send, $skip] = $this->resolve(1);

        $this->assertSame('overdue:1', $send);
        $this->assertEqualsCanonicalizing(['before_due:7', 'before_due:3', 'before_due:1'], $skip);
    }

    #[Test]
    public function it_generates_repeat_offsets_past_the_last_configured_offset(): void
    {
        // after = [1,7,14] with a 7-day repeat continues 21, 28, ...
        $this->assertSame('overdue:21', $this->resolve(21, [], 7)[0]);
        $this->assertSame('overdue:28', $this->resolve(28, [], 7)[0]);
        $this->assertSame('overdue:21', $this->resolve(22, [], 7)[0], 'holds the bucket between repeats');
    }

    #[Test]
    public function it_handles_an_empty_after_list_with_repeat_days_set(): void
    {
        $this->assertNull($this->resolve(4, [], 5, self::BEFORE, [])[0]);
        $this->assertSame('overdue:5', $this->resolve(5, [], 5, self::BEFORE, [])[0]);
        $this->assertSame('overdue:10', $this->resolve(10, [], 5, self::BEFORE, [])[0]);
    }

    #[Test]
    public function it_sends_nothing_when_no_offsets_are_configured(): void
    {
        $this->assertNull($this->resolve(-3, [], 0, [], [])[0]);
        $this->assertNull($this->resolve(30, [], 0, [], [])[0]);
    }

    #[Test]
    public function it_returns_nothing_when_every_candidate_slot_is_already_logged(): void
    {
        $logged = ['before_due:7', 'before_due:3', 'before_due:1'];

        [$send, $skip] = $this->resolve(-1, $logged);

        $this->assertNull($send);
        $this->assertSame([], $skip);
    }

    #[Test]
    public function it_bounds_generated_repeat_offsets_for_a_very_old_invoice(): void
    {
        // A 1-day repeat on an invoice years overdue must not build an unbounded list.
        $plan = reminder_schedule_for_invoice(5000, [], [1], 1, []);

        $this->assertLessThanOrEqual(REMINDER_MAX_GENERATED_OFFSETS, count($plan['skip']));
        $this->assertNotNull($plan['send']);
    }

    #[Test]
    public function it_sends_each_offset_exactly_once_over_the_life_of_an_invoice(): void
    {
        // Walk a daily cron from 10 days before due to 30 days overdue and assert
        // every configured offset fires once and only once.
        $logged = [];
        $sent   = [];

        for ($day = -10; $day <= 30; $day++) {
            $plan = reminder_schedule_for_invoice($day, self::BEFORE, self::AFTER, 7, $logged);

            foreach ($plan['skip'] as $slot) {
                $logged[] = reminder_slot_key($slot['type'], $slot['offset']);
            }

            if ($plan['send'] === null) {
                continue;
            }

            $key = reminder_slot_key($plan['send']['type'], $plan['send']['offset']);

            $this->assertNotContains($key, $sent, 'reminder ' . $key . ' was sent twice');

            $sent[]   = $key;
            $logged[] = $key;
        }

        $this->assertSame(
            [
                'before_due:7', 'before_due:3', 'before_due:1',
                'overdue:1', 'overdue:7', 'overdue:14', 'overdue:21', 'overdue:28',
            ],
            $sent
        );
    }

    /** @return array{0: ?string, 1: string[]} send slot key and skipped slot keys */
    private function resolve(int $days, array $logged = [], int $repeat = 0, array $before = self::BEFORE, array $after = self::AFTER): array
    {
        $plan = reminder_schedule_for_invoice($days, $before, $after, $repeat, $logged);

        $send = $plan['send'] === null
            ? null
            : reminder_slot_key($plan['send']['type'], $plan['send']['offset']);

        $skip = array_map(
            static fn (array $slot): string => reminder_slot_key($slot['type'], $slot['offset']),
            $plan['skip']
        );

        return [$send, $skip];
    }
}
