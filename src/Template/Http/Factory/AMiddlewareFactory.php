<?php
declare(strict_types=1);

namespace Kentron\Template\Http\Factory;

use \RuntimeException;

use Kentron\Template\Http\AMiddleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

abstract class AMiddlewareFactory
{
    use TGetTransportEntity;

    /**
     * Gets a middleware class and returns the closure for Slim to use
     *
     * @param string $middlewareClass The FQDN of the middleware class
     *
     * @return callable The closure
     */
    final protected static function getMiddleware(string $middlewareClass): callable
    {
        return function (ServerRequestInterface $request, RequestHandlerInterface $requestHandler) use ($middlewareClass)
        {
            $transportEntity = static::getTransportEntity();
            $middleware = new $middlewareClass();

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
