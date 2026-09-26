<?php

namespace App\Core;

class Config
{
    private static array $items = [];

    public static function loadFromDirectory(string $directory): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $files = glob(rtrim($directory, '/') . '/*.php') ?: [];
        foreach ($files as $file) {
            $key = basename($file, '.php');
            $value = require $file;
            if (is_array($value)) {
                self::$items[$key] = $value;
            }
        }
    }

    public static function set(string $key, $value): void
    {
        $segments = explode('.', $key);
        $data =& self::$items;
        foreach ($segments as $segment) {
            if (!isset($data[$segment]) || !is_array($data[$segment])) {
                $data[$segment] = [];
            }
            $data =& $data[$segment];
        }
        $data = $value;
    }

    public static function get(string $key, $default = null)
    {
        $segments = explode('.', $key);
        $data = self::$items;

        foreach ($segments as $segment) {
            if (!is_array($data) || !array_key_exists($segment, $data)) {
                return $default;
            }
            $data = $data[$segment];
        }

        return $data;
    }

    public static function all(): array
    {
        return self::$items;
    }
}
