<?php
declare(strict_types=1);

namespace Kentron\Support\Http;

enum EDecodeAs
{
    case None;
    case Json;
    case Serial;
    case File;
    case Xml;
    case Soap;
}
