<?php echo($client->client_address_1 ? htmlsc($client->client_address_1) . '<br>' : ''); ?>
<?php echo($client->client_address_2 ? htmlsc($client->client_address_2) . '<br>' : ''); ?>
<?php echo($client->client_city ? htmlsc($client->client_city) : ''); ?>
<?php echo($client->client_state ? htmlsc($client->client_state) : ''); ?>
<?php echo($client->client_zip ? htmlsc($client->client_zip) : ''); ?>
<?php echo($client->client_country ? '<br>' . htmlsc($client->client_country) : ''); ?>
