<html>
<head>
    <title><?php echo lang('sales_by_date'); ?></title>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/default/css/reports.css" type="text/css">

</head>

<body>
<h3 class="report_title"><?php echo lang('sales_by_date'); ?></h3>

<table>

    <tr>
        <th style="width:15%;text-align:center;border-bottom: none;"> <?php echo lang('vat_id'); ?> </th>
        <th style="width:50%;text-align:center;border-bottom: none;"> <?php echo lang('name'); ?> </th>
        <th style="width:15%;text-align:center;border-bottom: none;"> <?php echo lang('period'); ?> </th>
        <th style="width:20%;text-align:center;border-bottom: none;"> <?php echo lang('quantity'); ?> </th>
    </tr>

    <tr>
        <td colspan="4" style="border-bottom: none;">
            <hr/>
        </td>
    </tr>

    <?php

    $initial_year = 0;
    $final_year = 0;
    $numYears = 1;
    $numRows = 1;
    $contRows = 0;
    $contYears = 0;
    $pattern = '/^payment_*/i';

    foreach ($results as $result) {

        if ($final_year == 0) {

            foreach ($result as $index => $value) {

                if ($initial_year == 0) {
                    $initial_year = intval(substr($index, 11, 4));
                }
                $aux = intval(substr($index, 11, 4));
                if ($aux > $final_year) {
                    $final_year = $aux;
                }

            }
        }

        if ($contYears == 0 && ($final_year - $initial_year) > 0) {
            $numYears = $final_year - $initial_year + 1;
            $contYears = 1;
        }
        if ($contRows == 0) {
            $numRows = $numRows + ($numYears * 4);
            $contRows = 1;
        }

        ?>

        <tr>
            <td style="border-bottom: none;text-align:center;"> <?php echo $result->VAT_ID; ?> </td>
            <td style="border-bottom: none;text-align:center;" rowspan="<?php echo $numRows; ?>"
                valign="top"> <?php echo $result->Name; ?> </td>
            <td style="border-bottom: none;text-align:center;"> <?php echo lang('annual'); ?> </td>
            <td style="border-bottom: none;text-align:center;"> <?php echo format_currency($result->total_payment); ?> </td>
        </tr>

        <?php

        foreach ($result as $index => $value) {

            $quarter = substr($index, 8, 2);
            $year = substr($index, 11, 4);

            if (preg_match($pattern, $index)) {
                ?>

                <tr>
                    <td style="border-bottom: none;">&nbsp;</td>
                    <td style="border-bottom: none;text-align:center;"><?php
                        if ($quarter == "t1") {
                            echo lang('Q1') . "/" . $year;
                        } else {
                            if ($quarter == "t2") {
                                echo lang('Q2') . "/" . $year;
                            } else {
                                if ($quarter == "t3") {
                                    echo lang('Q3') . "/" . $year;
                                } else {
                                    if ($quarter == "t4") {
                                        echo lang('Q4') . "/" . $year;
                                    }
                                }
                            }
                        }
                        ?></td>
                    <td style="border-bottom: none;text-align:center;"><?php if ($value > 0) {
                            echo format_currency($value);
                        } ?></td>
                </tr>

                <?php
            }
        }
        ?>
        <tr>
            <td colspan="4" style="border-bottom: none;">
                <hr/>
            </td>
        </tr>

        <?php
    }
    ?>

</table>
</body>
</html>
