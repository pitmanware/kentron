<?php
declare(strict_types=1);

namespace Kentron\Enum;

enum EContentType: string
{
    case Json = "application/json";
    case word = "application/msword";
    case OctetStream = "application/octet-stream";
    case Pdf = "application/pdf";
    case Excel = "application/vnd.ms-excel";
    case Xhtml = "application/xhtml+xml";
    case Xml = "application/xml";
    case Zip = "application/zip";
    case Mpeg = "audio/mpeg";
    case Gif = "image/gif";
    case Jpeg = "image/jpeg";
    case Png = "image/png";
    case Svg = "image/svg+xml";
    case Tiff = "image/tiff";
    case Css = "text/css";
    case Csv = "text/csv";
    case Html = "text/html";
    case Javascript = "text/javascript";
    case Plain = "text/plain";
    case Mp4 = "video/mp4";
}
