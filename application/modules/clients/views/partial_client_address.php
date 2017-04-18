<?php $this->load->helper('country'); ?>

<span class="client-address-street-line">
    <?php echo($client->client_address_1 ? htmlsc($client->client_address_1) . '<br>' : ''); ?>
</span>
<span class="client-address-street-line">
    <?php echo($client->client_address_2 ? htmlsc($client->client_address_2) . '<br>' : ''); ?>
</span>
<span class="client-adress-town-line">
    <?php echo($client->client_city ? htmlsc($client->client_city) . ' ' : ''); ?>
    <?php echo($client->client_state ? htmlsc($client->client_state) . ' ' : ''); ?>
    <?php echo($client->client_zip ? htmlsc($client->client_zip) : ''); ?>
</span>
<span class="client-adress-country-line">
    <?php echo($client->client_country ? '<br>' . get_country_name(trans('cldr'), $client->client_country) : ''); ?>
</span>
