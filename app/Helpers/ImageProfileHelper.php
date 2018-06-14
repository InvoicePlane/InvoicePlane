<?php

use IP\Support\ProfileImage\ProfileImageFactory;

function profileImageUrl($user)
{
    $profileImage = ProfileImageFactory::create();

    return $profileImage->getProfileImageUrl($user);
}
