<?php

namespace REW\Api\Internal;

use Psr\Http\Message\ServerRequestInterface;
use REW\Api\Internal\Exception\NotFoundException;
use REW\Api\Internal\Exception\ServerErrorException;
use REW\Api\Internal\Exception\ServerSuccessException;
use REW\Core\Interfaces\ContainerInterface;
use Slim\Slim;

/**
 * Route Slim Requests to a Controller File
 */
class Router
{

    /**
     * @var Slim
     */
    protected $app;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param Slim $app
     * @param ContainerInterface $container
     */
    public function __construct(Slim $app, ContainerInterface $container)
    {
        $this->app = $app;
        $this->container = $container;
    }

    /**
     * Render the Requested Route Controller
     *
     * @param string $controllerClass
     * @param array $args
     */
    public function render($controllerClass, $args = [])
    {
        // Get Current Slim Route's Method + Pattern
        $server = $this->container->get(ServerRequestInterface::class)->getServerParams();

        try {
            // Locate and Run the Controller
            if (empty($controllerClass)) {
                throw new NotFoundException('Invalid route requested - the route\'s controller has not been mapped.');
            } else if (class_exists($controllerClass)) {
                // Load and Invoke the controller
                call_user_func_array(
                    $this->container->get($controllerClass),
                    [$this->app->request, $this->app->response, $args]
                );
            } else {
                throw new NotFoundException('Invalid route requested - the route\'s controller is mapped but does not exist.');
            }
        } catch (ServerErrorException $e) {
            $httpCode = $e->getHttpCode();
            $httpCode = $httpCode ?: -1;
            $this->app->halt($httpCode, json_encode([
                'error' => [
                    'code'    => $e->getCode(),
                    'message' => $e->getMessage(),
                    'type'    => $e->getType(),
                ],
            ]));
        } catch (ServerSuccessException $e) {
            $httpCode = $e->getHttpCode();
            $httpCode = $httpCode ?: -1;
            $this->app->halt($httpCode, json_encode([
                'success' => [
                    'code'    => $e->getCode(),
                    'message' => $e->getMessage(),
                    'type'    => $e->getType(),
                ],
            ]));
        }
    }

}