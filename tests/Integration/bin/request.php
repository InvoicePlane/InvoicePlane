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

if ($session !== []) {
    if (session_status() === PHP_SESSION_NONE) {
        session_id('ci_test_' . mb_substr(md5((string) json_encode($session)), 0, 12));
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

ob_start();
$exception = null;

try {
    require dirname(__DIR__, 3) . '/public/index.php';
} catch (Throwable $throwable) {
    $exception = $throwable::class . ': ' . $throwable->getMessage() . ' @ ' . $throwable->getFile() . ':' . $throwable->getLine();
}

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
