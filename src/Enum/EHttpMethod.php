<?php
declare(strict_types=1);

namespace Kentron\Enum;

enum EHttpMethod: string
{
    case Get = "GET";
    case Put = "PUT";
    case Post = "POST";
    case Delete = "DELETE";
    case Patch = "PATCH";
    case Soap = "SOAP";
}
