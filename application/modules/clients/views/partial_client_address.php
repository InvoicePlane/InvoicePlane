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

<!-- -->
<hr />
<?php _trans('billing_address'); ?>:<br />
<span class="billing-address-name-line">
    <?php echo($client->billing_address_name ? htmlsc($client->billing_address_name) . '<br>' : ''); ?>
</span>
<span class="billing-address-street-line">
    <?php echo($client->billing_address_1 ? htmlsc($client->billing_address_1) . '<br>' : ''); ?>
</span>
<span class="billing-address-street-line">
    <?php echo($client->billing_address_2 ? htmlsc($client->billing_address_2) . '<br>' : ''); ?>
</span>
<span class="billing-adress-town-line">
    <?php echo($client->billing_city ? htmlsc($client->billing_city) . ' ' : ''); ?>
    <?php echo($client->billing_state ? htmlsc($client->billing_state) . ' ' : ''); ?>
    <?php echo($client->billing_zip ? htmlsc($client->billing_zip) : ''); ?>
</span>
<span class="billing-adress-country-line">
    <?php echo($client->billing_country ? '<br>' . get_country_name(trans('cldr'), $client->billing_country) : ''); ?>
</span>

<!-- -->
<hr />
<?php _trans('delivery_address'); ?>:<br />
<span class="delivery-address-name-line">
    <?php echo($client->delivery_address_name ? htmlsc($client->delivery_address_name) . '<br>' : ''); ?>
</span>
<span class="delivery-address-street-line">
    <?php echo($client->delivery_address_1 ? htmlsc($client->delivery_address_1) . '<br>' : ''); ?>
</span>
<span class="delivery-address-street-line">
    <?php echo($client->delivery_address_2 ? htmlsc($client->delivery_address_2) . '<br>' : ''); ?>
</span>
<span class="delivery-adress-town-line">
    <?php echo($client->delivery_city ? htmlsc($client->delivery_city) . ' ' : ''); ?>
    <?php echo($client->delivery_state ? htmlsc($client->delivery_state) . ' ' : ''); ?>
    <?php echo($client->delivery_zip ? htmlsc($client->delivery_zip) : ''); ?>
</span>
<span class="delivery-adress-country-line">
    <?php echo($client->delivery_country ? '<br>' . get_country_name(trans('cldr'), $client->delivery_country) : ''); ?>
</span>

