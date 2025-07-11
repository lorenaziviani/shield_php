<?php

require_once __DIR__ . '/../vendor/autoload.php';

use ShieldPHP\Metrics;

$redis = new Redis();
$redis->connect('redis', 6379);
$metrics = new Metrics($redis);

header('Content-Type: text/plain');


echo "shieldphp_blocked_requests_total {$metrics->getBlockedRequests()}\n\n";
echo "shieldphp_blocked_requests_rate_limit {$metrics->getBlockedRequestsByReason('rate_limit')}\n\n";
echo "shieldphp_blocked_requests_waf {$metrics->getBlockedRequestsByReason('waf')}\n\n";
echo "shieldphp_blocked_ips_total " . count($metrics->getBlockedIPs()) . "\n\n";
