<?php

namespace Tests\Unit\Core;

/**
 * Unit tests for Mdl_Settings pure-PHP logic.
 *
 * Covered:
 *  - setting() — key lookup with default fallback
 *  - save_batch() — separation of inserts vs updates
 *  - set_setting() — in-memory mutation
 *
 * @group unit
 * @group models
 * @group settings
 */
class StubMdl_Settings
{
    public array $settings = [];

    public function setting(string $key, string $default = ''): string
    {
        return (isset($this->settings[$key]) && $this->settings[$key] !== '')
            ? (string) $this->settings[$key]
            : $default;
    }

    public function set_setting(string $key, mixed $value): void
    {
        $this->settings[$key] = $value;
    }

    /**
     * Replicates the insert/update classification from Mdl_Settings::save_batch().
     *
     * @param array    $settings     key => value pairs to persist
     * @param string[] $existingKeys keys already present in ip_settings
     *
     * @return array{0: array, 1: array} [toUpdate, toInsert]
     */
    public function classifyBatch(array $settings, array $existingKeys): array
    {
        $existingMap = array_flip($existingKeys);
        $toUpdate    = [];
        $toInsert    = [];

        foreach ($settings as $key => $value) {
            if (isset($existingMap[$key])) {
                $toUpdate[$key] = $value;
            } else {
                $toInsert[$key] = $value;
            }
        }

        return [$toUpdate, $toInsert];
    }
}
