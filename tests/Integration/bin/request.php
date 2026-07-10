<?php

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

$method  = mb_strtoupper((string) ($request['method'] ?? 'GET'));
$uri     = '/' . ltrim((string) ($request['uri'] ?? '/'), '/');
$query   = is_array($request['query'] ?? null) ? $request['query'] : [];
$post    = is_array($request['post'] ?? null) ? $request['post'] : [];
$session = is_array($request['session'] ?? null) ? $request['session'] : [];
$isAjax  = ! empty($request['ajax']);

// Ajax controllers (Base_Controller::$ajax_controller) show_404() unless the
// request looks like an XHR, so advertise it when the test asked for ajax().
if ($isAjax) {
    $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
}

if ($session !== []) {
    if (session_status() === PHP_SESSION_NONE) {
        session_id('citest' . mb_substr(md5((string) json_encode($session)), 0, 12));
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
$_SERVER['HTTP_ACCEPT']        = 'text/html,application/xhtml+xml';
$_SERVER['DOCUMENT_ROOT']      = dirname(__DIR__, 3) . '/public';
$_SERVER['SCRIPT_FILENAME']    = dirname(__DIR__, 3) . '/public/index.php';
$_SERVER['REQUEST_TIME']       = time();
$_SERVER['REQUEST_TIME_FLOAT'] = microtime(true);

// CI3 uses is_cli() to decide whether to parse $argv instead of REQUEST_URI.
// In CLI mode (PHP_SAPI=cli) it would ignore REQUEST_URI and return an empty
// route, defaulting to the dashboard controller.  Override is_cli() here so
// the URI class uses REQUEST_URI, matching a real web-server request.
if ( ! function_exists('is_cli')) {
    function is_cli(): bool
    {
        return false;
    }
}

ob_start();
$exception = null;

// CI's redirect() calls exit(), which prevents normal execution flow after require.
// We capture the result in a shutdown function so it's always emitted, even when exit() is called.
register_shutdown_function(static function () use (&$exception): void {
    // Avoid double-output if the try/catch path already ran.
    if (defined('CI_TEST_RESULT_SENT')) {
        return;
    }

    $output = ob_get_clean() ?: '';

    $result = [
        'status'    => http_response_code() ?: 200,
        'headers'   => headers_list(),
        'output'    => $output,
        'exception' => $exception,
    ];

    echo '__CI_TEST_RESULT_START__';
    echo base64_encode((string) json_encode($result, JSON_THROW_ON_ERROR));
    echo '__CI_TEST_RESULT_END__';
});

try {
    require dirname(__DIR__, 3) . '/public/index.php';
} catch (Throwable $throwable) {
    // MY_Exceptions turns show_error()/show_404() into a RuntimeException carrying
    // the HTTP status code (in the testing environment). A 4xx is a normal
    // application response (e.g. a 404), not a crash, so surface it as such and
    // let the test assert on the status code instead of catching a fatal.
    $code = $throwable->getCode();
    if ($throwable instanceof RuntimeException && $code >= 400 && $code < 500) {
        http_response_code($code);
        // Emit a minimal body carrying the status reason phrase so tests can
        // assert on it (MY_Exceptions throws before CI can render its error view).
        $phrases = [400 => 'Bad Request', 401 => 'Unauthorized', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 422 => 'Unprocessable Entity', 429 => 'Too Many Requests'];
        echo $code . ' ' . ($phrases[$code] ?? 'Client Error') . ': ' . $throwable->getMessage();
    } else {
        $exception = $throwable::class . ': ' . $throwable->getMessage() . ' @ ' . $throwable->getFile() . ':' . $throwable->getLine();
    }
}

define('CI_TEST_RESULT_SENT', true);
$output = ob_get_clean();

$result = [
    'status'    => http_response_code() ?: 200,
    'headers'   => headers_list(),
    'output'    => $output,
    'exception' => $exception,
];

echo '__CI_TEST_RESULT_START__';
echo base64_encode((string) json_encode($result, JSON_THROW_ON_ERROR));
echo '__CI_TEST_RESULT_END__';

exit($exception === null ? 0 : 1);
