<?php defined('BASEPATH') || exit('No direct script access allowed'); ?>

A PHP Error was encountered

Severity:    <?php echo $severity, "\n"; ?>
Message:     <?php echo $message, "\n"; ?>
Filename:    <?php echo $filepath, "\n"; ?>
Line Number: <?php echo $line; ?>

<?php if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE) : ?>
    Backtrace:
    <?php foreach (debug_backtrace() as $error) : ?>
        <?php if (isset($error['file']) && ! str_starts_with($error['file'], realpath(BASEPATH))) : ?>
            File: <?php echo $error['file'], "\n"; ?>
            Line: <?php echo $error['line'], "\n"; ?>
            Function: <?php echo $error['function'], "\n\n"; ?>
        <?php endif ?>
    <?php endforeach ?>

<?php endif;
