<?php
declare(strict_types=1);

namespace Kentron\Enum;

enum EMime: string
{
    case video3gpp2                                                           = "video/3gpp2";
    case video3gp                                                             = "video/3gp";
    case video3gpp                                                            = "video/3gpp";
    case applicationXCompressed                                               = "application/x-compressed";
    case audioXAcc                                                            = "audio/x-acc";
    case audioAc3                                                             = "audio/ac3";
    case applicationPostscript                                                = "application/postscript";
    case audioXAiff                                                           = "audio/x-aiff";
    case audioAiff                                                            = "audio/aiff";
    case audioXAu                                                             = "audio/x-au";
    case videoXMsvideo                                                        = "video/x-msvideo";
    case videoMsvideo                                                         = "video/msvideo";
    case videoAvi                                                             = "video/avi";
    case applicationXTroffMsvideo                                             = "application/x-troff-msvideo";
    case applicationMacbinary                                                 = "application/macbinary";
    case applicationMacBinary                                                 = "application/mac-binary";
    case applicationXBinary                                                   = "application/x-binary";
    case applicationXMacbinary                                                = "application/x-macbinary";
    case imageBmp                                                             = "image/bmp";
    case imageXBmp                                                            = "image/x-bmp";
    case imageXBitmap                                                         = "image/x-bitmap";
    case imageXXbitmap                                                        = "image/x-xbitmap";
    case imageXWinBitmap                                                      = "image/x-win-bitmap";
    case imageXWindowsBmp                                                     = "image/x-windows-bmp";
    case imageMsBmp                                                           = "image/ms-bmp";
    case imageXMsBmp                                                          = "image/x-ms-bmp";
    case applicationBmp                                                       = "application/bmp";
    case applicationXBmp                                                      = "application/x-bmp";
    case applicationXWinBitmap                                                = "application/x-win-bitmap";
    case applicationCdr                                                       = "application/cdr";
    case applicationCoreldraw                                                 = "application/coreldraw";
    case applicationXCdr                                                      = "application/x-cdr";
    case applicationXCoreldraw                                                = "application/x-coreldraw";
    case imageCdr                                                             = "image/cdr";
    case imagexCdr                                                            = "image/x-cdr";
    case zzApplicationZzWinassocCdr                                           = "zz-application/zz-winassoc-cdr";
    case applicationMacCompactpro                                             = "application/mac-compactpro";
    case applicationPkixCrl                                                   = "application/pkix-crl";
    case applicationPkcsCrl                                                   = "application/pkcs-crl";
    case applicationXX509CaCert                                               = "application/x-x509-ca-cert";
    case applicationPkixCert                                                  = "application/pkix-cert";
    case textCss                                                              = "text/css";
    case textXCommaSeparatedValues                                            = "text/x-comma-separated-values";
    case textCommaSeparatedValues                                             = "text/comma-separated-values";
    case applicationVndMsexcel                                                = "application/vnd.msexcel";
    case applicationXDirector                                                 = "application/x-director";
    case applicationVndOpenxmlformatsOfficedocumentWordprocessingmlDocument   = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
    case applicationXDvi                                                      = "application/x-dvi";
    case messageRfc822                                                        = "message/rfc822";
    case applicationXMsdownload                                               = "application/x-msdownload";
    case videoXF4v                                                            = "video/x-f4v";
    case audioXFlac                                                           = "audio/x-flac";
    case videoXFlv                                                            = "video/x-flv";
    case imageGif                                                             = "image/gif";
    case applicationGpgKeys                                                   = "application/gpg-keys";
    case applicationXGtar                                                     = "application/x-gtar";
    case applicationXGzip                                                     = "application/x-gzip";
    case applicationMacBinhex40                                               = "application/mac-binhex40";
    case applicationMacBinhex                                                 = "application/mac-binhex";
    case applicationXBinhex40                                                 = "application/x-binhex40";
    case applicationXMacBinhex40                                              = "application/x-mac-binhex40";
    case textHtml                                                             = "text/html";
    case imageXIcon                                                           = "image/x-icon";
    case imageXIco                                                            = "image/x-ico";
    case imageVndMicrosoftIcon                                                = "image/vnd.microsoft.icon";
    case textCalendar                                                         = "text/calendar";
    case applicationJavaArchive                                               = "application/java-archive";
    case applicationXJavaApplication                                          = "application/x-java-application";
    case applicationXJar                                                      = "application/x-jar";
    case imageJp2                                                             = "image/jp2";
    case videoMj2                                                             = "video/mj2";
    case imageJpx                                                             = "image/jpx";
    case imageJpm                                                             = "image/jpm";
    case imageJpeg                                                            = "image/jpeg";
    case imagePjpeg                                                           = "image/pjpeg";
    case applicationXJavascript                                               = "application/x-javascript";
    case applicationJson                                                      = "application/json";
    case textJson                                                             = "text/json";
    case applicationVndGoogleEarthKmlXml                                      = "application/vnd.google-earth.kml+xml";
    case applicationVndGoogleEarthKmz                                         = "application/vnd.google-earth.kmz";
    case textXLog                                                             = "text/x-log";
    case audioXM4a                                                            = "audio/x-m4a";
    case applicationVndMpegurl                                                = "application/vnd.mpegurl";
    case audioMidi                                                            = "audio/midi";
    case applicationVndMif                                                    = "application/vnd.mif";
    case videoQuicktime                                                       = "video/quicktime";
    case videoXSgiMovie                                                       = "video/x-sgi-movie";
    case audioMpeg                                                            = "audio/mpeg";
    case audioMpg                                                             = "audio/mpg";
    case audioMpeg3                                                           = "audio/mpeg3";
    case audioMp3                                                             = "audio/mp3";
    case videoMp4                                                             = "video/mp4";
    case videoMpeg                                                            = "video/mpeg";
    case applicationOda                                                       = "application/oda";
    case audioOgg                                                             = "audio/ogg";
    case videoOgg                                                             = "video/ogg";
    case applicationOgg                                                       = "application/ogg";
    case applicationXPkcs10                                                   = "application/x-pkcs10";
    case applicationPkcs10                                                    = "application/pkcs10";
    case applicationXPkcs12                                                   = "application/x-pkcs12";
    case applicationXPkcs7Signature                                           = "application/x-pkcs7-signature";
    case applicationPkcs7Mime                                                 = "application/pkcs7-mime";
    case applicationXPkcs7Mime                                                = "application/x-pkcs7-mime";
    case applicationXPkcs7Certreqresp                                         = "application/x-pkcs7-certreqresp";
    case applicationPkcs7Signature                                            = "application/pkcs7-signature";
    case applicationPdf                                                       = "application/pdf";
    case applicationOctetStream                                               = "application/octet-stream";
    case applicationXX509UserCert                                             = "application/x-x509-user-cert";
    case applicationXPemFile                                                  = "application/x-pem-file";
    case applicationPgp                                                       = "application/pgp";
    case applicationXHttpdPhp                                                 = "application/x-httpd-php";
    case applicationPhp                                                       = "application/php";
    case applicationXPhp                                                      = "application/x-php";
    case textPhp                                                              = "text/php";
    case textXPhp                                                             = "text/x-php";
    case applicationXHttpdPhpSource                                           = "application/x-httpd-php-source";
    case imagePng                                                             = "image/png";
    case imageXPng                                                            = "image/x-png";
    case applicationPowerpoint                                                = "application/powerpoint";
    case applicationVndMsPowerpoint                                           = "application/vnd.ms-powerpoint";
    case applicationVndMsOffice                                               = "application/vnd.ms-office";
    case applicationMsword                                                    = "application/msword";
    case applicationVndOpenxmlformatsOfficedocumentPresentationmlPresentation = "application/vnd.openxmlformats-officedocument.presentationml.presentation";
    case applicationXPhotoshop                                                = "application/x-photoshop";
    case imageVndAdobePhotoshop                                               = "image/vnd.adobe.photoshop";
    case audioXRealaudio                                                      = "audio/x-realaudio";
    case audioXPnRealaudio                                                    = "audio/x-pn-realaudio";
    case applicationXRar                                                      = "application/x-rar";
    case applicationRar                                                       = "application/rar";
    case applicationXRarCompressed                                            = "application/x-rar-compressed";
    case audioXPnRealaudioPlugin                                              = "audio/x-pn-realaudio-plugin";
    case applicationXPkcs7                                                    = "application/x-pkcs7";
    case textRtf                                                              = "text/rtf";
    case textRichtext                                                         = "text/richtext";
    case videoVndRnRealvideo                                                  = "video/vnd.rn-realvideo";
    case applicationXStuffit                                                  = "application/x-stuffit";
    case applicationSmil                                                      = "application/smil";
    case textSrt                                                              = "text/srt";
    case imageSvgXml                                                          = "image/svg+xml";
    case applicationXShockwaveFlash                                           = "application/x-shockwave-flash";
    case applicationXTar                                                      = "application/x-tar";
    case applicationXGzipCompressed                                           = "application/x-gzip-compressed";
    case imageTiff                                                            = "image/tiff";
    case textPlain                                                            = "text/plain";
    case textXVcard                                                           = "text/x-vcard";
    case applicationVideolan                                                  = "application/videolan";
    case textVtt                                                              = "text/vtt";
    case audioXWav                                                            = "audio/x-wav";
    case audioWave                                                            = "audio/wave";
    case audioWav                                                             = "audio/wav";
    case applicationWbxml                                                     = "application/wbxml";
    case videoWebm                                                            = "video/webm";
    case audioXMsWma                                                          = "audio/x-ms-wma";
    case applicationWmlc                                                      = "application/wmlc";
    case videoXMsWmv                                                          = "video/x-ms-wmv";
    case videoXMsAsf                                                          = "video/x-ms-asf";
    case applicationXhtmlXml                                                  = "application/xhtml+xml";
    case applicationExcel                                                     = "application/excel";
    case applicationMsexcel                                                   = "application/msexcel";
    case applicationXMsexcel                                                  = "application/x-msexcel";
    case applicationXMsExcel                                                  = "application/x-ms-excel";
    case applicationXExcel                                                    = "application/x-excel";
    case applicationXDos_ms_excel                                             = "application/x-dos_ms_excel";
    case applicationXls                                                       = "application/xls";
    case applicationXXls                                                      = "application/x-xls";
    case applicationVndOpenxmlformatsOfficedocumentSpreadsheetmlSheet         = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
    case applicationVndMsExcel                                                = "application/vnd.ms-excel";
    case applicationXml                                                       = "application/xml";
    case textXml                                                              = "text/xml";
    case textXsl                                                              = "text/xsl";
    case applicationXspfXml                                                   = "application/xspf+xml";
    case applicationXCompress                                                 = "application/x-compress";
    case applicationXZip                                                      = "application/x-zip";
    case applicationZip                                                       = "application/zip";
    case applicationXZipCompressed                                            = "application/x-zip-compressed";
    case applicationSCompressed                                               = "application/s-compressed";
    case multipartXZip                                                        = "multipart/x-zip";
    case textXScriptzsh                                                       = "text/x-scriptzsh";

    public function toExt(): ?string
    {
        return match ($this) {
            self::video3gpp2                                                           => '3g2',
            self::video3gp,
            self::video3gpp                                                            => '3gp',
            self::applicationXCompressed                                               => '7zip',
            self::audioXAcc                                                            => 'aac',
            self::audioAc3                                                             => 'ac3',
            self::applicationPostscript                                                => 'ai',
            self::audioXAiff,
            self::audioAiff                                                            => 'aif',
            self::audioXAu                                                             => 'au',
            self::videoXMsvideo,
            self::videoMsvideo,
            self::videoAvi,
            self::applicationXTroffMsvideo                                             => 'avi',
            self::applicationMacbinary,
            self::applicationMacBinary,
            self::applicationXBinary,
            self::applicationXMacbinary                                                => 'bin',
            self::imageBmp,
            self::imageXBmp,
            self::imageXBitmap,
            self::imageXXbitmap,
            self::imageXWinBitmap,
            self::imageXWindowsBmp,
            self::imageMsBmp,
            self::imageXMsBmp,
            self::applicationBmp,
            self::applicationXBmp,
            self::applicationXWinBitmap                                                => 'bmp',
            self::applicationCdr,
            self::applicationCoreldraw,
            self::applicationXCdr,
            self::applicationXCoreldraw,
            self::imageCdr,
            self::imagexCdr,
            self::zzApplicationZzWinassocCdr                                           => 'cdr',
            self::applicationMacCompactpro                                             => 'cpt',
            self::applicationPkixCrl,
            self::applicationPkcsCrl                                                   => 'crl',
            self::applicationXX509CaCert,
            self::applicationPkixCert                                                  => 'crt',
            self::textCss                                                              => 'css',
            self::textXCommaSeparatedValues,
            self::textCommaSeparatedValues,
            self::applicationVndMsexcel                                                => 'csv',
            self::applicationXDirector                                                 => 'dcr',
            self::applicationVndOpenxmlformatsOfficedocumentWordprocessingmlDocument   => 'docx',
            self::applicationXDvi                                                      => 'dvi',
            self::messageRfc822                                                        => 'eml',
            self::applicationXMsdownload                                               => 'exe',
            self::videoXF4v                                                            => 'f4v',
            self::audioXFlac                                                           => 'flac',
            self::videoXFlv                                                            => 'flv',
            self::imageGif                                                             => 'gif',
            self::applicationGpgKeys                                                   => 'gpg',
            self::applicationXGtar                                                     => 'gtar',
            self::applicationXGzip                                                     => 'gzip',
            self::applicationMacBinhex40,
            self::applicationMacBinhex,
            self::applicationXBinhex40,
            self::applicationXMacBinhex40                                              => 'hqx',
            self::textHtml                                                             => 'html',
            self::imageXIcon,
            self::imageXIco,
            self::imageVndMicrosoftIcon                                                => 'ico',
            self::textCalendar                                                         => 'ics',
            self::applicationJavaArchive,
            self::applicationXJavaApplication,
            self::applicationXJar                                                      => 'jar',
            self::imageJp2,
            self::videoMj2,
            self::imageJpx,
            self::imageJpm                                                             => 'jp2',
            self::imageJpeg,
            self::imagePjpeg                                                           => 'jpeg',
            self::applicationXJavascript                                               => 'js',
            self::applicationJson,
            self::textJson                                                             => 'json',
            self::applicationVndGoogleEarthKmlXml                                      => 'kml',
            self::applicationVndGoogleEarthKmz                                         => 'kmz',
            self::textXLog                                                             => 'log',
            self::audioXM4a                                                            => 'm4a',
            self::applicationVndMpegurl                                                => 'm4u',
            self::audioMidi                                                            => 'mid',
            self::applicationVndMif                                                    => 'mif',
            self::videoQuicktime                                                       => 'mov',
            self::videoXSgiMovie                                                       => 'movie',
            self::audioMpeg,
            self::audioMpg,
            self::audioMpeg3,
            self::audioMp3                                                             => 'mp3',
            self::videoMp4                                                             => 'mp4',
            self::videoMpeg                                                            => 'mpeg',
            self::applicationOda                                                       => 'oda',
            self::audioOgg,
            self::videoOgg,
            self::applicationOgg                                                       => 'ogg',
            self::applicationXPkcs10,
            self::applicationPkcs10                                                    => 'p10',
            self::applicationXPkcs12                                                   => 'p12',
            self::applicationXPkcs7Signature                                           => 'p7a',
            self::applicationPkcs7Mime,
            self::applicationXPkcs7Mime                                                => 'p7c',
            self::applicationXPkcs7Certreqresp                                         => 'p7r',
            self::applicationPkcs7Signature                                            => 'p7s',
            self::applicationPdf,
            self::applicationOctetStream                                               => 'pdf',
            self::applicationXX509UserCert,
            self::applicationXPemFile                                                  => 'pem',
            self::applicationPgp                                                       => 'pgp',
            self::applicationXHttpdPhp,
            self::applicationPhp,
            self::applicationXPhp,
            self::textPhp,
            self::textXPhp,
            self::applicationXHttpdPhpSource                                           => 'php',
            self::imagePng,
            self::imageXPng                                                            => 'png',
            self::applicationPowerpoint,
            self::applicationVndMsPowerpoint,
            self::applicationVndMsOffice                                               => 'ppt',
            self::applicationMsword                                                    => 'doc',
            self::applicationVndOpenxmlformatsOfficedocumentPresentationmlPresentation => 'pptx',
            self::applicationXPhotoshop,
            self::imageVndAdobePhotoshop                                               => 'psd',
            self::audioXRealaudio                                                      => 'ra',
            self::audioXPnRealaudio                                                    => 'ram',
            self::applicationXRar,
            self::applicationRar,
            self::applicationXRarCompressed                                            => 'rar',
            self::audioXPnRealaudioPlugin                                              => 'rpm',
            self::applicationXPkcs7                                                    => 'rsa',
            self::textRtf                                                              => 'rtf',
            self::textRichtext                                                         => 'rtx',
            self::videoVndRnRealvideo                                                  => 'rv',
            self::applicationXStuffit                                                  => 'sit',
            self::applicationSmil                                                      => 'smil',
            self::textSrt                                                              => 'srt',
            self::imageSvgXml                                                          => 'svg',
            self::applicationXShockwaveFlash                                           => 'swf',
            self::applicationXTar                                                      => 'tar',
            self::applicationXGzipCompressed                                           => 'tgz',
            self::imageTiff                                                            => 'tiff',
            self::textPlain                                                            => 'txt',
            self::textXVcard                                                           => 'vcf',
            self::applicationVideolan                                                  => 'vlc',
            self::textVtt                                                              => 'vtt',
            self::audioXWav,
            self::audioWave,
            self::audioWav                                                             => 'wav',
            self::applicationWbxml                                                     => 'wbxml',
            self::videoWebm                                                            => 'webm',
            self::audioXMsWma                                                          => 'wma',
            self::applicationWmlc                                                      => 'wmlc',
            self::videoXMsWmv,
            self::videoXMsAsf                                                          => 'wmv',
            self::applicationXhtmlXml                                                  => 'xhtml',
            self::applicationExcel                                                     => 'xl',
            self::applicationMsexcel,
            self::applicationXMsexcel,
            self::applicationXMsExcel,
            self::applicationXExcel,
            self::applicationXDos_ms_excel,
            self::applicationXls,
            self::applicationXXls                                                      => 'xls',
            self::applicationVndOpenxmlformatsOfficedocumentSpreadsheetmlSheet,
            self::applicationVndMsExcel                                                => 'xlsx',
            self::applicationXml,
            self::textXml                                                              => 'xml',
            self::textXsl                                                              => 'xsl',
            self::applicationXspfXml                                                   => 'xspf',
            self::applicationXCompress                                                 => 'z',
            self::applicationXZip,
            self::applicationZip,
            self::applicationXZipCompressed,
            self::applicationSCompressed,
            self::multipartXZip                                                        => 'zip',
            self::textXScriptzsh                                                       => 'zsh'
        };
    }
}
