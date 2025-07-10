<?php

namespace ShieldPHP;

class SimpleFileLogger implements LoggerInterface
{
    private string $file;
    private string $env;
    private bool $debug;

    public function __construct(string $file, string $env = 'prod', bool $debug = false)
    {
        $this->file = $file;
        $this->env = $env;
        $this->debug = $debug;
    }

    public function log(string $level, string $message, array $context = []): void
    {
        if ($this->env === 'prod' && $level === 'debug' && !$this->debug) {
            return; // Não loga debug em produção se não estiver habilitado
        }
        $date = date('Y-m-d H:i:s');
        $contextStr = $context ? json_encode($context, JSON_UNESCAPED_UNICODE) : '';
        $line = "[$date][$this->env][$level] $message $contextStr\n";
        file_put_contents($this->file, $line, FILE_APPEND);
    }
} 