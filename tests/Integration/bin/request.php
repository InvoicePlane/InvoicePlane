<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

// Signal to MY_Exceptions (and any other CI3 extension) that this PHP process
// is a test subprocess. Errors that would normally echo HTML and exit() will
// throw RuntimeException instead, making them visible as real PHPUnit failures.
define('CI_TEST_SUBPROCESS', true);

$encodedRequest = getenv('CI_TEST_REQUEST') ?: '';
$decodedRequest = base64_decode($encodedRequest, true);

if ($decodedRequest === false) {
    fwrite(STDERR, 'Invalid CI_TEST_REQUEST payload.' . PHP_EOL);
    exit(2);
}

$request = json_decode($decodedRequest, true);

if ( ! is_array($request)) {
    fwrite(STDERR, 'Unable to decode CI_TEST_REQUEST JSON payload.' . PHP_EOL);
    exit(3);
}

// Disable CSRF verification in test subprocesses — forms don't carry real tokens.
putenv('CSRF_PROTECTION=false');
$_ENV['CSRF_PROTECTION'] = 'false';

$method  = mb_strtoupper((string) ($request['method'] ?? 'GET'));
$uri     = '/' . mb_ltrim((string) ($request['uri'] ?? '/'), '/');
$query   = is_array($request['query'] ?? null) ? $request['query'] : [];
$post    = is_array($request['post'] ?? null) ? $request['post'] : [];
$session = is_array($request['session'] ?? null) ? $request['session'] : [];
$isAjax  = (bool) ($request['ajax'] ?? false);

if ($session !== []) {
    if (session_status() === PHP_SESSION_NONE) {
        session_id('citst' . mb_substr(md5((string) json_encode($session)), 0, 12));
        session_start();
    }

    $_SESSION = $session;
}

$queryString = http_build_query($query);
$requestUri  = '/index.php' . $uri . ($queryString !== '' ? '?' . $queryString : '');

$_GET     = $query;
$_POST    = $post;
$_COOKIE  = [];
$_FILES   = [];
$_REQUEST = $method === 'POST' ? array_merge($query, $post) : $query;

// CI3 detects CLI mode via is_cli() and uses $_SERVER['argv'] for URI routing.
// Set argv so _parse_argv() returns the correct URI path instead of defaulting to the
// default controller (dashboard).
$_SERVER['argv'] = [basename(__FILE__), mb_ltrim($uri, '/')];
$_SERVER['argc'] = 2;

$_SERVER['REQUEST_METHOD']     = $method;
$_SERVER['REQUEST_URI']        = $requestUri;
$_SERVER['QUERY_STRING']       = $queryString;
$_SERVER['SCRIPT_NAME']        = '/index.php';
$_SERVER['PHP_SELF']           = '/index.php' . $uri;
$_SERVER['PATH_INFO']          = $uri;
$_SERVER['SERVER_PROTOCOL']    = 'HTTP/1.1';
$_SERVER['HTTP_HOST']          = 'localhost';
$_SERVER['SERVER_NAME']        = 'localhost';
$_SERVER['SERVER_PORT']        = '80';
$_SERVER['HTTPS']              = 'off';
$_SERVER['REMOTE_ADDR']        = '127.0.0.1';
$_SERVER['HTTP_USER_AGENT']    = 'PHPUnit CI3 Integration Runner';
$_SERVER['HTTP_ACCEPT']        = $isAjax ? 'application/json' : 'text/html,application/xhtml+xml';

if ($isAjax) {
    $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
}
$_SERVER['DOCUMENT_ROOT']      = dirname(__DIR__, 3) . '/public';
$_SERVER['SCRIPT_FILENAME']    = dirname(__DIR__, 3) . '/public/index.php';
$_SERVER['REQUEST_TIME']       = time();
$_SERVER['REQUEST_TIME_FLOAT'] = microtime(true);

// Capture all application output via ob callback. PHP calls shutdown functions BEFORE
// flushing output buffers, so we must call ob_end_clean() inside the shutdown function
// to force-flush and populate $captured_output before reading it.
$captured_output = '';
ob_start(static function (string $chunk) use (&$captured_output): string {
    $captured_output .= $chunk;
    return '';
});

$ci_exception   = null;
$result_emitted = false;

register_shutdown_function(static function () use (&$result_emitted, &$captured_output, &$ci_exception): void {
    if ($result_emitted) {
        return;
    }

    // Flush all remaining output buffers down to the callback layer.
    // ob_end_flush() passes each buffer's content to the next level; when it reaches
    // the callback ob_start (level 1), the callback fires and populates $captured_output.
    // This correctly handles content written before exit() without double-capturing
    // anything that the callback already processed during normal CI3 output flushing.
    while (ob_get_level() > 0) {
        ob_end_flush();
    }

    $result = [
        'status'    => http_response_code() ?: 200,
        'headers'   => headers_list(),
        'output'    => $captured_output,
        'exception' => $ci_exception,
    ];

    $result_emitted = true;
    fwrite(STDOUT, '__CI_TEST_RESULT_START__');
    fwrite(STDOUT, base64_encode((string) json_encode($result, JSON_THROW_ON_ERROR)));
    fwrite(STDOUT, '__CI_TEST_RESULT_END__');
});

try {
    require dirname(__DIR__, 3) . '/public/index.php';
} catch (Throwable $throwable) {
    $ci_exception = $throwable::class . ': ' . $throwable->getMessage() . ' @ ' . $throwable->getFile() . ':' . $throwable->getLine();
}

// Normal (non-exit) code path: flush any remaining buffered content through the callback.
while (ob_get_level() > 0) {
    ob_end_flush();
}

if ( ! $result_emitted) {
    $result = [
        'status'    => http_response_code() ?: 200,
        'headers'   => headers_list(),
        'output'    => $captured_output,
        'exception' => $ci_exception,
    ];

    $result_emitted = true;
    fwrite(STDOUT, '__CI_TEST_RESULT_START__');
    fwrite(STDOUT, base64_encode((string) json_encode($result, JSON_THROW_ON_ERROR)));
    fwrite(STDOUT, '__CI_TEST_RESULT_END__');
}
