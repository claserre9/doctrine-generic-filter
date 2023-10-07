<?php

namespace Tests;
use Dotenv\Dotenv;

class DotEnvLoader
{
    /**
     * Load environment variables from .env file.
     *
     * @param string $path Path to the directory containing the .env file.
     */
    public static function loadEnvironment(string $path = __DIR__ . '/../'): void
    {
        $dotenv = Dotenv::createImmutable($path);
        $dotenv->load();
    }
}