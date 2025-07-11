<?php

namespace ShieldPHP;

use ShieldPHP\LoggerInterface;
use ShieldPHP\SecurityResponse;

class SecurityMiddleware
{
    private RateLimiter $rateLimiter;
    private WAF $waf;
    private ?LoggerInterface $logger;
    private ?Metrics $metrics;

    public function __construct(
        RateLimiter $rateLimiter,
        WAF $waf,
        ?LoggerInterface $logger = null,
        ?Metrics $metrics = null
    ) {
        $this->rateLimiter = $rateLimiter;
        $this->waf = $waf;
        $this->logger = $logger;
        $this->metrics = $metrics;
    }

    /**
     * Middleware procedural: block if malicious or rate limit exceeded.
     *
     * @param string $ip
     * @param string $endpoint
     * @param array  $requestData
     */
    public function handle(string $ip, string $endpoint, array $requestData)
    {
        if (!$this->rateLimiter->allow($ip, $endpoint)) {
            if ($this->logger) {
                $this->logger->log(
                    'warning',
                    'Rate limit exceeded',
                    [
                    'ip' => $ip,
                    'endpoint' => $endpoint,
                    'data' => $requestData
                    ]
                );
            }
            if ($this->metrics) {
                $this->metrics->incrementBlockedRequest('rate_limit', $ip);
            }
            http_response_code(429);
            echo 'Too Many Requests';
            exit;
        }
        if ($this->waf->isMalicious($requestData)) {
            if ($this->logger) {
                $this->logger->log(
                    'alert',
                    'Malicious request blocked',
                    [
                    'ip' => $ip,
                    'endpoint' => $endpoint,
                    'data' => $requestData
                    ]
                );
            }
            if ($this->metrics) {
                $this->metrics->incrementBlockedRequest('waf', $ip);
            }
            http_response_code(403);
            echo 'Forbidden: Malicious request detected.';
            exit;
        }
    }

    /**
     * Example of PSR-15 integration
     */
    public function process($request, $handler)
    {
        $ip = $request->getServerParams()['REMOTE_ADDR'] ?? 'unknown';
        $endpoint = $request->getUri()->getPath();
        $params = array_merge(
            $request->getQueryParams() ?? [],
            $request->getParsedBody() ?? []
        );
        if (!$this->rateLimiter->allow($ip, $endpoint)) {
            if ($this->logger) {
                $this->logger->log(
                    'warning',
                    'Rate limit exceeded',
                    [
                    'ip' => $ip,
                    'endpoint' => $endpoint,
                    'data' => $params
                    ]
                );
            }
            if ($this->metrics) {
                $this->metrics->incrementBlockedRequest('rate_limit', $ip);
            }
            return new SecurityResponse(429, 'Too Many Requests');
        }
        if ($this->waf->isMalicious($params)) {
            if ($this->logger) {
                $this->logger->log(
                    'alert',
                    'Malicious request blocked',
                    [
                    'ip' => $ip,
                    'endpoint' => $endpoint,
                    'data' => $params
                    ]
                );
            }
            if ($this->metrics) {
                $this->metrics->incrementBlockedRequest('waf', $ip);
            }
            return new SecurityResponse(403, 'Forbidden: Malicious request detected.');
        }
        return $handler->handle($request);
    }
}
