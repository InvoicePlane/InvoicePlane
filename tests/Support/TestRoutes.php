<?php

namespace Tests\Support;

final class TestRoutes
{
    public const CLIENTS_INDEX = '/clients';
    public const GUEST_INDEX = '/guest';
    public const GUEST_GET = '/guest/get';
    public const GUEST_VIEW = '/guest/view';

    public const USER_CLIENTS_INDEX = '/user_clients';


    public const SESSIONS_LOGIN = '/sessions/login';
    public const CRM_AJAX_MODAL_CLIENT_LOOKUP = '/crm/ajax/modal_client_lookup';

    public static function crmAjaxGetClientDetails(int|string $clientId): string
    {
        return '/crm/ajax/get_client_details/' . $clientId;
    }

    public static function userClientsForm(?int $id = null): string
    {
        if ($id === null) {
            return '/user_clients/form';
        }

        return '/user_clients/form/' . $id;
    }

    public static function userClientsDelete(int $id): string
    {
        return '/user_clients/delete/' . $id;
    }

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
