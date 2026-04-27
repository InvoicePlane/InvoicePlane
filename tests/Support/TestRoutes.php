<?php

namespace Tests\Support;

final class TestRoutes
{
    public const CLIENTS_INDEX = '/clients';
    public const CLIENTS_FORM = '/clients/form';

    public static function clientsStatus(string $status): string
    {
        return '/clients/status/' . $status;
    }

    public static function clientsView(int $clientId): string
    {
        return '/clients/view/' . $clientId;
    }

    private function __construct() {}
}
