<?php
declare(strict_types=1);

namespace Kentron\Entity;

use Kentron\Support\Json;
use Kentron\Template\Entity\AEntity;

use Nyholm\Psr7\Response;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

class TransportEntity extends AEntity
{
    /** The arguments for the controller */
    protected array $args = [];

    /** The body to be rendered on response */
    protected string|null $body = null;

    /** The data to be put in the body */
    protected mixed $data = null;

    /** The default content type for the response header */
    protected string $defaultContentType = "application/json";

    /** The status of the response */
    protected bool $failed;

    /**
     * The headers of the response
     *
     * @var array<string,string>
     */
    protected array $headers = [];

    /** Flag to determine if the response should be json encoded */
    protected bool $jsonEncode = true;

    /** The next function to be called by middleware */
    protected RequestHandlerInterface $next;

    /** Any GET or POST parameters */
    protected array $queryParameters = [];

    /** Whether the response should be sent or not */
    protected bool $quiet = false;

    /** Used for front end routing */
    protected string|null $redirect = null;

    /** The Slim Request class */
    protected ServerRequestInterface $request;

    /** The body of the request */
    protected StreamInterface $requestBody;

    /** The URL of the request */
    protected string $requestUrl;

    /** The Slim ResponseInterface class */
    protected ResponseInterface $response;

    /** The HTTP status code of the response */
    protected int $statusCode = 200;

    /**
     * Cookie list to be added to the response
     *
     * @var string[]
     */
    protected array $cookies = [];

    /**
     * Sets the default content type on instanitation
     */
    public function __construct()
    {
        $this->response = new Response();
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

    public function &getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    public function getRequestBody(): StreamInterface
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

    public function &getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function hasFailed(): bool
    {
        return $this->failed ?? $this->hasErrors();
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
    public function getBody(): string
    {
        if ($this->quiet) {
            return "";
        }

        if (empty($this->body)) {
            if ($this->jsonEncode) {
                $this->body = Json::toString(
                    array_merge(
                        [
                            "failed" => $this->hasFailed(),
                            "data" => $this->getData(),
                            "redirect" => $this->redirect
                        ],
                        $this->normaliseAlerts()
                    )
                );
            }
        }

        return $this->body ?: "";
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

    public function setRequest(ServerRequestInterface &$request): void
    {
        global $_POST, $_COOKIE;

        $this->request = $request;
        $paramString = http_build_query($this->request->getQueryParams());

        $this->requestUrl = $this->request->getUri()->getPath() . (!$paramString ? "" : "?{$paramString}");
        $this->requestBody = $this->request->getBody();
        $this->queryParameters = $this->request->getQueryParams();
        $this->request->getBody()->rewind();

        if ($this->request->getMethod() === "POST") {
            $_POST = (array)$request->getParsedBody();
        }
        $_COOKIE = (array)$request->getCookieParams();
    }

    public function setResponse(ResponseInterface &$response): void
    {
        $this->response = $response;
    }

    public function setBadRequest(): void
    {
        $this->setStatusCode(400);
    }

    public function setUnauthorised(): void
    {
        $this->setStatusCode(401);
    }

    public function setInternalServerError(): void
    {
        $this->setStatusCode(500);
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
    final public function &respond(): ResponseInterface
    {
        $this->mergeRespond();
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

        $body = $this->response->getBody();
        $body->write($this->getBody() ?? "");
        $this->response = $this->response->withBody($body);
    }
}
