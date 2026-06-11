# Ajouter l'entree de menu InvoicePlane

Le module est accessible directement par :

- `index.php/pdp`
- `index.php/pdp/settings`

Pour afficher une entree dans le menu InvoicePlane, il faut modifier le fichier de navigation de ton installation. Selon les versions/themes InvoicePlane, il se trouve generalement dans un de ces chemins :

- `application/modules/layout/views/includes/navbar.php`
- `application/modules/layout/views/includes/sidebar.php`
- `application/modules/layout/views/includes/navigation.php`
- `application/views/includes/navbar.php`

Ajoute ce bloc dans la liste des menus, idealement pres des factures :

```php
<li class="<?php echo uri_string() == 'pdp' || strpos(uri_string(), 'pdp/') === 0 ? 'active' : ''; ?>">
    <a href="<?php echo site_url('pdp'); ?>">
        <i class="fa fa-exchange"></i>
        <span>Facturation electronique</span>
    </a>
</li>
```

Si ton menu utilise des entrees de type dropdown, utilise plutot :

```php
<li>
    <a href="<?php echo site_url('pdp'); ?>">
        <i class="fa fa-exchange"></i> Facturation electronique
    </a>
</li>
```

## Bouton sur une facture

Pour ajouter un bouton sur la page de detail d'une facture, ajoute pres des autres boutons d'action :

```php
<a class="btn btn-default" href="<?php echo site_url('pdp/send/' . $invoice->invoice_id); ?>">
    <i class="fa fa-paper-plane"></i> Transmettre PA/PDP
</a>
```

Si la variable est un tableau :

```php
<a class="btn btn-default" href="<?php echo site_url('pdp/send/' . $invoice['invoice_id']); ?>">
    <i class="fa fa-paper-plane"></i> Transmettre PA/PDP
</a>
```
