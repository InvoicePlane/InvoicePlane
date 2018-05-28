<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Support;

class cURL
{
    public static function post($url, $postVars)
    {
        $urlString = '';

        foreach ($postVars as $key => $var)
        {
            $postVars[$key] = urlencode($var);
        }

        foreach ($postVars as $key => $value)
        {
            $urlString .= $key . '=' . $value . '&';
        }

        rtrim($urlString, '&');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($postVars));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $urlString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);

        curl_close($ch);

        return $result;
    }
}