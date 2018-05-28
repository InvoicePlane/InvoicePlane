<?php

namespace FI\Modules\Groups;

class GroupOptions
{
    public function resetNumberOptions()
    {
        return [
            '0' => trans('fi.never'),
            '1' => trans('fi.yearly'),
            '2' => trans('fi.monthly'),
            '3' => trans('fi.weekly'),
        ];
    }
}