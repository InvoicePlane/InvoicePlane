<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

#[AllowDynamicProperties]
class ClientTitleEnum
{
    const MISTER    = 'mr';

    const MISSUS    = 'mrs';

    const DOCTOR    = 'doctor';

    const PROFESSOR = 'professor';

    const CUSTOM    = 'custom';

    private static function getValues()
    {
        return [
            self::MISTER,
            self::MISSUS,
            self::DOCTOR,
            self::PROFESSOR,
            self::CUSTOM
        ];
    }

    public static function tryFrom($value)
    {

        $values = self::getValues();

        $searchResult = array_search($value, $values);

        if ($searchResult) {
            $returnObject = new StdClass();
            $returnObject->value = $searchResult;

            return $returnObject;
        } else {
            return null;
        }
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
}
