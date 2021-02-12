<?php

namespace Kentron\Template;

use Kentron\Entity\TransportEntity;

use Kentron\Template\Http\AController;
use Kentron\Template\Http\AMiddleware;

use Nyholm\Psr7\{Response, ServerRequest};
use Relay\Relay;

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
    public function reset(): void
    {
        $this->resetStores();
        $this->resetTransportEntity();
        $this->loadConfig();
        $this->loadVariables();
    }

    abstract public function resetStores(): void;

    final public function resetTransportEntity(): void
    {
        $this->transportEntity = new TransportEntity();
    }

    /**
     * Load in the config file
     *
     * @return void
     */
    abstract protected function loadConfig(): void;

    /**
     * Sets the variables in the Variable Store
     *
     * @return void
     */
    abstract protected function loadVariables(): void;

    /**
     * Set up the database connection
     *
     * @return void
     */
    abstract protected function bootOrm(): void;

    /**
     * Load in all the routes
     *
     * @return void
     */
    abstract protected function bootRouter(): void;

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
    final protected function getController(string $controllerClass, string $method, ?string $name = null): callable
    {
        $transportEntity = $this->transportEntity;

        return function (ServerRequest $request, Response $response, array $args) use ($transportEntity, $controllerClass, $method, $name)
        {
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

    /**
     * Gets a middleware class and returns the closure for Slim to use
     *
     * @param string $middlewareClass The FQDN of the middleware class
     *
     * @return callable The closure
     */
    final protected function getMiddleware(string $middlewareClass): callable
    {
        $transportEntity = $this->transportEntity;

        return function (ServerRequest $request, Relay $requestHandler) use ($transportEntity, $middlewareClass)
        {
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
