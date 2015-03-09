<?php

class  SetTimezoneClass
{
    public function setTimezone()
    {
        if (!ini_get('date.timezone')) {
            date_default_timezone_set('UTC');
        }
    }
}