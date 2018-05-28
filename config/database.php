<?php

return [

    'connections' => [

        /**
         * This is where you configure your database.
         * Typically you should only have to configure the host, database, username and password values.
         * InvoicePlane is designed and tested using MySQL only. Other database types may or may not work.
         */
        'mysql' => [
            'host'      => 'localhost',
            'database'  => 'invoiceplane',
            'username'  => 'root',
            'password'  => 'password',
            'prefix'    => '',

            'driver'    => 'mysql',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'strict'    => false,
        ],

    ],

    /**
     * Ignore the options below here.
     */

    'default' => env('DB_CONNECTION', 'mysql'),

    'fetch' => PDO::FETCH_CLASS,

    'migrations' => 'migrations',

];