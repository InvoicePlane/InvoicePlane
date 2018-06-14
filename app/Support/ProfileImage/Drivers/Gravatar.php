<?php

/**
 * InvoicePlane
 *
 * @package     InvoicePlane
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (C) 2014 - 2018 InvoicePlane
 * @license     https://invoiceplane.com/license
 * @link        https://invoiceplane.com
 *
 * Based on FusionInvoice by Jesse Terry (FusionInvoice, LLC)
 */

namespace IP\Support\ProfileImage\Drivers;

use IP\Modules\Users\Models\User;
use IP\Support\ProfileImage\ProfileImageInterface;

class Gravatar implements ProfileImageInterface
{
    public function getProfileImageUrl(User $user)
    {
        return 'https://www.gravatar.com/avatar/' . md5(strtolower($user->email)) . '?d=mm';
    }
}