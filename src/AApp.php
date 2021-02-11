<?php

namespace Kentron\Template;

use Kentron\Entity\TransportEntity;

use Kentron\Template\Http\AController;
use Kentron\Template\Http\AMiddleware;

use Nyholm\Psr7\{ServerRequest,Response};

/**
 * The inital application class, injected into the controllers
 */
abstract class AApp
{
    /**
     * Pseudo container for request and response
     *
     * @var TransportEntity
     */
    protected $transportEntity;

    /**
     * Set all things that allow for an app reset
     *
     * @return void
     */
    public function reset (): void
    {
        $this->loadConfig();
        $this->transportEntity = new TransportEntity();
        $this->setVariables();
        $this->bootOrm();
        $this->bootRouter();
    }

    /**
     * Load in the config file
     *
     * @return void
     */
    abstract protected function loadConfig (): void;

    /**
     * Set up the database connection
     *
     * @return void
     */
    abstract protected function bootOrm (): void;

    /**
     * Sets the variables in the Variable Store
     *
     * @return void
     */
    abstract protected function setVariables (): void;

    /**
     * Load in all the routes
     *
     * @return void
     */
    abstract protected function bootRouter (): void;

    /**
     * Protected Methods
     */

    /**
     * Returns a dynamic controller closure for Slim to use
     *
     * @param string $controllerClass The FQDN for a controller class
     * @param string $method          The method to call on the controller
     *
     * @return callable The closure
     */
    final protected function getController (string $controllerClass, string $method): callable
    {
        $transportEntity = $this->transportEntity;

        return function (ServerRequest $request, Response $response, array $args) use ($transportEntity, $controllerClass, $method)
        {
            $transportEntity->setRequest($request);
            $transportEntity->setResponse($response);
            $transportEntity->setArgs($args);

            $controller = new $controllerClass($transportEntity);

            if (!is_subclass_of($controller, AController::class)) {
                throw new \InvalidArgumentException("$controllerClass must be an instance of " . AController::class);
            }

            if (!method_exists($controller, $method) || !is_callable([$controller, $method])) {
                throw new \Error("Call to undefined method {$controllerClass}::{$method}");
            }

            if ($transportEntity->getStatusCode() === 200) {
                $controller->$method();
            }

            return $transportEntity->getResponse();
        };
    }

    /**
     * Gets a middleware class and returns the closure for Slim to use
     *
     * @param string $middlewareClass The FQDN of the middleware class
     *
     * @return callable The closure
     */
    final protected function getMiddleware (string $middlewareClass): callable
    {
        $transportEntity = $this->transportEntity;
        $middleware = new $middlewareClass();

        if (!is_subclass_of($middleware, AMiddleware::class)) {
            throw new \InvalidArgumentException("$middlewareClass must be an instance of " . AMiddleware::class);
        }

        return function (ServerRequest $request, Response $response, object $next) use ($transportEntity, $middleware)
        {
            $transportEntity->setRequest($request);
            $transportEntity->setResponse($response);
            $transportEntity->setNext($next);

            $middleware->run($transportEntity);

            return $transportEntity->getResponse();
        };
    }
}
