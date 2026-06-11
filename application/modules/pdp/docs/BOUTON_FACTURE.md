# Ajouter le bouton "Transmettre PA/PDP" sur une facture

Dans InvoicePlane 1.7.1, le fichier est generalement :

```text
application/modules/invoices/views/view.php
```

Cherche le bloc des boutons d'action de la facture, puis ajoute :

```php
<a class="btn btn-default" href="<?php echo site_url('pdp/invoice/' . $invoice_id); ?>">
    <i class="fa fa-paper-plane"></i> Transmettre PA/PDP
</a>
```

Si ta vue utilise `$invoice->invoice_id` au lieu de `$invoice_id`, utilise :

```php
<a class="btn btn-default" href="<?php echo site_url('pdp/invoice/' . $invoice->invoice_id); ?>">
    <i class="fa fa-paper-plane"></i> Transmettre PA/PDP
</a>
```

Le bouton ouvre une page de controle :

```text
index.php/pdp/invoice/{invoice_id}
```

Depuis cette page, tu peux :

- voir si le PDF Factur-X est trouve ;
- transmettre le PDF a la PA/PDP ;
- verifier le dernier statut.

## Test direct sans bouton

Tu peux aussi tester directement :

```text
index.php/pdp/invoice/2508
index.php/pdp/send/2508
index.php/pdp/status/2508
```

Remplace `2508` par l'ID de ta facture.
