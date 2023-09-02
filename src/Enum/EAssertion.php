<?php
declare(strict_types=1);

namespace Kentron\Enum;

enum EAssertion: string
{
    case Same = "same";
    case Equals = "eq";
    case LessThan = "lt";
    case GreaterThan = "gt";
    case LessThanEquals = "le";
    case GreaterThanEquals = "ge";
    case Spaceship = "sp";
    case Is = "is";
    case Has = "has";
    case Contains = "contains";
    case Matches = "matches";
}
