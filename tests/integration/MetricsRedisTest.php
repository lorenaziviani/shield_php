<?php

use PHPUnit\Framework\TestCase;
use ShieldPHP\Metrics;

class MetricsRedisTest extends TestCase
{
    private Redis $redis;
    private Metrics $metrics;

    protected function setUp(): void
    {
        $this->redis = new Redis();
        $this->redis->connect('redis', 6379);
        $this->redis->flushAll();
        $this->metrics = new Metrics($this->redis);
    }

    public function testIncrementAndGetBlockedRequests()
    {
        $this->metrics->incrementBlockedRequest('waf', '1.2.3.4');
        $this->metrics->incrementBlockedRequest('rate_limit', '1.2.3.4');
        $this->metrics->incrementBlockedRequest('waf', '5.6.7.8');
        $this->assertEquals(3, $this->metrics->getBlockedRequests());
        $this->assertEquals(2, $this->metrics->getBlockedRequestsByReason('waf'));
        $this->assertEquals(1, $this->metrics->getBlockedRequestsByReason('rate_limit'));
        $this->assertCount(2, $this->metrics->getBlockedIPs());
        $this->assertEquals(2, $this->metrics->getBlockedRequestsByIP('1.2.3.4'));
    }
} 