<?php

namespace ShieldPHP;

class WAFResponse
{
    public function __toString()
    {
        http_response_code(403);
        return 'Forbidden: Malicious request detected.';
    }
}
