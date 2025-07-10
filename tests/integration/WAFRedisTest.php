<?php

use PHPUnit\Framework\TestCase;
use ShieldPHP\WAF;
use ShieldPHP\WAFRuleLoader;

class WAFRedisTest extends TestCase
{
    public function testDetectsCustomRuleFromJson()
    {
        $rules = WAFRuleLoader::loadFromJson(__DIR__ . '/../../rules.json');
        $waf = new WAF($rules);
        $malicious = [
            'payload' => base64_encode(random_bytes(40)),
            'code' => 'eval(alert(1))'
        ];
        $this->assertTrue($waf->isMalicious($malicious));
    }
} 