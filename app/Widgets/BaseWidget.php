<?php

namespace App\Widgets;

abstract class BaseWidget
{
    protected $settings;

    public function __construct($settings = [])
    {
        $this->settings = $settings;
    }

    abstract public function render(): string;

    abstract public static function getConfig(): array;

    public function css(): string
    {
        return '';
    }

    public function js(): string
    {
        return '';
    }

    protected function get($key, $default = '')
    {
        return $this->settings[$key] ?? $default;
    }
}

