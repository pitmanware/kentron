<?php
declare(strict_types=1);

namespace Kentron\Facade\Env\Enum;

enum EEnvironment: string
{
    case Dev = "DEV";
    case Docker = "DOCKER";
    case Uat = "UAT";
    case UatDev = "UAT-DEV";
    case Live = "LIVE";
}
