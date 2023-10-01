<?php
declare(strict_types=1);

namespace Kentron\Template\Http\Router;

use \Closure;

abstract class ARoute
{
    public string $path;
    public string $name;
    public string $method;
    public \Closure $controller;

    /** @var \Closure[] */
    public array $middlewares = [];

    public function __construct(string $path = "")
    {
        $path = $this->sanitisePath($path);

        if (empty($path)) {
            $path = "[/]";
        }
        $this->path = $path;
    }

    final protected function sanitisePath(string $path): string
    {
        return rtrim("/" . trim($path, "/"), "/");
    }

    final public function addMiddleware(callable $middleware): static
    {
        $this->middlewares[] = Closure::fromCallable($middleware);
        return $this;
    }

    final public function setController(callable $controller): static
    {
        $this->controller = Closure::fromCallable($controller);
        return $this;
    }

    final public function setName(string $name): void
    {
        $this->name = $name;
    }
}
