<?php

    $tax_rates_array = array();
    foreach ($items as $item) {
        if($item->item_tax_total > 0){
            if(!isset($tax_rates_array[$item->item_tax_rate_id])){
                $tax_rates_array[$item->item_tax_rate_id] = new stdClass;
                $tax_rates_array[$item->item_tax_rate_id]->percent = 0;
                $tax_rates_array[$item->item_tax_rate_id]->total = 0;
                $tax_rates_array[$item->item_tax_rate_id]->percent = $item->item_tax_rate_percent;
            }
            
            $tax_rates_array[$item->item_tax_rate_id]->total += $item->item_tax_total;
        }
    }

?>
<html lang="<?php echo trans('cldr'); ?>">
<head>
    <meta charset="utf-8">
    <title><?php echo trans('invoice'); ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/templates.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/thermoclima-invoice-pdf.css">
</head>
<body>
<header class="clearfix">

    <div id="logo">
        <?php echo invoice_logo_pdf(); ?>
    </div>

    
    <div id="company">
        <div><b><span class="logo-red">THERMOCLIMA S.N.C.</span> di <span class="logo-blue">PESSOTTO GIUSEPPE & C.</span></b></div>
        <div><span class="logo-red">RISCALDAMENTO</span> – <span class="logo-blue">CONDIZIONAMENTO</span></div>
        <div><span class="small-text">Sede Legale: </span><span class="logo-red">Via Repubblica 25/3</span><span class="logo-blue"> 31020 SAN POLO DI PIAVE (TV)</span></div>
        <div><span class="small-text">Sede Operativa: </span><span class="logo-red">VIA LIBERAZIO</span><span class="logo-blue">NE 100 - 31028 VAZZOLA (TV)</span></div>
        <div><span class="logo-red">Reg. Impr. di TV / C</span><span class="logo-blue">.F.e P.I. 02489580262</span></div>
        <div><span class="logo-red">email: info@the</span><span class="logo-blue">rmoclimacaldaie.it</span></div>
        <div><span class="logo-red">www.thermo</span><span class="logo-blue">climacaldaie.it</span></div>

    </div>

    <div class="invoice-details clearfix">
        <table>
            <tr>
                <td>Fattura N°</td>
                <td style="text-align: center;"><strong><?php echo $invoice->invoice_number; ?></strong></td>
            </tr>
            <tr>
                <td>Data</td>
                <td><strong><?php echo date_from_mysql($invoice->invoice_date_created, true); ?></strong></td>
            </tr>
        </table>
    </div>
    
    <div class="clearfix"></div>
    <br>
    <br>
    <div id="client">
        <span class="th-small-title">Spett.le ditta</span>
        <div>
            <b><?php echo $invoice->client_name; ?></b>
        </div>
        <?php if ($invoice->client_vat_id) {
            echo '<div>' . trans('vat_id_short') . ': ' . $invoice->client_vat_id . '</div>';
        }
        if ($invoice->client_tax_code) {
            echo '<div>' . trans('tax_code_short') . ': ' . $invoice->client_tax_code . '</div>';
        }
        if ($invoice->client_address_1) {
            echo '<div>' . $invoice->client_address_1 . '</div>';
        }
        if ($invoice->client_address_2) {
            echo '<div>' . $invoice->client_address_2 . '</div>';
        }
        if ($invoice->client_city && $invoice->client_zip) {
            echo '<div>' . $invoice->client_city . ' ' . $invoice->client_zip . '</div>';
        } else {
            if ($invoice->client_city) {
                echo '<div>' . $invoice->client_city . '</div>';
            }
            if ($invoice->client_zip) {
                echo '<div>' . $invoice->client_zip . '</div>';
            }
        }
        if ($invoice->client_state) {
            echo '<div>' . $invoice->client_state . '</div>';
        }
        if ($invoice->client_country) {
            echo '<div>' . get_country_name(trans('cldr'), $invoice->client_country) . '</div>';
        }

        echo '<br/>';

        if ($invoice->client_phone) {
            echo '<div>' . trans('phone_abbr') . ': ' . $invoice->client_phone . '</div>';
        } ?>

    </div>

    <div class="clearfix"></div>
    <br/>

    <div id="th-payment-details">
        <div class="row clearfix">
            <div class="label-inline">Pagamento</div>
            <div class="content-after-label">Bonifico Bancario</div>
        </div>

        <div class="row clearfix">
            <div class="label-inline">Banca</div>
            <div class="content-after-label">VENETO BANCA HOLDING - AGENZIA di CIMADOLMO</div>
        </div>

        <div class="row clearfix">
            <div class="label-inline">Codice IBAN</div>
            <div class="content-after-label"><strong>IT30K0503562230019570022809</strong></div>
        </div>
    </div>

</header>

<main>

    <table class="item-table">
        <thead>
        <tr>
            <th class="item-desc"><?php echo trans('description'); ?></th>
            <th class="item-amount text-right"><?php echo trans('qty'); ?></th>
            
            <th class="item-price text-right"><?php echo 'Importo' ?></th>
            <th class="item-total text-right"><?php echo trans('tax_rate'); ?></th>
        </tr>
        </thead>
        <tbody>

        <?php
        foreach ($items as $item) { ?>
            <tr>
                <td><?php echo $item->item_price <= 0 ? "<!--<strong>-->" : null; ?><?php echo nl2br($item->item_description); ?><?php echo $item->item_price <= 0 ? "<!--</strong>-->" : null; ?></td>
                <td class="text-right">
                    <?php echo $item->item_price > 0 ? format_amount($item->item_quantity) : null;?>
                </td>
                <td class="text-right">
                    <?php echo $item->item_price > 0 ? format_currency($item->item_price) : null; ?>
                </td>
                <td class="text-right">
                    <?php echo $item->item_price > 0 ? ($item->item_tax_rate_percent != NULL ? format_amount($item->item_tax_rate_percent) : '0') . '%' : null; ?>
                </td>
            </tr>
        <?php } ?>

        </tbody>
        
    </table>

</main>

<footer>
    
    <div clasS="box-info">
        <p><?php 
        if(empty($invoice->invoice_custom_non_mostrare_frase_reverse_charge)){
            echo $invoice->user_custom_reverse_charge;
        }
        echo "</p><br/><br/><p>";
        if(!empty($invoice->invoice_custom_riga_aggiuntiva)){
            echo $invoice->invoice_custom_riga_aggiuntiva;
        }
        ?></p>
    </div>

    <div class="box-totale">
        <table style="float:right">
            <tr>
                <td>
                    Imponibile:
                </td>
                <td class="txt-right">
                    <?php echo format_currency($invoice->invoice_item_subtotal); ?>
                </td>
            </tr>
            <?php
            foreach($tax_rates_array as $tax){
                echo "<tr><td>Imposta " . format_amount($tax->percent) . '%</td><td class="txt-right">' . format_currency($tax->total) . '</td></tr>';
            }
            ?>
            <tr>
                <td>
                    <strong>Totale Fattura:</strong>
                </td>
                <td class="txt-right">
                    <strong><?php echo format_currency($invoice->invoice_total); ?></strong>
                </td>
            </tr>
        </table>

    </div>
</footer>
</body>
</html>