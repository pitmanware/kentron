<?php

namespace Kentron\Factory;

use Kentron\Store\Variable\AVariable;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;

abstract class AControllerFactory
{
    /**
     * Returns a dynamic controller closure for Slim to use
     *
     * @param string $controllerClass The FQDN for a controller class
     * @param string $method          The method to call on the controller
     *
     * @return callable The closure
     */
    final protected function getController(string $controllerClass, string $method, ?string $name = null): callable
    {
        return function (ServerRequest $request, Response $response, array $args) use ($controllerClass, $method, $name)
        {
            $transportEntity = AVariable::getTransportEntity();

            $transportEntity->setRouteName($name);
            $transportEntity->setRequest($request);
            $transportEntity->setResponse($response);
            $transportEntity->setArgs($args);

            $controller = new $controllerClass($transportEntity);

            if (!is_subclass_of($controller, AController::class)) {
                throw new \RuntimeException("$controllerClass must be an instance of " . AController::class);
            }

            if (!method_exists($controller, $method) || !is_callable([$controller, $method])) {
                throw new \RuntimeException("Call to undefined method {$controllerClass}::{$method}");
            }

            if ($transportEntity->getStatusCode() === 200) {
                $controller->$method();
            }

            return $transportEntity->getResponse();
        };
    }
}
