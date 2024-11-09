<?php

/**
 * @AllowDynamicProperties
 */
class UblTypeEnum
{
    public const CIUS_V2    = 'CIUS_V2';
    public const NLCIUS_V2  = 'NLCIUS_V2';
    public const ZUGFERD_V1 = 'ZUGFERD_V1';

    /**
     * Attempts to retrieve an enum instance based on a value.
     *
     * @param string $value the template type value
     *
     * @return stdClass|null returns an object with the value if found, or null otherwise
     */
    public static function tryFrom($value)
    {
        $values = self::getValues();

        if (in_array($value, $values)) {
            $returnObject = new stdClass();
            $returnObject->value = $value;

            return $returnObject;
        }
    }

    /**
     * Retrieves all enum cases as objects.
     *
     * @return array returns an array of stdClass objects, each containing a value property
     */
    public static function cases()
    {
        $values = self::getValues();
        $returnArray = [];

        foreach ($values as $value) {
            $valueObject = new stdClass();
            $valueObject->value = $value;
            $returnArray[] = $valueObject;
        }

        return $returnArray;
    }

    private static function getValues()
    {
        return [
            self::CIUS_V2,
            self::NLCIUS,
            self::ZUGFERD_V1,
        ];
    }
}
