<?php
declare(strict_types=1);

namespace Kentron\Template\Http\Factory;

use \RuntimeException;

use Kentron\Template\AClass;
use Kentron\Template\Http\AMiddleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class AMiddlewareFactory extends AClass
{
    use TGetTransportEntity;

    /**
     * Gets a middleware class and returns the closure for Slim to use
     *
     * @param string $middlewareClass The FQDN of the middleware class
     *
     * @return callable The closure
     */
    final protected function getMiddleware(string $middlewareClass): callable
    {
        return function (ServerRequestInterface $request, RequestHandlerInterface $requestHandler) use ($middlewareClass)
        {
            $transportEntity = $this->getTransportEntity();
            $middleware = new $middlewareClass($transportEntity);

            if (!is_subclass_of($middleware, AMiddleware::class)) {
                throw new RuntimeException("$middlewareClass must be an instance of " . AMiddleware::class);
            }

            $transportEntity->setRequest($request);
            $transportEntity->setNext($requestHandler);

            $middleware->run($transportEntity);

            return $transportEntity->getResponse();
        };
    }
}
