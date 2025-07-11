<?php

use PHPUnit\Framework\TestCase;
use ShieldPHP\RateLimiter;

class RateLimiterRedisTest extends TestCase
{
    private Redis $redis;
    private RateLimiter $limiter;
    private string $ip = '127.0.0.1';
    private string $endpoint = '/api/test';

    protected function setUp(): void
    {
        $redisHost = getenv('REDIS_HOST') ?: '127.0.0.1';
        $this->redis = new Redis();
        $this->redis->connect($redisHost, 6379);
        $this->redis->flushAll();
        $this->limiter = new RateLimiter($this->redis, 2, 10);
    }

    public function testAllowsWithinLimit()
    {
        $this->assertTrue($this->limiter->allow($this->ip, $this->endpoint));
        $this->assertTrue($this->limiter->allow($this->ip, $this->endpoint));
    }

    public function testBlocksWhenLimitExceeded()
    {
        $this->limiter->allow($this->ip, $this->endpoint);
        $this->limiter->allow($this->ip, $this->endpoint);
        $this->assertFalse($this->limiter->allow($this->ip, $this->endpoint));
    }
} 