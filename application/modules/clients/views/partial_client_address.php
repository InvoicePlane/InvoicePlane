<span class="client-address-street-line">
    <?php echo $client->client_address_1 ? htmlsc($client->client_address_1) . '<br>' : ''; ?>
</span>
<span class="client-address-street-line">
    <?php echo $client->client_address_2 ? htmlsc($client->client_address_2) . '<br>' : ''; ?>
</span>
<span class="client-adress-town-line">
    <?php 
    // list of countries with ZIP + City order
    $zipBeforeCityCountries = ['AT', 'BE', 'CH', 'CZ', 'DE', 'DK', 'ES', 'FI', 'FR', 'IT', 'LU', 'NL', 'NO', 'PL', 'SE', 'SK'];
    
    // check default country, if empty use system default
    $country_to_check = $client->client_country ? $client->client_country : get_setting('default_country');
    // if the client country or (if empty) the (system) default country matches one of the "zipBeforeCityCountries" change the order to zip+city
    if (in_array($country_to_check, $zipBeforeCityCountries)): ?>
        <?php echo $client->client_zip ? htmlsc($client->client_zip) . ' ' : ''; ?>
        <?php echo $client->client_city ? htmlsc($client->client_city) . ' ' : ''; ?>
        <?php echo $client->client_state ? htmlsc($client->client_state) : ''; ?>
    <?php else: ?>
        <?php echo $client->client_city ? htmlsc($client->client_city) . ' ' : ''; ?>
        <?php echo $client->client_state ? htmlsc($client->client_state) . ' ' : ''; ?>
        <?php echo $client->client_zip ? htmlsc($client->client_zip) : ''; ?>
    <?php endif; ?>
</span>
<span class="client-adress-country-line">
    // get the default country code from the system settings and if the client country matches, hide the country name in the address
    <?php 
    $default_country = get_setting('default_country');

    if ($client->client_country && $client->client_country !== $default_country) {
        echo '<br>' . get_country_name(trans('cldr'), $client->client_country);
    } 
    ?>
</span>
