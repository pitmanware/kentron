<?php
declare(strict_types=1);

namespace Kentron\Enum;

enum EContentType: string
{
    case TYPE_JSON = "application/json";
    case TYPE_MSWORD = "application/msword";
    case TYPE_OCTET_STREAM = "application/octet-stream";
    case TYPE_PDF = "application/pdf";
    case TYPE_EXCEL = "application/vnd.ms-excel";
    case TYPE_XHTML = "application/xhtml+xml";
    case TYPE_XML = "application/xml";
    case TYPE_ZIP = "application/zip";
    case TYPE_MPEG = "audio/mpeg";
    case TYPE_GIF = "image/gif";
    case TYPE_JPEG = "image/jpeg";
    case TYPE_PNG = "image/png";
    case TYPE_SVG = "image/svg+xml";
    case TYPE_TIFF = "image/tiff";
    case TYPE_CSS = "text/css";
    case TYPE_CSV = "text/csv";
    case TYPE_HTML = "text/html";
    case TYPE_JAVASCRIPT = "text/javascript";
    case TYPE_PLAIN = "text/plain";
    case TYPE_MP4 = "video/mp4";
}
