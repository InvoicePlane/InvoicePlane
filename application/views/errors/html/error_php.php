<?php
defined('BASEPATH') || exit('No direct script access allowed');

// Simple translation function for error pages
function error_trans($key) {
    static $translations = null;
    if ($translations === null) {
        $lang_file = APPPATH . 'language/english/ip_lang.php';
        if (file_exists($lang_file)) {
            include $lang_file;
            if (isset($lang) && is_array($lang)) {
                $translations = $lang;
            }
        }
        if ($translations === null) {
            $translations = [];
        }
    }
    return isset($translations[$key]) ? $translations[$key] : $key;
}
?>

<div style="border:1px solid #990000;padding-left:20px;margin:0 0 10px 0;">

    <h4><?php echo error_trans('error_php_encountered'); ?></h4>

    <p><?php echo error_trans('error_severity'); ?>: <?php echo $severity; ?></p>
    <p><?php echo error_trans('error_message'); ?>: <?php echo $message; ?></p>
    <p><?php echo error_trans('error_filename'); ?>: <?php echo $filepath; ?></p>
    <p><?php echo error_trans('error_line_number'); ?>: <?php echo $line; ?></p>
<?php
if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE) {
?>
    <p><?php echo error_trans('error_backtrace'); ?>:</p>
<?php
    foreach (debug_backtrace() as $error) {
        if (isset($error['file']) && ! str_starts_with($error['file'], realpath(BASEPATH))) {
?>
    <p style="margin-left:10px">
        <?php echo error_trans('error_file'); ?>: <?php echo $error['file'] ?><br/>
        <?php echo error_trans('error_line'); ?>: <?php echo $error['line'] ?><br/>
        <?php echo error_trans('error_function'); ?>: <?php echo $error['function'] ?>
    </p>

<?php
        }
    }
}
?>

</div>
