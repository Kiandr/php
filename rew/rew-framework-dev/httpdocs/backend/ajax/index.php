<?php

/**
 * Internal REW CRM API
 *
 * Handles internal requests
 * Uses active CRM session to process authentication checks
 */
use REW\Api\Internal\Config;
use REW\Api\Internal\Exception\NotFoundException;
use REW\Api\Internal\Exception\ServerErrorException;
use REW\Api\Internal\Router;
use Slim\Slim;

// Require Autoloaders
require_once __DIR__ . '/../../../boot/app.php';
define('TABLE_ACTIONPLANS', 'action_plans');
define('TABLE_USERS_ACTIONPLANS', 'users_action_plans');
define('TABLE_TASKS', 'tasks');

// Start time
$time_start = microtime(true);

// UTC Timezone
date_default_timezone_set('UTC');

// Load Slim App
$app = new Slim([
    'time.start' => $time_start,
]);

// API Config (Slim: Bindings, Hooks, Routing)
$container = Container::getInstance();
$container->set(Slim::class, $app);

$router = new Router($app, $container);
$config = new Config($app, $router, $container);
$config();

// 404
$app->notFound(function () use ($app) {
    try {
        throw new NotFoundException('The specified API resource does not exist');
    } catch (ServerErrorException $e) {
        $httpCode = $e->getHttpCode();
        $httpCode = $httpCode ?: -1;
        $app->halt($httpCode, json_encode([
            'error' => [
                'code'    => $e->getCode(),
                'message' => $e->getMessage(),
                'type'    => $e->getType(),
            ],
        ]));
    }
});

// Init Slim Routing
$app->run();
