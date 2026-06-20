<?php

/**
 * Minimal CI3 session mock.
 */
class MockSession
{
    private array $data = [];

    private array $flashdata = [];

    public function userdata(string $key): mixed
    {
        return $this->data[$key] ?? null;
    }

    public function set_userdata(string|array $newdata, mixed $newval = ''): void
    {
        if (is_array($newdata)) {
            $this->data = array_merge($this->data, $newdata);
        } else {
            $this->data[$newdata] = $newval;
        }
    }

    public function set_flashdata(string $key, mixed $value): void
    {
        $this->flashdata[$key] = $value;
    }

    public function flashdata(string $key): mixed
    {
        return $this->flashdata[$key] ?? null;
    }

    public function unset_userdata(string|array $key): void
    {
        if (is_array($key)) {
            foreach ($key as $k) {
                unset($this->data[$k]);
            }
        } else {
            unset($this->data[$key]);
        }
    }

    public function mark_as_flash(string $key): bool
    {
        return isset($this->data[$key]);
    }
}
