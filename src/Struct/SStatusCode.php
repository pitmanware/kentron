<?php
declare(strict_types=1);

namespace Kentron\Struct;

final class SStatusCode
{
    /**
     * #### 100 - Continue
     * Used to respond to a HEAD request to validate headers before sending the body
     */
    public const CODE_100 = 100;

    /**
     * #### 200 - OK
     * Everything was successful
     */
    public const CODE_200 = 200;
    /**
     * #### 201 - Created
     * The server successfully created the resource from a given PUT/POST
     */
    public const CODE_201 = 201;
    /**
     * #### 202 - Accepted
     * The server successfully recieved the data from a PUT/POST but has not yet processed it
     */
    public const CODE_202 = 202;
    /**
     * #### 203 - Non-Authorative Information
     * The response data was collected from a local or third-party copy, and not from the requested server
     */
    public const CODE_203 = 203;
    /**
     * #### 204 - No Content
     * Mostly used for PUT requests where no response body is expected, such as 'save' forms on UI
     */
    public const CODE_204 = 204;
    /**
     * #### 205 - Reset Content
     * Tells the client that it needs to reload the document to update the UI
     */
    public const CODE_205 = 205;
    /**
     * #### 206 - Partial Content
     * The server is only responding a part of the total resource, as specified by the Range header from the client
     */
    public const CODE_206 = 206;

    /**
     * #### 300 - Multiple Choices
     * Indicates that there are multiple available responses and the user needs to choose one
     */
    public const CODE_300 = 300;
    /**
     * #### 301 - Moved Permanently
     * Indicates that the resource has been permanently changed to the URL specified by the Location header
     * After first use by the browser, any subsequent requests will automatically redirect before hitting the server
     */
    public const CODE_301 = 301;
    /**
     * #### 302 - Found
     * Indicates that the resource has been temporarily changed to the URL specified by the Location header
     * It is expected that the request could be made multiple times with different redirects
     */
    public const CODE_302 = 302;
    /**
     * #### 303 - See Other
     * The server cannot supply the requested information, but provides a GET URL to where it can be retreived using the Location header
     */
    public const CODE_303 = 303;
    /**
     * #### 304 - Not Modified
     * Indicates an implicit redirection to a cached resource for GET and HEAD requests
     */
    public const CODE_304 = 304;
    /**
     * #### 307 - Temporary Redirect
     * Indicates that the resource has been permanently changed to the URL specified by the Location header
     * The new request method must match that of the original: GET->GET, POST->POST etc.
     * After first use by the browser, any subsequent requests will automatically redirect before hitting the server
     */
    public const CODE_307 = 307;
    /**
     * #### 308 - Permanent Redirect
     * Indicates that the resource has been temporarily changed to the URL specified by the Location header
     * The new request method must match that of the original: GET->GET, POST->POST etc.
     * It is expected that the request could be made multiple times with different redirects
     */
    public const CODE_308 = 308;

    /**
     * #### 400 - Bad Request
     * The server cannot or will not process the request due to something that is perceived to be a client error
     */
    public const CODE_400 = 400;
    /**
     * #### 401 - Unauthorised
     * The user is attempting to make a request that requires authentication that they are not providing or the authentication data is invalid
     */
    public const CODE_401 = 401;
    /**
     * #### 403 - Forbidden
     * The user is authenticated, but does not have permission to make this request
     */
    public const CODE_403 = 403;
    /**
     * #### 404 - Not Found
     * The requested URL is not recognised or the resource is not available
     */
    public const CODE_404 = 404;
    /**
     * #### 405 - Method Not Allowed
     * The method used for this request is not valid
     */
    public const CODE_405 = 405;
    /**
     * #### 408 - Request Timeout
     * The server is shutting the connection after idle timeout
     */
    public const CODE_408 = 408;
    /**
     * #### 409 - Conflict
     * The request body of a PUT or POST is shown to conflict with the version stored on the server, useful for state machines
     */
    public const CODE_409 = 409;
    /**
     * #### 411 - Length Required
     * The request requires the Content-Length header and was not given one
     */
    public const CODE_411 = 411;
    /**
     * #### 413 - Payload Too Large
     * The request is too large for the server to handle
     */
    public const CODE_413 = 413;
    /**
     * #### 415 - Unsupported Media Type
     * The request Content-Type or Content-Encoding header is not accepted by the server
     */
    public const CODE_415 = 415;
    /**
     * #### 416 - Range Not Satisfiable
     * The request Range header is outside of the expected range of the resource
     */
    public const CODE_416 = 416;
    /**
     * #### 418 - I'm a Teapot
     * The server refuses to brew coffee becuase it is, permanently, a teapot
     */
    public const CODE_418 = 418;
    /**
     * #### 422 - Unprocessable Entity
     * The request body was well-formed but could not be correctly processed by the server
     */
    public const CODE_422 = 422;
    /**
     * #### 429 - Too Many Requests
     * The user has sent too many requests within a given time
     */
    public const CODE_429 = 429;
    /**
     * #### 451 - Unavailable For Legal Reasons
     * The requested resource could not be legally provided
     */
    public const CODE_451 = 451;

    /**
     * #### 500 - Internal Server Error
     * The server has encountered a situation it does not know how to handle
     */
    public const CODE_500 = 500;
    /**
     * #### 501 - Not Implemented
     * The request method is not supported by the server and cannot be handled
     */
    public const CODE_501 = 501;
    /**
     * #### 502 - Bad Gateway
     * The server, acting as a gateway, received an invalid response from the application it was attempting to connect to
     */
    public const CODE_502 = 502;
    /**
     * #### 503 - Service Unavailable
     * The server is not ready to handle requests because it is either down or busy
     */
    public const CODE_503 = 503;
    /**
     * #### 504 - Gateway Timeout
     * The server, acting as a gateway, did not receive a response from the application in time
     */
    public const CODE_504 = 504;
    /**
     * #### 505 - HTTP Version Not Supported
     * The HTTP version used in the request is not supported by the server
     */
    public const CODE_505 = 505;
    /**
     * #### 508 - Loop Detected
     * The server detected an infinite loop while processing the request
     */
    public const CODE_508 = 508;

    /**
     * Check if the code is in the 1xx or 2xx blocks
     *
     * @param int $code
     * 
     * @return bool
     */
    public static function codeIndicatesSuccess(int $code): bool
    {
        return match($code) {
            self::CODE_100,
            self::CODE_200,
            self::CODE_201,
            self::CODE_202,
            self::CODE_203,
            self::CODE_204,
            self::CODE_205,
            self::CODE_206 => true,
            default => false
        };
    }

    /**
     * Checks if the code is in the 4xx block
     *
     * @param int $code
     * 
     * @return bool
     */
    public static function codeIndicatesClientError(int $code): bool
    {
        return match($code) {
            self::CODE_400,
            self::CODE_401,
            self::CODE_403,
            self::CODE_404,
            self::CODE_405,
            self::CODE_408,
            self::CODE_409,
            self::CODE_411,
            self::CODE_413,
            self::CODE_415,
            self::CODE_416,
            self::CODE_418,
            self::CODE_422,
            self::CODE_429,
            self::CODE_451 => true,
            default => false
        };
    }

    /**
     * Checks if the code is in the 5xx block
     *
     * @param int $code
     * 
     * @return bool
     */
    public static function codeIndicatesServerError(int $code): bool
    {
        return match($code) {
            self::CODE_500,
            self::CODE_501,
            self::CODE_502,
            self::CODE_503,
            self::CODE_504,
            self::CODE_505,
            self::CODE_508 => true,
            default => false
        };
    }

    /**
     * Checks if the code requires the Location header to be set
     *
     * @param int $code
     * 
     * @return bool
     */
    public static function codeRequiresLocation(int $code): bool
    {
        return match($code) {
            self::CODE_301,
            self::CODE_302,
            self::CODE_303,
            self::CODE_307,
            self::CODE_308 => true,
            default => false
        };
    }

    /**
     * Get the error or notification message relating to this code
     *
     * @param int $code
     * 
     * @return string
     */
    public static function getMessage(int $code): string
    {
        return match($code) {
            self::CODE_100 => "Continue: The header check was successful, the server is now ready to receive data",

            self::CODE_200 => "OK: The request succeeded normally",
            self::CODE_201 => "Created: The request succeeded and a new resource was created",
            self::CODE_202 => "Accepted: The request has been received but has not yet been acted upon",
            self::CODE_203 => "Non-Authoritative Information: The server returned transformed data from a non-origin location",
            self::CODE_204 => "No Content: The request succeeded and does not need to return data, and therefore has not",
            self::CODE_205 => "Reset Content: The request succeeded and asks the client to reload its view",
            self::CODE_206 => "Partial Content: The request succeeded and is only returning part of the total resource as detailed by the Range header from the client",

            self::CODE_300 => "Multiple Choices: There are multiple available responses and the user must choose one",
            self::CODE_301 => "Moved Permanently: The resource has been permanently changed to the URL in the Location header",
            self::CODE_302 => "Found: The resource has been temporarily changed to the URL in the Location header",
            self::CODE_303 => "See Other: The resource could not be supplied, but provides a URL in the Location header to where it can be requested",
            self::CODE_304 => "Not Modified: The response has not been modified and the client should continue using the cached redirect",
            self::CODE_307 => "Temporary Redirect: The resource has been temporarily changed to the URL in the Location header. The new request must use the same method",
            self::CODE_308 => "Permanent Redirect: The resource has been permanently changed to the URL in the Location header. The new request must use the same method",

            self::CODE_400 => "Bad Request: The server cannot or will not process the request due to a perceived client error",
            self::CODE_401 => "Unauthorized: The user requires authentication to make this request, it is either missing or invalid",
            self::CODE_403 => "Forbidden: The user is authenticated, but does not have permission to make this request",
            self::CODE_404 => "Not Found: The requested URL is not recognised or the resource is not available",
            self::CODE_405 => "Method Not Allowed: The method used for this request is not valid",
            self::CODE_408 => "Request Timeout: The server has shut down the connection after idle timeout",
            self::CODE_409 => "Conflict: The request body is shown to conflict with the version stored on the server",
            self::CODE_411 => "Length Required: The request requires the Content-Length header and was not given one",
            self::CODE_413 => "Payload Too Large: The request body is too large",
            self::CODE_415 => "Unsupported Media Type: The given Content-Type or Content-Encoding header is not accepted by the server",
            self::CODE_416 => "Range Not Satisfiable: The given Range header is outside of the expected range of the resource",
            self::CODE_418 => "I'm a Teapot: The server refuses to brew coffee becuase it is, permanently, a teapot",
            self::CODE_422 => "Unprocessable Entity: The request body was well-formed but could not be correctly processed by the server",
            self::CODE_429 => "Too Many Requests: The user has sent too many requests within a given time",
            self::CODE_451 => "Unavailable For Legal Reasons: The requested resource could not be legally provided",

            self::CODE_500 => "Internal Server Error: The request method is not supported by the server and cannot be handled",
            self::CODE_501 => "Not Implemented: The request method is not supported by the server and cannot be handled",
            self::CODE_502 => "Bad Gateway: The server, acting as a gateway, received an invalid response from the application it was attempting to connect to",
            self::CODE_503 => "Service Unavailable: The server is not ready to handle requests because it is either down or busy",
            self::CODE_504 => "Gateway Timeout: The server, acting as a gateway, did not receive a response from the application in time",
            self::CODE_505 => "HTTP Version Not Supported: The HTTP version used in the request is not supported by the server",
            self::CODE_508 => "Loop Detected: The server detected an infinite loop while processing the request"
        };
    }
}
