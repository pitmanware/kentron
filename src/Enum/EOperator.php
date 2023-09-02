<?php
declare(strict_types=1);

namespace Kentron\Enum;

enum EOperator: string
{
    case Add = "add";
    case Subtract = "subtract";
    case Divide = "divide";
    case Multiply = "multiply";
    case Raise = "raise";
    case And = "and";
    case Or = "or";
}
