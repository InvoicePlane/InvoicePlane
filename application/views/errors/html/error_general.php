<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html style="display:table;width:100%;">
<head>
    <meta charset="utf-8">
    <title>InvoicePlane - <?php echo $heading; ?></title>
    <style>
        html,
        html * {
            box-sizing: border-box;
        }

        body {
            font-family: sans-serif;
            background: #B94A48;
            color: #fff;
            height: 100vh;
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            padding: 2vh 2vw;
        }

        h4 {
            font-size: 30px;
            text-align: center;
            width: 100%;
        }

        p {
            font-size: 16px;
            width: 100%;
        }
    </style>
</head>
<body>
<h4><?php echo $heading; ?></h4>
<p><?php echo $message; ?></p>
</body>
</html>
