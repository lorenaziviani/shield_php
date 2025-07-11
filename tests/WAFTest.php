<?php

use PHPUnit\Framework\TestCase;
use ShieldPHP\WAF;

class WAFTest extends TestCase
{
    private WAF $waf;

    protected function setUp(): void
    {
        $this->waf = new WAF();
    }

    public function testDetectsSQLInjection()
    {
        $malicious = [
            'id' => "1 OR 1=1",
            'query' => "SELECT * FROM users"
        ];
        $this->assertTrue($this->waf->isMalicious($malicious));
    }

    public function testDetectsXSS()
    {
        $malicious = [
            'input' => '<script>alert("xss")</script>',
            'param' => '<img src=x onerror=alert(1)>'
        ];
        $this->assertTrue($this->waf->isMalicious($malicious));
    }

    public function testDetectsShellInjection()
    {
        $malicious = [
            'cmd' => 'ls; rm -rf /',
            'payload' => '`cat /etc/passwd`'
        ];
        $this->assertTrue($this->waf->isMalicious($malicious));
    }

    public function testAllowsBenignInput()
    {
        $benign = [
            'name' => 'Maria',
            'email' => 'maria@example.com',
            'comment' => 'Hello, how are you?'
        ];
        $this->assertFalse($this->waf->isMalicious($benign));
    }
} 