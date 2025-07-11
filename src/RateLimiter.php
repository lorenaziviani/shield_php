<?php

namespace ShieldPHP;

class RateLimiter
{
    private \Redis $redis;
    private int $limit;
    private int $window;

    public function __construct(\Redis $redis, int $limit = 100, int $window = 60)
    {
        $this->redis = $redis;
        $this->limit = $limit;
        $this->window = $window;
    }

    /**
     * Verify if the IP exceeded the limit of requests for an endpoint.
     *
     * @param  string $ip
     * @param  string $endpoint
     * @return bool true if allowed, false if blocked
     */
    public function allow(string $ip, string $endpoint): bool
    {
        $key = $this->getKey($ip, $endpoint);
        $current = $this->redis->incr($key);
        if ($current === 1) {
            $this->redis->expire($key, $this->window);
        }
        return $current <= $this->limit;
    }

    private function getKey(string $ip, string $endpoint): string
    {
        return sprintf('ratelimit:%s:%s', $ip, md5($endpoint));
    }
}
