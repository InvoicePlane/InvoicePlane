<?php

function addon_path($path = '')
{
    return base_path('custom/addons') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
}

function attachment_path($path = '')
{
    return storage_path('attachments') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
}
