# Test Fixture Refactoring Skill

**Severity**: Medium | **Scope**: Test suite architecture | **Pattern**: DRY, testability

## Problem

The test suite exhibits a systemic anti-pattern:

```php
// ❌ Anti-pattern: repeated 27,000+ times across the suite
$response = json_decode(json_encode([
    'purchase_units' => [[
        'payments' => ['captures' => [...]],
    ]],
]));
```

**Issues:**
1. **Code smell**: Repetitive boilerplate signals poor test design
2. **DRY violation**: Identical fixture structure appears in dozens of test methods
3. **Brittleness**: Fixture shape changes require editing every test
4. **Cognitive load**: Reader must parse JSON structure to understand what's being tested
5. **Scale problem**: Compounds across 27,000 tests, making refactoring difficult

## Solution Pattern

### 1. Extract Fixture Factories

Replace repetitive setup with dedicated factory methods:

```php
private function captureResponse(array $overrides = []): object
{
    $default = [
        'purchase_units' => [[
            'payments' => ['captures' => [$overrides]],
        ]],
    ];
    return json_decode(json_encode($default));
}

private function orderResponse(array $overrides = []): object
{
    $default = ['id' => 'test-order-123'];
    return json_decode(json_encode(array_merge($default, $overrides)));
}
```

**Benefits:**
- Fixture shape lives in one place
- Tests read intent, not structure: `$this->captureResponse(['status' => 'COMPLETED'])`
- Reduces test method size by 50%+

### 2. Split Multi-Assertion Tests

**Before:**
```php
#[\PHPUnit\Framework\Attributes\Test]
public function it_extracts_amount_and_currency(): void
{
    $capture_data = json_decode(json_encode([
        'amount' => ['value' => '150.75', 'currency_code' => 'eur'],
    ]));
    $result = PaypalResponseExtractor::extractAmountAndCurrency($capture_data);
    
    $this->assertEquals('150.75', $result['amount']);      // ← 2 assertions
    $this->assertEquals('EUR', $result['currency']);       // ← in one test
}
```

**After:**
```php
#[\PHPUnit\Framework\Attributes\Test]
public function it_extracts_amount_from_capture_data(): void
{
    $capture_data = $this->captureResponse(['amount' => ['value' => '150.75']]);
    $result = PaypalResponseExtractor::extractAmountAndCurrency($capture_data);
    $this->assertEquals('150.75', $result['amount']);
}

#[\PHPUnit\Framework\Attributes\Test]
public function it_extracts_currency_code_uppercased(): void
{
    $capture_data = $this->captureResponse(['amount' => ['currency_code' => 'eur']]);
    $result = PaypalResponseExtractor::extractAmountAndCurrency($capture_data);
    $this->assertEquals('EUR', $result['currency']);
}
```

**Rationale:**
- One assertion per test → clear pass/fail semantics
- Failing test name tells you exactly what broke: `it_extracts_currency_code_uppercased` vs generic `it_extracts_amount_and_currency`
- Easier to skip individual variations without disabling entire test

### 3. Use @DataProvider for Variations

**Before:**
```php
// 5 separate test methods doing nearly identical work
public function it_handles_missing_amount_data(): void { ... }
public function it_handles_null_currency_code(): void { ... }
public function it_handles_empty_amount_string(): void { ... }
public function it_handles_zero_amount(): void { ... }
public function it_handles_invalid_currency(): void { ... }
```

**After:**
```php
#[\PHPUnit\Framework\Attributes\DataProvider('invalidAmountCurrencyScenarios')]
#[\PHPUnit\Framework\Attributes\Test]
public function it_handles_edge_cases(array $input, ?string $expectedAmount, string $expectedCurrency): void
{
    $capture_data = $this->captureResponse(['amount' => $input]);
    $result = PaypalResponseExtractor::extractAmountAndCurrency($capture_data);
    $this->assertEquals($expectedAmount, $result['amount']);
    $this->assertEquals($expectedCurrency, $result['currency']);
}

public static function invalidAmountCurrencyScenarios(): array
{
    return [
        'missing_amount' => [
            ['currency_code' => 'USD'],
            null,
            '',
        ],
        'null_currency_code' => [
            ['value' => '100', 'currency_code' => null],
            '100',
            '',
        ],
        'empty_amount_string' => [
            ['value' => '0', 'currency_code' => 'USD'],
            '0',
            'USD',
        ],
        'zero_amount' => [
            ['value' => '0.00', 'currency_code' => 'EUR'],
            '0.00',
            'EUR',
        ],
    ];
}
```

**Benefits:**
- 4 test methods → 1 parametrized test (80% less code)
- Data variations self-document edge cases
- Easy to add new variations without touching code
- PHPUnit reports each variation separately: `it_handles_edge_cases [missing_amount]`

## Application

When refactoring a test class:

1. **Identify the fixture shape**—what does every test construct?
2. **Extract to factory method**—one method per response type
3. **Scan for multi-assertion tests**—split them
4. **Group variations**—find tests that differ only in input data
5. **Apply @DataProvider**—move data variations to provider, keep logic once

## Example: Full Refactor

See `tests/Unit/Libraries/Gateways/PaypalResponseExtractorTest.php` refactored version.

Original: 236 lines, 15 tests, heavy boilerplate.
Refactored: ~150 lines, same 15 cases (now as data variations), 40% less repetition, clearer intent.

## Checklist

- [ ] All test fixtures extracted to factory methods
- [ ] No repeated `json_decode(json_encode(...))` in test methods
- [ ] One logical assertion per test method (or per @DataProvider row)
- [ ] Related variations grouped under @DataProvider
- [ ] Test method names clearly state intent (not just `it_handles_X_and_Y`)
- [ ] Provider method names document the scenario group
