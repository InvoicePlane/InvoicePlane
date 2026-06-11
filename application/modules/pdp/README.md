# InvoicePlane PDP module

This module adds a PDP connector screen to InvoicePlane.

## Included backend

- SuperPDP

## Automatic backend discovery

Backends are discovered automatically from:

```text
application/modules/pdp/libraries/providers/*Provider.php
```

To add another backend, for example Seqino, add a file such as:

```text
application/modules/pdp/libraries/providers/SeqinoProvider.php
```

The class must implement `PdpProviderInterface`. The backend will then appear automatically in the PDP settings screen.

See `docs/BACKENDS.md` for the provider contract.
