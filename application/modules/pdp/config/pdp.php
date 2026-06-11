<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| InvoicePlane PDP / Plateforme Agreee module configuration
|--------------------------------------------------------------------------
| PDP providers are discovered automatically from:
| application/modules/pdp/libraries/providers/*Provider.php
|
| A provider must implement PdpProviderInterface. Its code is inferred from
| the class name by removing the Provider suffix, for example:
| SuperPdpProvider => superpdp
| SeqinoProvider   => seqino
*/
$config['pdp_default_provider'] = 'superpdp';
$config['pdp_facturx_format'] = 'Facturxv10';
$config['pdp_facturx_profile'] = 'extended';
