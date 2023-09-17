<?php
declare(strict_types=1);

namespace Kentron\Template\Http\Router;

use Kentron\Template\Http\Router\Route\Group;

interface IRouter
{
    public static function load(Group $group): void;
}
