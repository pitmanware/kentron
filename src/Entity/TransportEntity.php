<?php

namespace Kentron\Entity;

use Kentron\Entity\Template\AEntity;

use Nyholm\Psr7\{Response, ServerRequest, Stream};
use Psr\Http\Server\RequestHandlerInterface;

class TransportEntity extends AEntity
{
    /**
     * The arguments for the controller
     *
     * @var array
     */
    protected $args = [];

    /**
     * The body to be rendered on response
     *
     * @var string
     */
    protected $body;

    /**
     * The data to be put in the body
     *
     * @var mixed
     */
    protected $data;

    /**
     * The default content type for the response header
     *
     * @var string
     */
    protected $defaultContentType = "application/json";

    /**
     * The status of the response
     *
     * @var bool
     */
    protected $failed;

    /**
     * The headers of the response
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Flag to determine if the response should be json encoded
     *
     * @var bool
     */
    protected $jsonEncode = true;

    /**
     * The PSR15 function to be called by middleware
     *
     * @var RequestHandlerInterface
     */
    protected $next;

    /**
     * Any GET or POST parameters
     *
     * @var array
     */
    protected $queryParameters = [];

    /**
     * Whether the response should be sent or not
     *
     * @var bool
     */
    protected $quiet = false;

    /**
     * Used for front end routing
     *
     * @var string|null
     */
    protected $redirect = null;

    /**
     * The PSR7 Request class
     *
     * @var ServerRequest
     */
    protected $request;

    /**
     * The body of the request
     *
     * @var Stream
     */
    protected $requestBody;

    /**
     * The URL of the request
     *
     * @var string
     */
    protected $requestUrl;

    /**
     * The PSR7 Response class
     *
     * @var Response
     */
    protected $response;

    /**
     * The name of the route
     *
     * @var string|null
     */
    protected $routeName;

    /**
     * The HTTP status code of the response
     *
     * @var int
     */
    protected $statusCode = 200;

    /**
     * Cookie list to be added to the response
     *
     * @var string[]
     */
    protected $cookies = [];

    /**
     * Sets the default content type on instanitation
     */
    public function __construct()
    {
        $this->headers["content-type"] = $this->defaultContentType;
        $this->headers["cache-control"] = "max-age=300, must-revalidate";
    }

    /**
     * Getters
     */

    public function getArgs(): array
    {
        return $this->args;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getQueryParameters(): array
    {
        return $this->queryParameters;
    }

    public function getStatusCode(): ?int
    {
        return $this->statusCode;
    }

    public function &getRequest(): ServerRequest
    {
        return $this->request;
    }

    public function getRequestBody(): Stream
    {
        return $this->requestBody;
    }

    public function getRequestBodyContent(): ?string
    {
        $body = $this->requestBody->getContents() ?: null;
        $this->requestBody->rewind();

        return $body;
    }

    public function getRequestUrl(): string
    {
        return $this->requestUrl;
    }

    public function &getResponse(): Response
    {
        return $this->response;
    }

    public function getRouteName(): ?string
    {
        return $this->routeName;
    }

    public function hasFailed(): bool
    {
        return $this->failed ?? count($this->getErrors()) > 0;
    }

    public function iterateHeaders(): iterable
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
    public function getBody(): ?string
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

    public function setArgs(array $args): void
    {
        $this->args = $args;
    }

    public function setBody(?string $body = null): void
    {
        $this->body = $body;
    }

    public function setContentType(string $contentType): void
    {
        switch ($contentType) {
            case $this->defaultContentType:
                $this->jsonEncode = true;
                break;
        }

        $this->headers["content-type"] = $contentType;
    }

    public function setHtml(): void
    {
        $this->setContentType("text/html");
    }

    public function setData($data): void
    {
        $this->data = $data;
    }

    public function setFailed(bool $failed = true): void
    {
        $this->failed = $failed;
    }

    public function setNext(RequestHandlerInterface $next): void
    {
        $this->next = $next;
    }

    /**
     * Disables JSON output, used for GET requests
     *
     * @param boolean $quiet
     *
     * @return void
     */
    public function setQuiet(bool $quiet = true): void
    {
        $this->quiet = $quiet;
    }

    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    public function setRedirect(string $redirect): void
    {
        $this->redirect = $redirect;
    }

    public function setRequest(ServerRequest &$request): void
    {
        global $_POST, $_COOKIE;

        $this->request = $request;

        $this->requestUrl = $this->request->getUri()->getPath();
        $this->requestBody = $this->request->getBody();
        $this->request->getBody()->rewind();
        $this->queryParameters = $this->request->getQueryParams();

        if ($this->request->getMethod() === "POST") {
            $_POST = (array)$request->getParsedBody();
        }
        $_COOKIE = (array)$request->getCookieParams();
    }

    public function setResponse(Response &$response): void
    {
        $this->response = $response;
    }

    public function setRouteName(?string $name = null): void
    {
        $this->routeName = $name;
    }

    public function setUnauthorised(): void
    {
        $this->setStatusCode(401);
    }

    public function addCookies(array $cookies): void
    {
        $this->cookies += $cookies;
    }

    /**
     * Helpers
     */

    /**
     * Builds the response
     *
     * @return void
     */
    final public function &respond(): Response
    {
        $this->mergeRespond();
        $this->response->getBody()->write($this->getBody() ?? "");
        return $this->response;
    }

    /**
     * Calls the next function in the middleware stack
     *
     * @return void
     */
    final public function next(): void
    {
        $this->response = $this->next->handle($this->request);
    }

    final public function redirect(string $url): void
    {
        $this->statusCode = 302;
        $this->headers["Location"] = $url;
    }

    final public function hasParameters(): bool
    {
        return !empty($this->queryParameters);
    }

    private function mergeRespond(): void
    {
        $this->response = $this->response->withStatus($this->statusCode);

        foreach ($this->iterateHeaders() as $header => $value) {
            $this->response = $this->response->withHeader($header, $value);
        }

        foreach ($this->cookies as $cookie) {
            $this->response = $this->response->withHeader("Set-Cookie", $cookie);
        }
    }
}
