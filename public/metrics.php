<?php
require_once __DIR__ . '/../vendor/autoload.php';

use ShieldPHP\Metrics;

$redis = new Redis();
$redis->connect('redis', 6379); // ajuste se necessário
$metrics = new Metrics($redis);

header('Content-Type: text/plain');

echo "# HELP shieldphp_blocked_requests_total Total blocked requests\n";
echo "# TYPE shieldphp_blocked_requests_total counter\n";
echo "shieldphp_blocked_requests_total {$metrics->getBlockedRequests()}\n\n";

echo "# HELP shieldphp_blocked_requests_rate_limit Blocked requests by rate limit\n";
echo "# TYPE shieldphp_blocked_requests_rate_limit counter\n";
echo "shieldphp_blocked_requests_rate_limit {$metrics->getBlockedRequestsByReason('rate_limit')}\n\n";

echo "# HELP shieldphp_blocked_requests_waf Blocked requests by WAF\n";
echo "# TYPE shieldphp_blocked_requests_waf counter\n";
echo "shieldphp_blocked_requests_waf {$metrics->getBlockedRequestsByReason('waf')}\n\n";

echo "# HELP shieldphp_blocked_ips_total Total unique blocked IPs\n";
echo "# TYPE shieldphp_blocked_ips_total gauge\n";
echo "shieldphp_blocked_ips_total " . count($metrics->getBlockedIPs()) . "\n\n"; 