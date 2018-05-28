<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Support\ProfileImage;

use FI\Modules\Users\Models\User;

interface ProfileImageInterface
{
    public function getProfileImageUrl(User $user);
}