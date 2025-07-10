<?php

namespace ShieldPHP;

class WAF
{
    private array $customRules;
    private array $compiledCustomPatterns = [];

    public function __construct(array $customRules = [])
    {
        $this->customRules = $customRules;
        // Pré-compilar padrões customizados para otimizar
        foreach ($customRules as $rule) {
            if (!empty($rule['pattern'])) {
                $this->compiledCustomPatterns[] = $rule['pattern'];
            }
        }
    }

    /**
     * Verifica se a requisição contém padrões de SQL Injection.
     * @param array $requestData Dados da requisição (ex: $_GET, $_POST, $_REQUEST)
     * @return bool true se for malicioso, false caso contrário
     */
    public function isMalicious(array $requestData): bool
    {
        foreach ($requestData as $value) {
            if (!is_string($value)) {
                continue;
            }
            if (
                $this->detectSQLInjection($value) ||
                $this->detectXSS($value) ||
                $this->detectShellInjection($value) ||
                $this->detectCustomRules($value)
            ) {
                return true;
            }
        }
        return false;
    }

    private function detectSQLInjection($value): bool
    {
        if (!is_string($value)) {
            return false;
        }
        // Regras básicas de SQLi (pode ser expandido depois)
        $patterns = [
            '/(\bUNION\b|\bSELECT\b|\bINSERT\b|\bUPDATE\b|\bDELETE\b|\bDROP\b|\bOR\b|\bAND\b)/i',
            '/(--|#|\/\*)/',
            '/\b1=1\b/',
            '/\bOR\s+\d+=\d+/i',
            '/\bSLEEP\s*\(/i',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }
        return false;
    }

    private function detectXSS($value): bool
    {
        if (!is_string($value)) {
            return false;
        }
        // Regras básicas de XSS
        $patterns = [
            '/<script.*?>.*?<\/script>/is',
            '/on\w+\s*=\s*([\"\"]).*?\1/is',
            '/javascript:/i',
            '/<.*?\s+src\s*=\s*[\"\"]/is',
            '/document\.(cookie|location)/i',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }
        return false;
    }

    private function detectShellInjection($value): bool
    {
        if (!is_string($value)) {
            return false;
        }
        // Regras básicas de shell injection
        $patterns = [
            '/(;|&&|\|\|)/',
            '/\b(cat|ls|rm|wget|curl|nc|bash|sh)\b/i',
            '/`.*?`/',
            '/\$\(.*?\)/',
        ];
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }
        return false;
    }

    private function detectCustomRules($value): bool
    {
        if (empty($this->compiledCustomPatterns)) {
            return false;
        }
        foreach ($this->compiledCustomPatterns as $pattern) {
            if (preg_match($pattern, $value)) {
                return true;
            }
        }
        return false;
    }
} 