<?php
declare(strict_types=1);

namespace Kentron\Template\Http\Router;

use Kentron\Template\Http\Router\Route\Group;

abstract class ARouteAdapter
{
    public function __construct(
        public Group $group
    ) {}

    abstract public function translate(): void;
}
