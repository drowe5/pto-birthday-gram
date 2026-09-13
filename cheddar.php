<?php
/*
 * A read-only pass-through to the Cheddar Up API.
 *
 * The browser cannot call api.cheddarup.com itself: the response carries no
 * CORS header for your domain, and a key placed in page JavaScript is readable
 * by anyone who opens the page. So the page calls this file on your own domain
 * instead, and this file -- which the browser never sees the source of --
 * attaches the key and forwards the request.
 *
 * Only GET is forwarded, and only to the host named in config.php, so the worst
 * this can do is read.
 */

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function fail($status, $error, $detail = null) {
    http_response_code($status);
    $out = array('error' => $error);
    if ($detail !== null) { $out['detail'] = $detail; }
    echo json_encode($out);
    exit;
}

$configFile = __DIR__ . '/config.php';
if (!is_file($configFile)) {
    fail(500, 'no_config', 'config.php is missing. Copy config.sample.php to config.php and put your key in it.');
}
$config = require $configFile;
if (!is_array($config)) {
    fail(500, 'bad_config', 'config.php must end with a return array(...);');
}

$key     = isset($config['api_key']) ? trim($config['api_key']) : '';
$style   = isset($config['auth_style']) ? strtolower(trim($config['auth_style'])) : 'bearer';
$base    = isset($config['base_url']) ? rtrim(trim($config['base_url']), '/') : 'https://api.cheddarup.com';
$secret  = isset($config['access_token']) ? (string) $config['access_token'] : '';
$timeout = isset($config['timeout']) ? (int) $config['timeout'] : 20;

/* Setup check. Reports what is wrong without ever echoing the key itself, so
   it is safe to open in a browser while getting the upload working. */
if (isset($_GET['probe'])) {
    echo json_encode(array(
        'ok'          => true,
        'php'         => PHP_VERSION,
        'curl'        => function_exists('curl_init'),
        'key_set'     => $key !== '' && strpos($key, 'PASTE-YOUR') === false,
        'key_tail'    => $key === '' ? '' : ('...' . substr($key, -4)),
        'auth_style'  => $style,
        'base_url'    => $base,
        'token_required' => $secret !== '',
    ));
    exit;
}

if ($secret !== '') {
    $given = isset($_GET['token']) ? (string) $_GET['token'] : '';
    if (!hash_equals($secret, $given)) {
        fail(401, 'bad_token', 'The access password in the page does not match the one in config.php.');
    }
}
if ($key === '' || strpos($key, 'PASTE-YOUR') !== false) {
    fail(500, 'no_key', 'config.php has no api_key set yet.');
}
if (!function_exists('curl_init')) {
    fail(500, 'no_curl', 'This server has no PHP cURL extension, so it cannot reach Cheddar Up.');
}

$path = isset($_GET['path']) ? (string) $_GET['path'] : '';
if ($path === '') { $path = '/'; }
if ($path[0] !== '/') { $path = '/' . $path; }
/* Anything that could walk off the configured host, or smuggle in a second
   URL, is refused rather than cleaned up. */
if (strpos($path, '..') !== false || strpos($path, '\\') !== false
    || strpos($path, '//') !== false || !preg_match('#^/[A-Za-z0-9/_\-.~%]*$#', $path)) {
    fail(400, 'bad_path', 'A path may only contain letters, digits and / _ - . ~ %');
}

/* Everything else on the query string rides along, so the page can pass
   page numbers and filters through without this file knowing about them. */
$extra = $_GET;
unset($extra['path'], $extra['token'], $extra['probe'], $extra['_']);

$headers = array('Accept: application/json');
$userpwd = null;
switch ($style) {
    case 'token':     $headers[] = 'Authorization: Token ' . $key; break;
    case 'x-api-key': $headers[] = 'X-Api-Key: ' . $key; break;
    case 'basic':     $userpwd = $key . ':'; break;
    case 'query':     $extra['api_key'] = $key; break;
    default:          $headers[] = 'Authorization: Bearer ' . $key; break;
}

$url = $base . $path;
if (!empty($extra)) {
    $url .= (strpos($url, '?') === false ? '?' : '&') . http_build_query($extra);
}

$ch = curl_init($url);
curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => $headers,
    CURLOPT_TIMEOUT        => $timeout > 0 ? $timeout : 20,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_FOLLOWLOCATION => false,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
    CURLOPT_USERAGENT      => 'birthday-grams/1.0',
));
if ($userpwd !== null) {
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, $userpwd);
}

$body   = curl_exec($ch);
$status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
$type   = (string) curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
$cerr   = curl_error($ch);
curl_close($ch);

if ($body === false) {
    fail(502, 'upstream_unreachable', $cerr !== '' ? $cerr : 'No response from ' . $base);
}

http_response_code($status === 0 ? 502 : $status);
/* A non-JSON body (an HTML error page, a login redirect) is wrapped rather
   than passed through, so the page always gets JSON back and can say what
   happened instead of failing to parse. */
if (stripos($type, 'json') === false) {
    echo json_encode(array(
        'error'         => 'not_json',
        'upstream_status' => $status,
        'content_type'  => $type,
        'body'          => substr($body, 0, 4000),
    ));
    exit;
}
echo $body;
