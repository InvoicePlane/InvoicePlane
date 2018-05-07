<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="<?php echo base_url('assets/core/core.css'); ?>">
    <script src="<?php echo base_url('assets/core/dependencies.js'); ?>"></script>
</head>
<body>

<div class="setup container">
    <div class="row justify-content-center">
        <div class="setup-wrapper col-md-8 col-lg-6">

            <h1 class="ip-logo text-center mt-5">
                <img src="<?php echo base_url('assets/ip_logo.svg'); ?>" alt="InvoicePlane" width="300px">
            </h1>

            <div class="card-wrapper mt-5 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <?php echo $content; ?>
                    </div>
                </div>
            </div>

            <?php if (isset($progress)) : ?>
                <div class="progress mb-5" style="height: 5px;">
                    <div class="progress-bar" role="progressbar" style="width: <?php echo $progress; ?>%"
                         aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

</body>
</html>
