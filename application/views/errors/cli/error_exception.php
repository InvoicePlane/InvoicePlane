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

echo "\n", error_trans('error_exception_encountered'), "\n\n";
echo error_trans('error_type'), ":        ", get_class($exception), "\n";
echo error_trans('error_message'), ":     ", $message, "\n";
echo error_trans('error_filename'), ":    ", $exception->getFile(), "\n";
echo error_trans('error_line_number'), ": ", $exception->getLine();

if (defined('SHOW_DEBUG_BACKTRACE') && SHOW_DEBUG_BACKTRACE) : ?>

    <?php echo error_trans('error_backtrace'); ?>:
    <?php foreach ($exception->getTrace() as $error) : ?>
        <?php if (isset($error['file']) && ! str_starts_with($error['file'], realpath(BASEPATH))) : ?>
            <?php echo error_trans('error_file'); ?>: <?php echo $error['file'], "\n"; ?>
            <?php echo error_trans('error_line'); ?>: <?php echo $error['line'], "\n"; ?>
            <?php echo error_trans('error_function'); ?>: <?php echo $error['function'], "\n\n"; ?>
        <?php endif ?>
    <?php endforeach ?>

<?php endif;
