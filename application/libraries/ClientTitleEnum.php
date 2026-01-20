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

    public const VALUES = [
        self::MISTER,
        self::MISSUS,
        self::DOCTOR,
        self::PROFESSOR,
    ];

    public static function tryFrom($value)
    {
        if (false !== $searchResult = array_search($value, self::VALUES, true)) {
            $returnObject        = new StdClass();
            $returnObject->value = $searchResult;

            return $returnObject;
        }
    }

    /**
     * @return list<\StdClass>
     */
    public static function cases(): array
    {
        $values   = self::VALUES;
        $values[] = self::CUSTOM;

        $returnArray = [];

        foreach ($values as $value) {
            $valueObject        = new StdClass();
            $valueObject->value = $value;

            $returnArray[] = $valueObject;
        }

        return $returnArray;
    }
}
