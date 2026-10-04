<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

final class SupplierInvoiceAccess
{
    public const ADMINISTRATOR = 1;

    public function canRead(int $userType): bool
    {
        return $userType === self::ADMINISTRATOR;
    }

    public function canWrite(int $userType): bool
    {
        return $userType === self::ADMINISTRATOR;
    }

    public function canManagePayments(int $userType): bool
    {
        return $this->canWrite($userType);
    }

    public function canManageAttachments(int $userType): bool
    {
        return $this->canWrite($userType);
    }

    public function canArchive(int $userType): bool
    {
        return $this->canWrite($userType);
    }
}
