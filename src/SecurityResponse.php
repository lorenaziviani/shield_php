<?php

namespace ShieldPHP;

class SecurityResponse
{
    private int $code;
    private string $message;
    public function __construct(int $code, string $message)
    {
        $this->code = $code;
        $this->message = $message;
    }
    public function __toString()
    {
        http_response_code($this->code);
        return $this->message;
    }
}
