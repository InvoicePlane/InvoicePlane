<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute muss akzeptiert werden.',
    'active_url'           => ':attribute ist keine gültige URL.',
    'after'                => ':attribute muss nach :date liegen.',
    'after_or_equal'       => ':attribute muss nach oder an :date liegen.',
    'alpha'                => ':attribute darf nur Buchstaben enthalten.',
    'alpha_dash'           => ':attribute darf nur Buchstaben, Nummern und Striche enthalten.',
    'alpha_num'            => ':attribute darf nur Buchstaben und Nummern enthalten.',
    'array'                => ':attribute muss eine Liste sein.',
    'before'               => ':attribute muss vor :date liegen.',
    'before_or_equal'      => ':attribute muss vor oder an :date liegen.',
    'between'              => [
        'numeric' => ':attribute muss zwischen :min und :max liegen.',
        'file'    => ':attribute muss zwischen :min und :max KB liegen.',
        'string'  => ':attribute muss zwischen :min und :max Zeichen lang sein.',
        'array'   => ':attribute muss mindestens :min und maximal :max Einträge haben.',
    ],
    'boolean'              => ':attribute Feld muss wahr oder falsch sein.',
    'confirmed'            => ':attribute Bestätigung stimmt nicht überein.',
    'date'                 => ':attribute ist nicht ein gültiges Datum.',
    'date_format'          => ':attribute passt nicht zum Format :format.',
    'different'            => ':attribute und :other müssen sich unterscheiden.',
    'digits'               => ':attribute muss :digits Zeichen lang sein.',
    'digits_between'       => ':attribute muss zwischen :min und :max Zeichne lang sein.',
    'dimensions'           => ':attribute hat ungültige Bilddimensionen.',
    'distinct'             => ':attribute Feld hat einen doppelten Wert.',
    'email'                => ':attribute muss eine gültige E-Mailadresse sein.',
    'exists'               => 'ausgewähltes :attribute ist ungültig.',
    'file'                 => ':attribute muss eine Datei sein.',
    'filled'               => ':attribute Feld muss einen Wert haben.',
    'image'                => ':attribute muss ein Bild sein.',
    'in'                   => 'ausgewähltes :attribute ist ungültig.',
    'in_array'             => ':attribute Feld existiert nicht in :other.',
    'integer'              => ':attribute muss ein Ganzzahlwert sein.',
    'ip'                   => ':attribute muss eine gültige IP-Adresse sein.',
    'ipv4'                 => ':attribute muss eine gültige IPv4-Adresse sein.',
    'ipv6'                 => ':attribute muss eine gülitge IPv6-Adresse sein.',
    'json'                 => ':attribute muss ein gültiger JSON-String sein.',
    'max'                  => [
        'numeric' => ':attribute darf nicht größer sein als :max.',
        'file'    => ':attribute darf nicht größer sein als :max KB.',
        'string'  => ':attribute darf nicht länger sein als :max Zeichen.',
        'array'   => ':attribute darf nicht mehr als :max Einträge haben.',
    ],
    'mimes'                => ':attribute muss eine Datei des Typs :values sein.',
    'mimetypes'            => ':attribute muss eine Datei des Typs :values sein.',
    'min'                  => [
        'numeric' => ':attribute muss mindestens :min sein.',
        'file'    => ':attribute muss mindestens :min KB groß sein.',
        'string'  => ':attribute must länger als :min Zeichen sein.',
        'array'   => ':attribute muss mindestens :min Einträge haben.',
    ],
    'not_in'               => 'ausgewähltes :attribute ist ungültig.',
    'numeric'              => ':attribute muss eine Zahl sein.',
    'present'              => ':attribute Feld muss angegeben werden.',
    'regex'                => ':attribute Format ist ungültig.',
    'required'             => ':attribute Feld ist erforderlich.',
    'required_if'          => ':attribute Feld ist erforderlich wenn :other auf :value gesetzt ist.',
    'required_unless'      => ':attribute Feld ist erforderlich, es sei denn :other ist auf :values gesetzt.',
    'required_with'        => ':attribute Feld ist erforderlich, wenn :values vorhanden ist.',
    'required_with_all'    => ':attribute Feld ist erforderlich, wenn :values vorhanden ist.',
    'required_without'     => ':attribute Feld ist erforderlich, wenn :values nicht vorhanden ist.',
    'required_without_all' => ':attribute Feld ist erforderlich, wenn nichts von :values vorhanden ist.',
    'same'                 => ':attribute und :other müssen übereinstimmen.',
    'size'                 => [
        'numeric' => ':attribute muss :size groß sein.',
        'file'    => ':attribute muss :size KB groß sein.',
        'string'  => ':attribute muss :size Zeichen lang sein.',
        'array'   => ':attribute muss :size Einträge haben.',
    ],
    'string'               => ':attribute muss eine Zeichenkette sein.',
    'timezone'             => ':attribute muss eine gültige Zeitzone sein.',
    'unique'               => ':attribute ist bereits vorhanden.',
    'uploaded'             => ':attribute konnte nicht hochgeladen werden.',
    'url'                  => ':attribute Format ist ungültig.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
