<?php

namespace ShieldPHP;

use ShieldPHP\WAFResponse;

class WAFMiddleware
{
    private WAF $waf;

    public function __construct(WAF $waf)
    {
        $this->waf = $waf;
    }

    /**
     * Middleware procedural: block execution if attack is detected.
     *
     * @param array $requestData
     */
    public function handle(array $requestData)
    {
        if ($this->waf->isMalicious($requestData)) {
            http_response_code(403);
            echo 'Forbidden: Malicious request detected.';
            exit;
        }
    }

    /**
     * Example of PSR-15 integration (for frameworks like Slim, Mezzio, etc)
     *
     * @param  $request
     * @param  $handler
     * @return mixed
     */
    public function process($request, $handler)
    {
        $params = array_merge(
            $request->getQueryParams() ?? [],
            $request->getParsedBody() ?? []
        );
        if ($this->waf->isMalicious($params)) {
            return new WAFResponse();
        }
        return $handler->handle($request);
    }
}
