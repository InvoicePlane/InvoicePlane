<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>404 Page Not Found</title>
    <link rel="stylesheet"
          href="/assets/invoiceplane/css/style.css">
</head>
<body class="error">
<div id="ip-logo">
  <img src="/assets/core/img/logo_400x200.png"/>
</div>

<div class="error-container">
  <h1><?php echo $heading; ?></h1>
  <p class="error-text"><?php echo $message; ?></p>
</div>
</body>
</html>
