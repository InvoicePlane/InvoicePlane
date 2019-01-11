<?php
require_once('../fatturapa.php');
$fatturapa = new FatturaPA();
$schema = $fatturapa->get_schema();
echo '<pre>';
print_r($schema);
echo '</pre>';