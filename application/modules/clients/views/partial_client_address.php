<span class="client-address-street-line">
    <?php echo $client->client_address_1 ? htmlsc($client->client_address_1) . '<br>' : ''; ?>
</span>
<span class="client-address-street-line">
    <?php echo $client->client_address_2 ? htmlsc($client->client_address_2) . '<br>' : ''; ?>
</span>
<span class="client-adress-town-line">
    <?php if (is_zip_before_city($client->client_country)): ?>
        <?php /* Format for most European countries: ZIP City */ ?>
        <?php echo $client->client_zip ? htmlsc($client->client_zip) . ' ' : ''; ?>
        <?php echo $client->client_city ? htmlsc($client->client_city) . ' ' : ''; ?>
    <?php else: ?>
        <?php /* International format: City ZIP */ ?>
        <?php echo $client->client_city ? htmlsc($client->client_city) . ' ' : ''; ?>
        <?php echo $client->client_zip ? htmlsc($client->client_zip) . ' ' : ''; ?>
    <?php endif; ?>
    
    <?php /* Always append state at the end if available */ ?>
    <?php echo $client->client_state ? htmlsc($client->client_state) : ''; ?>
</span>

<span class="client-adress-country-line">
    <?php 
    $default_country = get_setting('default_country');
    /* Only display the country name if it differs from the system's default country */
    if ($client->client_country && $client->client_country !== $default_country) {
        echo '<br>' . get_country_name(trans('cldr'), $client->client_country);
    } 
    ?>
</span>
