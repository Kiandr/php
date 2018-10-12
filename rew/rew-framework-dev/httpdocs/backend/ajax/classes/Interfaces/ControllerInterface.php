<?php

namespace REW\Api\Internal\Interfaces;

use Slim\Http\Response;
use Slim\Http\Request;
use Slim\Slim;

/**
 * Controller Interface
 * @package REW\Api\Internal\Controller
 */
interface ControllerInterface
{
    /**
     * Invoke Endpoint Controller
     *
     * @param Request $request
     * @param Response $response
     * @param array $routeParams
     * @return array
     */
    public function __invoke(Request $request, Response $response, $routeParams = []);
}
