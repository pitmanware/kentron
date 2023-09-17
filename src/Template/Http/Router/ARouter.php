<?php
declare(strict_types=1);

namespace Kentron\Template\Http\Router;

use Kentron\Template\Http\Factory\AControllerFactory;
use Kentron\Template\Http\Factory\AMiddlewareFactory;
use Kentron\Template\Http\Router\Route\Group;

abstract class ARouter
{
    protected function __construct
    (
        private AControllerFactory $controllerFactory,
        private AMiddlewareFactory|null $middlewareFactory = null
    ) {}

    abstract public function load(Group $group): void;

    protected function getControllerFactory(): AControllerFactory
    {
        return $this->controllerFactory;
    }

    protected function getMiddlewareFactory(): AMiddlewareFactory|null
    {
        return $this->middlewareFactory;
    }
}
