# PDP backend auto-discovery

The PDP module discovers available providers automatically from:

```text
application/modules/pdp/libraries/providers/*Provider.php
```

A backend must:

1. use a class name ending with `Provider`, for example `SeqinoProvider`;
2. implement `PdpProviderInterface`;
3. be placed in `libraries/providers/`.

The provider code is inferred from the class name:

```text
SuperPdpProvider => superpdp
SeqinoProvider   => seqino
```

Optionally, the class can expose metadata:

```php
public static function providerCode(): string
{
    return 'seqino';
}

public static function providerName(): string
{
    return 'Seqino';
}
```

After adding a new backend file, reload the PDP settings page. The backend appears automatically in the provider select list.
