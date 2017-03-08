<?php

function format_client($client)
{
    if ($client->client_surname != "") {
        return $client->client_name . " " . $client->client_surname;
    }
    return $client->client_name;
}

function format_gender($gender)
{
    if ($gender == 0) {
        return trans('gender_male');
    }
    if ($gender == 1) {
        return trans('gender_female');
    }
    return trans('gender_other');
}
