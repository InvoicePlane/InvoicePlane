<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

#[AllowDynamicProperties]
class ClientTitleEnum
{
    public const MISTER = 'mr';

    public const MISSUS = 'mrs';

    public const DOCTOR = 'doctor';

    public const PROFESSOR = 'professor';

    public const CUSTOM = 'custom';

    public static function tryFrom($value)
    {
        $values = self::getValues();

        $searchResult = array_search($value, $values, true);

        if ($searchResult !== false) {
            $returnObject        = new StdClass();
            $returnObject->value = $values[$searchResult];

            return $returnObject;
        }
        
        return null;
    }

    public static function cases()
    {
        $values = self::getValues();

        $returnArray = [];

        foreach ($values as $value) {
            $valueObject        = new StdClass();
            $valueObject->value = $value;

            $returnArray[] = $valueObject;
        }

        return $returnArray;
    }

    private static function getValues()
    {
        return [
            self::MISTER,
            self::MISSUS,
            self::DOCTOR,
            self::PROFESSOR,
            self::CUSTOM,
        ];
    }
}
