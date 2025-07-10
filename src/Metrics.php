<?php

namespace ShieldPHP;

class Metrics
{
    private \Redis $redis;
    private string $prefix;

    public function __construct(\Redis $redis, string $prefix = 'shieldphp:metrics:')
    {
        $this->redis = $redis;
        $this->prefix = $prefix;
    }

    public function incrementBlockedRequest(string $reason, string $ip = null): void
    {
        $this->redis->incr($this->prefix . 'blocked_requests');
        $this->redis->incr($this->prefix . 'blocked_requests:' . $reason);
        if ($ip) {
            $this->redis->sAdd($this->prefix . 'blocked_ips', $ip);
            $this->redis->incr($this->prefix . 'blocked_requests:ip:' . $ip);
        }
    }

    public function getBlockedRequests(): int
    {
        return (int) $this->redis->get($this->prefix . 'blocked_requests') ?: 0;
    }

    public function getBlockedRequestsByReason(string $reason): int
    {
        return (int) $this->redis->get($this->prefix . 'blocked_requests:' . $reason) ?: 0;
    }

    public function getBlockedIPs(): array
    {
        return $this->redis->sMembers($this->prefix . 'blocked_ips') ?: [];
    }

    public function getBlockedRequestsByIP(string $ip): int
    {
        return (int) $this->redis->get($this->prefix . 'blocked_requests:ip:' . $ip) ?: 0;
    }
} 