<?php

/**
 * Handle IP if running via proxy
 */
if (isset($_SERVER['HTTP_X_REAL_IP'])) {
    $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_IP'];
}

// API config
define('API_VERSION', 'v1');

// Start time
$time_start = microtime(true);

// Require Composer Vendor Auto loader
require_once $_SERVER['DOCUMENT_ROOT'] . '/../boot/app.php';

// Require module
if (empty(Settings::getInstance()->MODULES['REW_CRM_API'])) {
    header('Location: ' . Settings::getInstance()->SETTINGS['URL']);
    exit;
}

// UTC Timezone
date_default_timezone_set('UTC');

// Create app
$app = new \Slim\ModifiedSlim(array(
    'view'              => new \Slim\View\JsonView(),
    'templates.path'    => './app',
    'time.start'        => $time_start,
));

// Hooks
$app->hook('app.halt', function ($halt) use ($app) {
    $app->halt($halt['status'], json_encode(array(
        'error' => array(
            'type' => $halt['type'],
            'message' => $halt['message'],
            'code' => (isset($halt['code']) ? $halt['code'] : -1),
        ),
    )));
});

// Hooks
$app->hook('slim.before', function () use ($app) {
    $app->response()->header('Content-Type', 'application/json');

    // Validate request
    $request = $app->request();
    $headers = $request->headers();
    $valid = false;

    $content_type = $request->getContentType();
    if ($content_type === 'application/json; charset=utf-8') {
        $_POST = json_decode($request->getBody(), true);
    }

    // Fetch Application by API key
    try {
        $db = DB::get('users');
        if ($application = $db->{'api_applications'}->search(array('$eq' => array('api_key' => $headers['X_REW_API_KEY'], 'enabled' => 'Y')))->fetch()) {
            $app->config('api.application', $application);
            $valid = true;
        }
    } catch (Exception $ex) {
        Log::error($ex);
    }

    // Require valid request
    if (!$valid) {
        $app->applyHook('app.halt', array(
            'status' => 401,
            'type' => 'invalid_request',
            'message' => 'The API key provided is not valid',
        ));
    }

    // Gated feature that must be purchased
    if (!Settings::getInstance()->MODULES['REW_ZAPIER_APP']
        && false !== (strpos($request->getUserAgent(), 'Zapier'))) {
            $app->applyHook('app.halt', array(
                'status' => 401,
                'type' => 'invalid_request',
                'message' => 'The REW CRM domain has not been enabled for Zapier.',
            ));
    }
});

// Log request
$app->hook('slim.after', function () use ($app) {

    // Require a valid application
    if (empty($api_application = $app->config('api.application'))) {
        return;
    }

    $db = DB::get('users');
    $request = $app->request();
    $response = $app->response();
    $time_start = $app->config('time.start');
    $duration = microtime(true) - $time_start;
    $app_status = $response->status();
    $request_status = 'ok';

    // Check response status
    if (in_array($app_status, array(200, 204, 404))) {
        $api_application['num_requests_ok'] = intval($api_application['num_requests_ok']) + 1;
    } else {
        $api_application['num_requests_error'] = intval($api_application['num_requests_error']) + 1;
        $request_status = 'error';
    }

    // Update app record
    $db->{'api_applications'}->update(array(
        'num_requests_ok' => $api_application['num_requests_ok'],
        'num_requests_error' => $api_application['num_requests_error'],
    ), array(
        '$eq' => array(
            'id' => $api_application['id'],
        ),
    ));

    // Log data
    $log_data = array(
        'app_id'        => $api_application['id'],
        'method'        => $request->getMethod(),
        'uri'           => $_SERVER['REQUEST_URI'],
        'get'           => (!empty($_GET) ? json_encode($_GET) : null),
        'post'          => (!empty($_POST) ? json_encode($_POST) : null),
        'headers'       => json_encode($request->headers()->all()),
        'status'        => $request_status,
        'response'      => ($request_status === 'error' ? $response->body() : ''),
        'user_agent'    => ($request->getUserAgent() ? $request->getUserAgent() : ''),
        'ip'            => $request->getIp(),
        'duration'      => $duration,
    );

    // Insert log entry
    $db->{'api_requests'}->insert($log_data);
});

// Ping
$app->get('/' . API_VERSION . '/ping', function () use ($app) {
    $time_start = $app->config('time.start');
    $duration = microtime(true) - $time_start;
    echo json_encode(array(
        'ttfb' => $duration,
        'timestamp' => time()
    ));
});

// Hooks
$app->post('/' . API_VERSION . '/hooks', function () use ($app) {
    $app->render(API_VERSION . '/hooks/trigger.php', array(
        'app' => $app,
    ));
});

// Agents
$app->get('/' . API_VERSION . '/agents', function () use ($app) {
    $app->render(API_VERSION . '/agents/list.php', array(
        'app' => $app,
    ));
});

// Groups
$app->get('/' . API_VERSION . '/groups', function () use ($app) {
    $app->render(API_VERSION . '/groups/list.php', array(
        'app' => $app,
    ));
});

$app->post('/' . API_VERSION . '/groups', function () use ($app) {
    $app->render(API_VERSION . '/groups/create.php', array(
        'app' => $app,
    ));
});

$app->get('/' . API_VERSION . '/groups/:id', function ($id) use ($app) {
    $app->render(API_VERSION . '/groups/group.php', array(
        'app' => $app,
        'id' => $id,
    ));
});

$app->delete('/' . API_VERSION . '/groups/:id', function ($id) use ($app) {
    $app->render(API_VERSION . '/groups/delete.php', array(
        'app' => $app,
        'id' => $id,
    ));
});

// Leads
$app->post('/' . API_VERSION . '/leads', function () use ($app) {
    $app->render(API_VERSION . '/leads/upsert.php', array(
        'app' => $app,
    ));
});

$app->get('/' . API_VERSION . '/leads/:email', function ($email) use ($app) {
    $app->render(API_VERSION . '/leads/lead.php', array(
        'app' => $app,
        'email' => $email,
    ));
});

$app->post('/' . API_VERSION . '/leads/:email', function ($email) use ($app) {
    $app->render(API_VERSION . '/leads/upsert.php', array(
        'app' => $app,
        'email' => $email,
    ));
});

$app->put('/' . API_VERSION . '/leads', function () use ($app) {
    $app->render(API_VERSION . '/leads/upsert.php', array(
        'app' => $app,
    ));
});

// Lead Favorites
$app->get('/' . API_VERSION . '/leads/:email/favorites', function ($email) use ($app) {
    $app->render(API_VERSION . '/leads/favorites/list.php', array(
        'app' => $app,
        'email' => $email,
    ));
});

$app->post('/' . API_VERSION . '/leads/:email/favorites', function ($email) use ($app) {
    $app->render(API_VERSION . '/leads/favorites/create.php', array(
        'app' => $app,
        'email' => $email,
    ));
});

$app->get('/' . API_VERSION . '/leads/:email/favorites/:id', function ($email, $id) use ($app) {
    $app->render(API_VERSION . '/leads/favorites/favorite.php', array(
        'app' => $app,
        'email' => $email,
        'id' => $id,
    ));
});

$app->delete('/' . API_VERSION . '/leads/:email/favorites/:id', function ($email, $id) use ($app) {
    $app->render(API_VERSION . '/leads/favorites/delete.php', array(
        'app' => $app,
        'email' => $email,
        'id' => $id,
    ));
});

// Lead History Events
$app->post('/' . API_VERSION . '/leads/:email/events', function ($email) use ($app) {
    $app->render(API_VERSION . '/leads/events/create.php', array(
        'app' => $app,
        'email' => $email,
    ));
});

// Lead Saved Searches
$app->get('/' . API_VERSION . '/leads/:email/searches', function ($email) use ($app) {
    $app->render(API_VERSION . '/leads/searches/list.php', array(
        'app' => $app,
        'email' => $email,
    ));
});

$app->post('/' . API_VERSION . '/leads/:email/searches', function ($email) use ($app) {
    $app->render(API_VERSION . '/leads/searches/create.php', array(
        'app' => $app,
        'email' => $email,
    ));
});

$app->get('/' . API_VERSION . '/leads/:email/searches/:id', function ($email, $id) use ($app) {
    $app->render(API_VERSION . '/leads/searches/search.php', array(
        'app' => $app,
        'email' => $email,
        'id' => $id,
    ));
});

$app->delete('/' . API_VERSION . '/leads/:email/searches/:id', function ($email, $id) use ($app) {
    $app->render(API_VERSION . '/leads/searches/delete.php', array(
        'app' => $app,
        'email' => $email,
        'id' => $id,
    ));
});

$app->post('/' . API_VERSION . '/leads/:email/searches/:id', function ($email, $id) use ($app) {
    $app->render(API_VERSION . '/leads/searches/update.php', array(
        'app' => $app,
        'email' => $email,
        'id' => $id,
    ));
});

$app->post('/' . API_VERSION . '/leads/:email/notes', function ($email) use ($app) {
    $app->render(API_VERSION . '/leads/notes/create.php', array(
        'app' => $app,
        'email' => $email,
    ));
});

// Group Of Functions That Are Only Available To REW
if (Settings::isREW()) {
    $app->get('/' . API_VERSION . '/leads/searches/instant/:feed/:id(/:limit)', function ($feed, $id, $limit = false) use ($app) {
        $app->render(API_VERSION . '/leads/searches/instant.php', array(
            'app' => $app,
            'feed' => $feed,
            'id' => $id,
            'limit' => $limit
        ));
    });

    $app->post('/' . API_VERSION . '/leads/searches/email_results/:feed', function ($feed) use ($app) {
        $app->render(API_VERSION . '/leads/searches/email_results.php', array(
            'app' => $app,
            'feed' => $feed
        ));
    });
}

// 404
$app->notFound(function () use ($app) {
    $app->applyHook('app.halt', array(
        'status' => 404,
        'type' => 'invalid_request',
        'message' => 'The specified API resource does not exist',
    ));
});

// JSON exception handler
$app->add(new \Slim\Middleware\JsonExceptions());

// Run app
$app->run();
