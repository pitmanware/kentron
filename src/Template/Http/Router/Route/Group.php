<?php
declare(strict_types=1);

namespace Kentron\Template\Http\Router\Route;

use Kentron\Template\Http\Router\ARoute;

final class Group extends ARoute
{
    /** @var ARoute[] */
    private array $routes = [];

    public function __construct(?string $path = null)
    {
        if (is_null($path)) {
            $this->path = "";
        }
        else {
            parent::__construct($path);
        }
    }

    public function group(string $path = ""): Group
    {
        return $this->addRoute(new Group($path));
    }

    public function get(string $path = ""): Get
    {
        return $this->addRoute(new Get($path));
    }

    public function post(string $path = ""): Post
    {
        return $this->addRoute(new Post($path));
    }

    public function put(string $path = ""): Put
    {
        return $this->addRoute(new Put($path));
    }

    public function delete(string $path = ""): Delete
    {
        return $this->addRoute(new Delete($path));
    }

    public function options(string $path = ""): Delete
    {
        return $this->addRoute(new Options($path));
    }

    /**
     * Cycle over the routes in this group
     *
     * @return ARoute[]
     */
    public function iterateRoutes(): iterable
    {
        yield from $this->routes;
    }

    private function addRoute(ARoute $route): ARoute
    {
        $this->routes[] = $route;
        return $route;
    }
}
