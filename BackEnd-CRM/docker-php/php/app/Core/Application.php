<?php

namespace App\Core;

class Application
{
    private static ?self $instance = null;

    private Router $router;
    private array $config = [];

    public function __construct()
    {
        self::$instance = $this;

        Config::loadFromDirectory(APP_PATH . '/Config');
        $this->config = Config::all();

        if ($timezone = Config::get('app.timezone')) {
            date_default_timezone_set($timezone);
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->router = new Router();
    }

    public static function instance(): ?self
    {
        return self::$instance;
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function config(string $key, $default = null)
    {
        return Config::get($key, $default);
    }

    public function run(): void
    {
        $request = Request::capture();
        $this->router->dispatch($request);
    }
}
