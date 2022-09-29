<?php

namespace carlansell94\Liteblog\Config;

final class Config
{
    public function __construct(
        private readonly string $config_dir = __DIR__ . '/../Config/config.ini'
    ) {
    }

    public function load(): bool
    {
        if (!file_exists($this->config_dir)) {
            return false;
        }

        if (!$config = parse_ini_file($this->config_dir)) {
            return false;
        }

        foreach ($config as $key => $value) {
            define($key, $value);
        }

        return true;
    }

    public function isValid(): bool
    {
        $required = array('DB_HOST', 'DB_USER', 'DB_PASS', 'DB_NAME');

        foreach ($required as $constant) {
            if (!defined($constant)) {
                return false;
            }
        }

        return true;
    }
}
