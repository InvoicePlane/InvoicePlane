<?php

namespace IP\Modules\Groups;

class GroupOptions
{
    public function resetNumberOptions()
    {
        return [
            '0' => trans('ip.never'),
            '1' => trans('ip.yearly'),
            '2' => trans('ip.monthly'),
            '3' => trans('ip.weekly'),
        ];
    }
}