<?php

namespace App\Plugins\Di;

class Container
{
    private static ?self $instance = null;
    private array $shared = [];

    /**
     * Get the singleton instance of the container.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Set a shared instance in the container.
     *
     * @param string $key
     * @param callable $value
     */
    public function setShared(string $key, callable $value): void
    {
        $this->shared[$key] = $value();
    }

    /**
     * Get a shared instance from the container.
     *
     * @param string $key
     * @return mixed
     * @throws \Exception
     */
    public function getShared(string $key)
    {
        if (!isset($this->shared[$key])) {
            throw new \Exception("Tried to retrieve a shared service which is not registered in the DI");
        }
        return $this->shared[$key];
    }
}
