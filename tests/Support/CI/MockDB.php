<?php

/**
 * Fluent mock database for CI3 model tests.
 *
 * Records calls and returns predictable values so model methods
 * that only read query results can be tested without a real DB.
 */
#[AllowDynamicProperties]
class MockDB
{
    /** @var array<string, mixed> */
    public array $calls = [];

    /** @var array<int, object> result rows returned by get()/result() */
    public array $rows = [];

    public int $insertId = 0;

    public int $affectedRows = 0;

    public function setRows(array $rows): static
    {
        $this->rows = array_map(fn ($r) => (object) $r, $rows);

        return $this;
    }

    // -----------------------------------------------------------------
    // Fluent Active-Record stubs — all return $this for chaining
    // -----------------------------------------------------------------

    public function select(mixed $select = '*', bool $escape = true): static { return $this; }

    public function distinct(bool $val = true): static { return $this; }

    public function from(string $from, bool $overwrite = false): static { return $this; }

    public function join(string $table, string $cond, string $type = '', bool $escape = true): static { return $this; }

    public function where(mixed $key, mixed $value = null, bool $escape = true): static { return $this; }

    public function or_where(mixed $key, mixed $value = null, bool $escape = true): static { return $this; }

    public function where_in(mixed $key = null, mixed $values = null, bool $escape = true): static { return $this; }

    public function or_where_in(mixed $key = null, mixed $values = null, bool $escape = true): static { return $this; }

    public function where_not_in(mixed $key = null, mixed $values = null, bool $escape = true): static { return $this; }

    public function like(mixed $field, string $match = '', string $side = 'both', bool $escape = true, bool $insensitiveSearch = false): static { return $this; }

    public function not_like(mixed $field, string $match = '', string $side = 'both', bool $escape = true, bool $insensitiveSearch = false): static { return $this; }

    public function group_by(mixed $by, bool $escape = true): static { return $this; }

    public function having(mixed $key, mixed $value = null, bool $escape = true): static { return $this; }

    public function order_by(string $orderby, string $direction = '', bool $escape = true): static { return $this; }

    public function limit(?int $value = null, ?int $offset = null): static { return $this; }

    public function offset(int $offset): static { return $this; }

    public function group_start(): static { return $this; }

    public function group_end(): static { return $this; }

    public function or_group_start(): static { return $this; }

    public function not_group_start(): static { return $this; }

    public function or_not_group_start(): static { return $this; }

    // -----------------------------------------------------------------
    // Query execution
    // -----------------------------------------------------------------

    public function get(?string $table = null, ?int $limit = null, ?int $offset = null): static
    {
        $this->calls[] = ['get', $table];

        return $this;
    }

    public function insert(string $table, mixed $set = null, ?bool $escape = null, int $batchSize = 100, bool $resetData = true): bool
    {
        $this->calls[] = ['insert', $table, $set];

        return true;
    }

    public function update(string $table, mixed $set = null, mixed $where = null, ?int $limit = null, bool $returnSQL = false): bool
    {
        $this->calls[] = ['update', $table, $set, $where];

        return true;
    }

    public function delete(mixed $table = '', mixed $where = '', ?int $limit = null, bool $resetData = true): mixed
    {
        $this->calls[] = ['delete', $table, $where];

        return true;
    }

    // -----------------------------------------------------------------
    // Result methods
    // -----------------------------------------------------------------

    public function result(): array
    {
        return $this->rows;
    }

    public function result_array(): array
    {
        return array_map(fn ($r) => (array) $r, $this->rows);
    }

    public function row(int $n = 0, string $type = 'object'): ?object
    {
        return $this->rows[$n] ?? null;
    }

    public function row_array(int $n = 0): ?array
    {
        return isset($this->rows[$n]) ? (array) $this->rows[$n] : null;
    }

    public function num_rows(): int
    {
        return count($this->rows);
    }

    public function insert_id(): int
    {
        return $this->insertId;
    }

    public function affected_rows(): int
    {
        return $this->affectedRows;
    }

    public function count_all_results(?string $table = null, bool $reset = true): int
    {
        return count($this->rows);
    }

    // CI3 query() compatibility
    public function query(string $sql, mixed $binds = false, bool $returnObject = true): static
    {
        $this->calls[] = ['query', $sql];

        return $this;
    }

    public function last_query(): string
    {
        return '';
    }

    public function escape(mixed $str): string
    {
        return "'" . addslashes((string) $str) . "'";
    }

    public function escape_str(mixed $str, bool $like = false): string
    {
        return addslashes((string) $str);
    }
}
