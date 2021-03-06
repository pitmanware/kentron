<?php

namespace Kentron\Factory;

use Kentron\Store\Variable\AVariable;
use Kentron\Template\Http\AMiddleware;
use Nyholm\Psr7\ServerRequest;
use Psr\Http\Server\RequestHandlerInterface;

abstract class AMiddlewareFactory
{
    /**
     * Gets a middleware class and returns the closure for Slim to use
     *
     * @param string $middlewareClass The FQDN of the middleware class
     *
     * @return callable The closure
     */
    final protected static function getMiddleware(string $middlewareClass): callable
    {
        return function (ServerRequest $request, RequestHandlerInterface $requestHandler) use ($middlewareClass)
        {
            $transportEntity = AVariable::getTransportEntity();
            $middleware = new $middlewareClass();

            if (!is_subclass_of($middleware, AMiddleware::class)) {
                throw new \RuntimeException("$middlewareClass must be an instance of " . AMiddleware::class);
            }

            $transportEntity->setRequest($request);
            $transportEntity->setNext($requestHandler);

            $middleware->run($transportEntity);

            return $transportEntity->getResponse();
        };
    }
}
