<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Connection-scoped database lock preventing overlapping provider syncs.
 */
final class IntegrationSyncLock
{
    private ?string $name = null;

    public function __construct(private object $database) {}

    public function __destruct()
    {
        $this->release();
    }

    public function acquire(int $merchantClientId): bool
    {
        if ($this->name !== null) {
            throw new LogicException('Integration synchronization lock is already held.');
        }

        $name = 'ip:einvoice:sync:' . $merchantClientId;
        $row  = $this->database
            ->query('SELECT GET_LOCK(?, 0) AS acquired', [$name])
            ->row_array();

        if ((int) ($row['acquired'] ?? 0) !== 1) {
            return false;
        }

        $this->name = $name;

        return true;
    }

    public function release(): void
    {
        if ($this->name === null) {
            return;
        }

        try {
            $this->database->query('SELECT RELEASE_LOCK(?)', [$this->name]);
        } catch (Throwable) {
            // A lost database connection releases MySQL advisory locks itself.
        } finally {
            $this->name = null;
        }
    }
}
