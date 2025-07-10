<?php

namespace ShieldPHP;

class WAFRuleLoader
{
    public static function loadFromJson(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("JSON rules file not found: $filePath");
        }
        $json = file_get_contents($filePath);
        $data = json_decode($json, true);
        if (!is_array($data)) {
            throw new \RuntimeException("Invalid JSON rules format");
        }
        return $data;
    }

    public static function loadFromYaml(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException("YAML rules file not found: $filePath");
        }
        if (!class_exists('Symfony\\Component\\Yaml\\Yaml')) {
            throw new \RuntimeException('symfony/yaml is required for YAML support');
        }
        $yaml = file_get_contents($filePath);
        $data = \Symfony\Component\Yaml\Yaml::parse($yaml);
        if (!is_array($data)) {
            throw new \RuntimeException("Invalid YAML rules format");
        }
        return $data;
    }
} 