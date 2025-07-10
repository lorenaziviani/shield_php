<?php

use PHPUnit\Framework\TestCase;
use ShieldPHP\RateLimiter;

class RateLimiterTest extends TestCase
{
    public function testAllowWithinLimit()
    {
        $redis = $this->createMock(Redis::class);
        $redis->method('incr')->willReturnOnConsecutiveCalls(1, 2, 3);
        $redis->method('expire')->willReturn(true);

        $limiter = new RateLimiter($redis, 3, 60);
        $ip = '127.0.0.1';
        $endpoint = '/api/test';

        $this->assertTrue($limiter->allow($ip, $endpoint));
        $this->assertTrue($limiter->allow($ip, $endpoint));
        $this->assertTrue($limiter->allow($ip, $endpoint));
    }

    public function testBlockWhenLimitExceeded()
    {
        $redis = $this->createMock(Redis::class);
        $redis->method('incr')->willReturnOnConsecutiveCalls(4);
        $redis->method('expire')->willReturn(true);

        $limiter = new RateLimiter($redis, 3, 60);
        $ip = '127.0.0.1';
        $endpoint = '/api/test';

        $this->assertFalse($limiter->allow($ip, $endpoint));
    }
} 