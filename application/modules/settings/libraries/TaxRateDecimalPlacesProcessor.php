<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class TaxRateDecimalPlacesProcessor
{
    public function validateAndNormalize($input, int $min, int $max): int
    {
        $decimal_places = filter_var(
            $input,
            FILTER_VALIDATE_INT,
            [
                'options' => [
                    'min_range' => $min,
                    'max_range' => $max,
                ],
            ]
        );

        if ($decimal_places === false) {
            throw new InvalidArgumentException('Invalid decimal places value');
        }

        return $decimal_places;
    }

    public function shouldAlterSchema(int $current, int $new): bool
    {
        return $current !== $new;
    }
}
