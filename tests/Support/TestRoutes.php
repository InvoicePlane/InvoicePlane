<?php

namespace Tests\Support;

final class TestRoutes
{
    public const CLIENTS_INDEX = '/clients';
    public const GUEST_INDEX = '/guest';

    public static function clientsStatus(string $status): string
    {
        return '/clients/status/' . $status;
    }

    public static function clientsForm(?int $clientId = null): string
    {
        if ($clientId === null) {
            return '/clients/form';
        }

        return '/clients/form/' . $clientId;
    }

    public static function clientsView(int $clientId): string
    {
        return '/clients/view/' . $clientId;
    }

    public static function clientsDelete(int $clientId): string
    {
        return '/clients/delete/' . $clientId;
    }

    private function __construct() {}
}
