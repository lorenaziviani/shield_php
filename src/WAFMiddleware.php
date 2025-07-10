<?php

namespace ShieldPHP;

class WAFMiddleware
{
    private WAF $waf;

    public function __construct(WAF $waf)
    {
        $this->waf = $waf;
    }

    /**
     * Middleware procedural: bloqueia execução se detectar ataque.
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
     * Exemplo de integração PSR-15 (para frameworks como Slim, Mezzio, etc)
     *
     * @param $request
     * @param $handler
     * @return mixed
     */
    public function process($request, $handler)
    {
        $params = array_merge(
            $request->getQueryParams() ?? [],
            $request->getParsedBody() ?? []
        );
        if ($this->waf->isMalicious($params)) {
            return new \ShieldPHP\WAFResponse();
        }
        return $handler->handle($request);
    }
}

class WAFResponse
{
    public function __toString()
    {
        http_response_code(403);
        return 'Forbidden: Malicious request detected.';
    }
} 