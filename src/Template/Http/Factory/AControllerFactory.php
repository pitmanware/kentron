<?php
declare(strict_types=1);

namespace Kentron\Template\Http\Factory;

use Kentron\Template\Http\Controller\AController;
use Kentron\Template\Store\Local;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
    final protected static function getController(string $controllerClass, string $method): callable
    {
        return function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($controllerClass, $method)
        {
            $transportEntity = Local::$transportEntity;

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
            return $transportEntity->respond();
        };
    }
}
