<?php

require_once __DIR__ . '/../vendor/autoload.php';

use ShieldPHP\RateLimiter;
use ShieldPHP\WAF;
use ShieldPHP\Metrics;

$redis = new Redis();
$redis->connect('redis', 6379);

$metrics = new Metrics($redis);
$rateLimiter = new RateLimiter($redis, 2, 60); // 2 requisições por IP por minuto
$waf = new WAF();

$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$endpoint = '/teste';
$requestData = array_merge($_GET, $_POST);

if (!$rateLimiter->allow($ip, $endpoint)) {
    $metrics->incrementBlockedRequest('rate_limit', $ip);
    http_response_code(429);
    echo 'Too Many Requests';
    exit;
}
if ($waf->isMalicious($requestData)) {
    $metrics->incrementBlockedRequest('waf', $ip);
    http_response_code(403);
    echo 'Forbidden: Malicious request detected.';
    exit;
}
echo 'OK';
