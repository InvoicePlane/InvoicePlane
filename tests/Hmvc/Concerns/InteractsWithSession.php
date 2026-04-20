<?php

declare(strict_types=1);

namespace Tests\Hmvc\Concerns;

trait InteractsWithSession
{
    protected function seedSession(array $data): void
    {
        if (!function_exists('get_instance')) {
            return;
        }

        $CI = &get_instance();

        if (!isset($CI->session)) {
            return;
        }

        foreach ($data as $key => $value) {
            $CI->session->set_userdata($key, $value);
        }
    }

    protected function clearSession(): void
    {
        if (!function_exists('get_instance')) {
            return;
        }

        $CI = &get_instance();

        if (!isset($CI->session)) {
            return;
        }

        $CI->session->sess_destroy();
    }

    protected function actingAsAdmin(int $userId = 1): void
    {
        $this->seedSession([
            'user_id'       => $userId,
            'user_type'     => 1,
            'user_email'    => 'admin@test.local',
            'user_name'     => 'Test Admin',
            'user_company'  => 'Test Co',
            'user_language' => 'english',
        ]);
    }

    protected function actingAsGuest(): void
    {
        $this->clearSession();
    }
}
