<?php

namespace Kentron\Entity;

use Kentron\Entity\Template\AEntity;

use Slim\Http\{Request,Response};

final class TransportEntity extends AEntity
{
    /**
     * The arguments for the controller
     *
     * @var array
     */
    private $args = [];

    /**
     * The body to be rendered on response
     *
     * @var string
     */
    private $body;

    /**
     * The data to be put in the body
     *
     * @var mixed
     */
    private $data;

    /**
     * The default content type for the response header
     *
     * @var string
     */
    private $defaultContentType = "application/json";

    /**
     * The status of the response
     *
     * @var bool
     */
    private $failed;

    /**
     * The headers of the response
     *
     * @var array
     */
    private $headers = [];

    /**
     * Flag to determine if the response should be json encoded
     *
     * @var bool
     */
    private $jsonEncode = true;

    /**
     * The next function to be called by middleware
     *
     * @var object
     */
    private $next;

    /**
     * Any GET or POST parameters
     *
     * @var array
     */
    private $queryParameters = [];

    /**
     * Whether the response should be sent or not
     *
     * @var bool
     */
    private $quiet = false;

    /**
     * Used for front end routing
     *
     * @var string|null
     */
    private $redirect = null;

    /**
     * The Slim Request class
     *
     * @var Request
     */
    private $request;

    /**
     * The body of the request
     *
     * @var string|null
     */
    private $requestBody;

    /**
     * The URL of the request
     *
     * @var string
     */
    private $requestUrl;

    /**
     * The Slim Response class
     *
     * @var Response
     */
    private $response;

    /**
     * The name of the Slim route
     *
     * @var string|null
     */
    private $routeName;

    /**
     * The HTTP status code of the response
     *
     * @var int
     */
    private $statusCode = 200;

    /**
     * Sets the default content type on instanitation
     */
    public function __construct ()
    {
        $this->headers["content-type"] = $this->defaultContentType;
        $this->headers["cache-control"] = "max-age=300, must-revalidate";
    }

    /**
     * Getters
     */

    public function getArgs (): array
    {
        return $this->args;
    }

    public function getData ()
    {
        return $this->data;
    }

    public function getQueryParameters (): array
    {
        return $this->queryParameters;
    }

    public function getStatusCode (): ?int
    {
        return $this->statusCode;
    }

    public function &getRequest (): Request
    {
        return $this->request;
    }

    public function getRequestBody (): ?string
    {
        return $this->requestBody;
    }

    public function getRequestUrl (): string
    {
        return $this->requestUrl;
    }

    public function &getResponse (): Response
    {
        return $this->response;
    }

    public function getRouteName (): ?string
    {
        return $this->routeName;
    }

    public function hasFailed (): bool
    {
        return $this->failed ?? count($this->getErrors()) > 0;
    }

    public function iterateHeaders (): iterable
    {
        foreach ($this->headers as $header => $value) {
            yield $header => $value;
        }
    }

    /**
     * Gets the body of the response, defaults to json encoded
     *
     * @return string
     */
    public function getBody (): ?string
    {
        if ($this->quiet) {
            return null;
        }

        if (empty($this->body)) {
            if ($this->jsonEncode) {
                $this->body = json_encode([
                    "failed" => $this->hasFailed(),
                    "data" => $this->getData(),
                    "alerts" => $this->normaliseAlerts(),
                    "redirect" => $this->redirect
                ]);
            }
        }

        return $this->body;
    }

    /**
     * Setters
     */

    public function setArgs (array $args): void
    {
        $this->args = $args;
    }

    public function setBody (?string $body = null): void
    {
        $this->body = $body;
    }

    public function setContentType (string $contentType): void
    {
        switch ($contentType) {
            case $this->defaultContentType:
                $this->jsonEncode = true;
                break;
        }

        $this->headers["content-type"] = $contentType;
    }

    public function setHtml (): void
    {
        $this->setContentType("text/html");
    }

    public function setData ($data): void
    {
        $this->data = $data;
    }

    public function setFailed (bool $failed = true): void
    {
        $this->failed = $failed;
    }

    public function setNext (object $next): void
    {
        $this->next = $next;
    }

    public function setStatusCode (int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    public function setRedirect (string $redirect): void
    {
        $this->redirect = $redirect;
    }

    public function setRequest (Request &$request): void
    {
        $this->request = $request;
        $this->requestUrl = $this->request->getUri()->getPath();
        $this->requestBody = $this->request->getBody()->getContents() ?: null;
        $this->queryParameters = $this->request->getQueryParams();
        $this->routeName = $this->request->getAttribute("route")->getName() ?? null;
        $this->request->getBody()->rewind();
    }

    public function setResponse (Response &$response): void
    {
        $this->response = $response;
    }

    public function setUnauthorised (): void
    {
        $this->setStatusCode(401);
    }

    /**
     * Helpers
     */

    /**
     * Renders the body
     *
     * @return void
     */
    final public function renderResponse (): void
    {
        $this->response = $this->response->withStatus($this->statusCode);

        foreach ($this->iterateHeaders() as $header => $value) {
            $this->response = $this->response->withHeader($header, $value);
        }

        $this->response->getBody()->write($this->getBody());
    }

    /**
     * Calls the next function in the middleware stack
     *
     * @return void
     */
    final public function next (): void
    {
        $next = $this->next;
        $next($this->request, $this->response);
    }

    /**
     * Disables JSON output, used for GET requests
     *
     * @param boolean $quiet
     *
     * @return void
     */
    final public function disableOutput (bool $quiet = true): void
    {
        $this->quiet = $quiet;
    }

    final public function redirect (string $url): void
    {
        $this->statusCode = 302;
        $this->headers["Location"] = $url;
    }

    final public function hasParameters (): bool
    {
        return !empty($this->queryParameters);
    }
}
