<?php
declare(strict_types=1);

namespace Kentron\Enum;

enum EStatusCode: int
{
    /**
     * #### 100 - Continue
     * Used to respond to a HEAD request to validate headers before sending the body
     */
    case Code100 = 100;

    /**
     * #### 200 - OK
     * Everything was successful
     */
    case Code200 = 200;
    /**
     * #### 201 - Created
     * The server successfully created the resource from a given PUT/POST
     */
    case Code201 = 201;
    /**
     * #### 202 - Accepted
     * The server successfully recieved the data from a PUT/POST but has not yet processed it
     */
    case Code202 = 202;
    /**
     * #### 203 - Non-Authorative Information
     * The response data was collected from a local or third-party copy, and not from the requested server
     */
    case Code203 = 203;
    /**
     * #### 204 - No Content
     * Mostly used for PUT requests where no response body is expected, such as 'save' forms on UI
     */
    case Code204 = 204;
    /**
     * #### 205 - Reset Content
     * Tells the client that it needs to reload the document to update the UI
     */
    case Code205 = 205;
    /**
     * #### 206 - Partial Content
     * The server is only responding a part of the total resource, as specified by the Range header from the client
     */
    case Code206 = 206;

    /**
     * #### 300 - Multiple Choices
     * Indicates that there are multiple available responses and the user needs to choose one
     */
    case Code300 = 300;
    /**
     * #### 301 - Moved Permanently
     * Indicates that the resource has been permanently changed to the URL specified by the Location header
     * After first use by the browser, any subsequent requests will automatically redirect before hitting the server
     */
    case Code301 = 301;
    /**
     * #### 302 - Found
     * Indicates that the resource has been temporarily changed to the URL specified by the Location header
     * It is expected that the request could be made multiple times with different redirects
     */
    case Code302 = 302;
    /**
     * #### 303 - See Other
     * The server cannot supply the requested information, but provides a GET URL to where it can be retreived using the Location header
     */
    case Code303 = 303;
    /**
     * #### 304 - Not Modified
     * Indicates an implicit redirection to a cached resource for GET and HEAD requests
     */
    case Code304 = 304;
    /**
     * #### 307 - Temporary Redirect
     * Indicates that the resource has been permanently changed to the URL specified by the Location header
     * The new request method must match that of the original: GET->GET, POST->POST etc.
     * After first use by the browser, any subsequent requests will automatically redirect before hitting the server
     */
    case Code307 = 307;
    /**
     * #### 308 - Permanent Redirect
     * Indicates that the resource has been temporarily changed to the URL specified by the Location header
     * The new request method must match that of the original: GET->GET, POST->POST etc.
     * It is expected that the request could be made multiple times with different redirects
     */
    case Code308 = 308;

    /**
     * #### 400 - Bad Request
     * The server cannot or will not process the request due to something that is perceived to be a client error
     */
    case Code400 = 400;
    /**
     * #### 401 - Unauthorised
     * The user is attempting to make a request that requires authentication that they are not providing or the authentication data is invalid
     */
    case Code401 = 401;
    /**
     * #### 403 - Forbidden
     * The user is authenticated, but does not have permission to make this request
     */
    case Code403 = 403;
    /**
     * #### 404 - Not Found
     * The requested URL is not recognised or the resource is not available
     */
    case Code404 = 404;
    /**
     * #### 405 - Method Not Allowed
     * The method used for this request is not valid
     */
    case Code405 = 405;
    /**
     * #### 408 - Request Timeout
     * The server is shutting the connection after idle timeout
     */
    case Code408 = 408;
    /**
     * #### 409 - Conflict
     * The request body of a PUT or POST is shown to conflict with the version stored on the server, useful for state machines
     */
    case Code409 = 409;
    /**
     * #### 411 - Length Required
     * The request requires the Content-Length header and was not given one
     */
    case Code411 = 411;
    /**
     * #### 413 - Payload Too Large
     * The request is too large for the server to handle
     */
    case Code413 = 413;
    /**
     * #### 415 - Unsupported Media Type
     * The request Content-Type or Content-Encoding header is not accepted by the server
     */
    case Code415 = 415;
    /**
     * #### 416 - Range Not Satisfiable
     * The request Range header is outside of the expected range of the resource
     */
    case Code416 = 416;
    /**
     * #### 418 - I'm a Teapot
     * The server refuses to brew coffee becuase it is, permanently, a teapot
     */
    case Code418 = 418;
    /**
     * #### 422 - Unprocessable Entity
     * The request body was well-formed but could not be correctly processed by the server
     */
    case Code422 = 422;
    /**
     * #### 429 - Too Many Requests
     * The user has sent too many requests within a given time
     */
    case Code429 = 429;
    /**
     * #### 451 - Unavailable For Legal Reasons
     * The requested resource could not be legally provided
     */
    case Code451 = 451;

    /**
     * #### 500 - Internal Server Error
     * The server has encountered a situation it does not know how to handle
     */
    case Code500 = 500;
    /**
     * #### 501 - Not Implemented
     * The request method is not supported by the server and cannot be handled
     */
    case Code501 = 501;
    /**
     * #### 502 - Bad Gateway
     * The server, acting as a gateway, received an invalid response from the application it was attempting to connect to
     */
    case Code502 = 502;
    /**
     * #### 503 - Service Unavailable
     * The server is not ready to handle requests because it is either down or busy
     */
    case Code503 = 503;
    /**
     * #### 504 - Gateway Timeout
     * The server, acting as a gateway, did not receive a response from the application in time
     */
    case Code504 = 504;
    /**
     * #### 505 - HTTP Version Not Supported
     * The HTTP version used in the request is not supported by the server
     */
    case Code505 = 505;
    /**
     * #### 508 - Loop Detected
     * The server detected an infinite loop while processing the request
     */
    case Code508 = 508;

    /**
     * Check if the code is in the 1xx or 2xx blocks
     *
     * @return bool
     */
    public function codeIndicatesSuccess(): bool
    {
        return match($this) {
            self::Code100,
            self::Code200,
            self::Code201,
            self::Code202,
            self::Code203,
            self::Code204,
            self::Code205,
            self::Code206 => true,
            default => false
        };
    }

    /**
     * Checks if the code is in the 4xx block
     *
     * @return bool
     */
    public function codeIndicatesClientError(): bool
    {
        return match($this) {
            self::Code400,
            self::Code401,
            self::Code403,
            self::Code404,
            self::Code405,
            self::Code408,
            self::Code409,
            self::Code411,
            self::Code413,
            self::Code415,
            self::Code416,
            self::Code418,
            self::Code422,
            self::Code429,
            self::Code451 => true,
            default => false
        };
    }

    /**
     * Checks if the code is in the 5xx block
     *
     * @return bool
     */
    public function codeIndicatesServerError(): bool
    {
        return match($this) {
            self::Code500,
            self::Code501,
            self::Code502,
            self::Code503,
            self::Code504,
            self::Code505,
            self::Code508 => true,
            default => false
        };
    }

    /**
     * Checks if the code requires the Location header to be set
     *
     * @return bool
     */
    public function codeRequiresLocation(): bool
    {
        return match($this) {
            self::Code301,
            self::Code302,
            self::Code303,
            self::Code307,
            self::Code308 => true,
            default => false
        };
    }

    /**
     * Get the error or notification message relating to this code
     *
     * @return string
     */
    public function getMessage(): string
    {
        return match($this) {
            self::Code100 => "Continue: The header check was successful, the server is now ready to receive data",

            self::Code200 => "OK: The request succeeded normally",
            self::Code201 => "Created: The request succeeded and a new resource was created",
            self::Code202 => "Accepted: The request has been received but has not yet been acted upon",
            self::Code203 => "Non-Authoritative Information: The server returned transformed data from a non-origin location",
            self::Code204 => "No Content: The request succeeded and does not need to return data, and therefore has not",
            self::Code205 => "Reset Content: The request succeeded and asks the client to reload its view",
            self::Code206 => "Partial Content: The request succeeded and is only returning part of the total resource as detailed by the Range header from the client",

            self::Code300 => "Multiple Choices: There are multiple available responses and the user must choose one",
            self::Code301 => "Moved Permanently: The resource has been permanently changed to the URL in the Location header",
            self::Code302 => "Found: The resource has been temporarily changed to the URL in the Location header",
            self::Code303 => "See Other: The resource could not be supplied, but provides a URL in the Location header to where it can be requested",
            self::Code304 => "Not Modified: The response has not been modified and the client should continue using the cached redirect",
            self::Code307 => "Temporary Redirect: The resource has been temporarily changed to the URL in the Location header. The new request must use the same method",
            self::Code308 => "Permanent Redirect: The resource has been permanently changed to the URL in the Location header. The new request must use the same method",

            self::Code400 => "Bad Request: The server cannot or will not process the request due to a perceived client error",
            self::Code401 => "Unauthorized: The user requires authentication to make this request, it is either missing or invalid",
            self::Code403 => "Forbidden: The user is authenticated, but does not have permission to make this request",
            self::Code404 => "Not Found: The requested URL is not recognised or the resource is not available",
            self::Code405 => "Method Not Allowed: The method used for this request is not valid",
            self::Code408 => "Request Timeout: The server has shut down the connection after idle timeout",
            self::Code409 => "Conflict: The request body is shown to conflict with the version stored on the server",
            self::Code411 => "Length Required: The request requires the Content-Length header and was not given one",
            self::Code413 => "Payload Too Large: The request body is too large",
            self::Code415 => "Unsupported Media Type: The given Content-Type or Content-Encoding header is not accepted by the server",
            self::Code416 => "Range Not Satisfiable: The given Range header is outside of the expected range of the resource",
            self::Code418 => "I'm a Teapot: The server refuses to brew coffee becuase it is, permanently, a teapot",
            self::Code422 => "Unprocessable Entity: The request body was well-formed but could not be correctly processed by the server",
            self::Code429 => "Too Many Requests: The user has sent too many requests within a given time",
            self::Code451 => "Unavailable For Legal Reasons: The requested resource could not be legally provided",

            self::Code500 => "Internal Server Error: The request method is not supported by the server and cannot be handled",
            self::Code501 => "Not Implemented: The request method is not supported by the server and cannot be handled",
            self::Code502 => "Bad Gateway: The server, acting as a gateway, received an invalid response from the application it was attempting to connect to",
            self::Code503 => "Service Unavailable: The server is not ready to handle requests because it is either down or busy",
            self::Code504 => "Gateway Timeout: The server, acting as a gateway, did not receive a response from the application in time",
            self::Code505 => "HTTP Version Not Supported: The HTTP version used in the request is not supported by the server",
            self::Code508 => "Loop Detected: The server detected an infinite loop while processing the request"
        };
    }
}
