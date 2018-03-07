<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="/assets/core/core.css">
    <script src="/assets/core/dependencies.js"></script>
</head>
<body>

<div class="app">
    <?php echo $content; ?>
</div>

<?php if (isset($footer)) : ?>
    <footer>
        <?php echo $footer; ?>
    </footer>
<?php endif; ?>

</body>
</html>
