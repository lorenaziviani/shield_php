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
     * Verifica se o IP ultrapassou o limite de requisições para um endpoint.
     *
     * @param string $ip
     * @param string $endpoint
     * @return bool true se permitido, false se bloqueado
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