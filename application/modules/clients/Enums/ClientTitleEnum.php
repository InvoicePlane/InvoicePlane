<?php

class ClientTitleEnum
{
    public const MISTER    = 'mr';

    public const MISSUS    = 'mrs';

    public const DOCTOR    = 'doctor';

    public const PROFESSOR = 'professor';

    public const CUSTOM    = 'custom';

    public static function tryFrom($value)
    {

        $values = self::getValues();

        $searchResult = array_search($value, $values);

        if ($searchResult) {

            $returnObject = new StdClass();
            $returnObject->value = $searchResult;

            return $returnObject;

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
