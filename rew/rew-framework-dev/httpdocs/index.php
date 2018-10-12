<?php

use REW\Core\Interfaces\PageInterface;
use REW\Core\Interfaces\SettingsInterface;
use REW\Core\Interfaces\Http\HostInterface;
use REW\Core\Interfaces\User\SessionInterface;
use REW\Api\Controller\Idx\SearchController;
use REW\Api\Controller\Idx\FavoriteController;
use REW\Api\Controller\User\AuthController;
use REW\Api\Controller\Drivetime\SearchController as DrivetimeSearchController;
use REW\Api\Exception\Request\UnauthorizedRequestException;

// Output Buffer
ob_start();

// Include sql_connect.php
require_once __DIR__ . '/sql_connect.php';

// Include Helper Functions
require_once __DIR__ . '/helper_functions.php';

// Memory snapshot
Profile::memory()->snapshot('Page Start');

// Include IDX Configuration
require_once __DIR__ . '/idx/common.inc.php';

/** @todo put routes into a config file. */
// Route JSON pages, if necessary (known route is matched)
if (preg_match('/\/(idx|auth|drivetime)(.*)?\/(json)\//', $_SERVER['REQUEST_URI'])) {
    $slimApp = new \Slim\Slim([
        'debug' => false
    ]);

    // Authentication middleware
    $requireAuth = function ($app) {
        $userSession = $app->get(SessionInterface::class);
        if (!$userSession->isValid()) {
            throw new UnauthorizedRequestException;
        }
    };

    // Exception handling for API endpoints
    $slimApp->error(function (\Exception $e) use ($app) {

        // An API exception was caught - encode as JSON response
        if ($e instanceof \REW\Api\Exception\APIExceptionInterface) {
            echo json_encode(['error' => $e]);
            return;
        }

        // This isn't an anticipated error... construct the JSON on-the-fly
        $internalError = new \REW\Api\Exception\Server\InternalErrorException;

        // If dev environment, show error & debug info
        $httpHost = $app->get(HostInterface::class);
        if ($httpHost->isDev()) {
            $internalError->setErrors([$e]);
            $internalError->showDebug(true);
        }

        // Return JSON error response
        echo json_encode($internalError);

    });

    // API endpoint for searching IDX listings
    $slimApp->get('/idx/feed/:feed/json/', function ($feed) use ($slimApp, $app) {
        $searchController = $app->make(SearchController::class);

        $params = ['feed' => $feed] + $slimApp->request()->get();
        // Build out the listing request
        $listings = $searchController->getListings($params);

        // Return successfully response (200) with JSON encoded model
        $slimApp->response()->header('Content-Type', 'application/json');
        $slimApp->response()->write(json_encode($listings));
        $slimApp->response()->setStatus(200);

    });

    // API endpoint for searching Layers
    $slimApp->get('/idx/feed/:feed/layers/json/', function ($feed) use ($slimApp, $app) {
        $searchController = $app->make(SearchController::class);

        $params = ['feed' => $feed] + $slimApp->request()->get();
        // Build out the listing request
        $layers = $searchController->getLayers($params);

        // Return successfully response (200) with JSON encoded model
        $slimApp->response()->header('Content-Type', 'application/json');
        $slimApp->response()->write(json_encode($layers));
        $slimApp->response()->setStatus(200);

    });

    // API endpoint for searching onboard community info
    $slimApp->get('/idx/feed/:zip/community/json/', function ($zip) use ($slimApp, $app) {
        $searchController = $app->make(SearchController::class);

        $params = ['zip' => $zip] + $slimApp->request()->get();
        // Build out the listing request
        $layers = $searchController->getCommunityInfo($params);

        // Return successfully response (200) with JSON encoded model
        $slimApp->response()->header('Content-Type', 'application/json');
        $slimApp->response()->write(json_encode($layers));
        $slimApp->response()->setStatus(200);

    });

    // API endpoint for getting IDX panels
    $slimApp->get('/idx/feed/:feed/panels/json/', function ($feed) use ($slimApp, $app) {
        $panelController = $app->make(\REW\Api\Controller\Idx\PanelController::class);

        // Build out the listing request
        $panels = $panelController->getIdxPanels($params = ['feed' => $feed] + $slimApp->request()->get());

        // Return successfully response (200) with JSON encoded model
        $slimApp->response()->header('Content-Type', 'application/json');
        $slimApp->response()->write(json_encode($panels));
        $slimApp->response()->setStatus(200);

    });

    // API endpoint for getting count of results, given criteria
    $slimApp->get('/idx/feed/:feed/count/json/', function ($feed) use ($slimApp, $app) {
        $searchController = $app->make(SearchController::class);

        $params = ['feed' => $feed] + $slimApp->request()->get();
        $count = $searchController->getCount($params);

        $slimApp->response()->header('Content-Type', 'application/json');
        $slimApp->response()->write(json_encode($count));
        $slimApp->response()->setStatus(200);
    });

    // API endpoint for saving searches
    $slimApp->post('/idx/feed/:feed/save/json/', function ($feed) use ($slimApp, $app) {
        $searchController = $app->make(SearchController::class);
        $params = json_decode($slimApp->request()->getBody(), true);

        if (null === $params) {
            throw new \REW\Api\Exception\Request\BadRequestException();
        }

        $params = ['feed' => $feed] + $params;
        $saveSearch = $searchController->saveSearch($params);

        // Return successfully response (201) with JSON encoded model
        $slimApp->response()->header('Content-Type', 'application/json');
        $slimApp->response()->write(json_encode($saveSearch));
        $slimApp->response()->setStatus(201);
    });

    // API endpoint for updating searches
    $slimApp->post('/idx/feed/:feed/save/:id/json/', function ($feed, $id ) use ($slimApp, $app) {
        $searchController = $app->make(SearchController::class);
        $params = json_decode($slimApp->request()->getBody(), true);

        if (null === $params) {
            throw new \REW\Api\Exception\Request\BadRequestException();
        }

        $params['id'] = $id;
        $params = ['feed' => $feed] + $params;
        $updatedSearch = $searchController->updateSearch($params, $id);

        // Return successfully response (200) with JSON encoded model
        $slimApp->response()->header('Content-Type', 'application/json');
        $slimApp->response()->write(json_encode($updatedSearch));
        $slimApp->response()->setStatus(200);
    });

    // API endpoint for getting field info.
    $slimApp->get('/idx/feed/json/', function () use ($slimApp, $app) {
        $searchController = $app->make(SearchController::class);

        $feedInfo = $searchController->getFeedInfo();

        // Return successfully response (200) with JSON encoded model
        $slimApp->response()->header('Content-Type', 'application/json');
        $slimApp->response()->write(json_encode($feedInfo));
        $slimApp->response()->setStatus(200);
    });

    // API endpoint for fetching favourites for a user
    $slimApp->get('/idx/feed/:feed/favorites/json/', function ($feed) use ($slimApp, $app, $requireAuth) {

        // Must be logged in
        $requireAuth($app);

        // Load IDX favorite controller
        $favoriteController = $app->make(FavoriteController::class);

        // Build out the favorite request
        $favorite = $favoriteController->getFavorites(
            $slimApp->request()->get('user'),
            $feed
        );

        // Return successfully response (200) with JSON encoded model
        $slimApp->response()->header('Content-Type', 'application/json');
        $slimApp->response()->write(json_encode($favorite));
        $slimApp->response()->setStatus(200);
    });

    // API endpoint for creating favourites for a user.
    $slimApp->post('/idx/feed/:feed/favorites/json/', function ($feed) use ($slimApp, $app, $requireAuth) {

        // Must be logged in
        $requireAuth($app);

        // Load IDX favorite controller
        $favoriteController = $app->make(FavoriteController::class);

        // Process request to add favorite listing
        $body = json_decode($slimApp->request()->getBody(), true);
        $favorite = $favoriteController->addFavorite(
            $body['user'],
            $feed,
            $body['listingId'],
            $body['listingType']
        );

        // Return successfully response (201) with JSON encoded model
        $slimApp->response()->header('Content-Type', 'application/json');
        $slimApp->response()->write(json_encode($favorite));
        $slimApp->response()->setStatus(201);
    });

    // API endpoint to remove favourites for a user.
    $slimApp->delete('/idx/feed/:feed/favorites/json/', function ($feed) use ($slimApp, $app, $requireAuth) {

        // Must be logged in
        $requireAuth($app);

        // Load IDX favorite controller
        $favoriteController = $app->make(FavoriteController::class);

        // Process request to remove favorite listing
        $body = json_decode($slimApp->request()->getBody(), true);
        $favoriteController->removeFavorite(
            $body['user'],
            $feed,
            $body['listingId'],
            $body['listingType']
        );

        // Return successfully response (204) and no content
        $slimApp->response()->header('Content-Type', 'application/json');
        $slimApp->response()->setStatus(204);
    });

    // API endpoint for authorizing user
    $slimApp->get('/auth/me/json/', function () use ($slimApp, $app) {
        // Load IDX favorite controller
        $tokenController = $app->make(AuthController::class);

        // Process request to get authorization token
        $token = $tokenController->getToken();
        $slimApp->response()->header('Content-Type', 'application/json');
        $slimApp->response()->write(json_encode($token));
        $slimApp->response()->setStatus(200);
    });

    // API endpoint for creating favourites for a user.
    $slimApp->get('/drivetime/json/', function () use ($slimApp, $app, $requireAuth) {

        // Load IDX favorite controller
        $drivetimeController = $app->make(DrivetimeSearchController::class);
        $params = $slimApp->request()->get();

        // Process request to get authorization token
        $drivetime = $drivetimeController->getSearch($params);

        // Return successfully response (200) with JSON encoded model
        $slimApp->response()->header('Content-Type', 'application/json');
        $slimApp->response()->write(json_encode($drivetime));
        $slimApp->response()->setStatus(200);
    });

    $slimApp->run();
    exit();
}

// Backend User
$backend_user = Auth::get();
if ($backend_user->isValid()) {
    $userTrack_badPage = true;
} else {
    $backend_user = false;
}

$settings = $app->get(SettingsInterface::class);

// Set Language
header('Content-Language: '. $settings->LANG);

$apiKey = $settings->get('google.maps.api_key');

if (empty($apiKey)) {
    $settings['MODULES']['REW_IDX_MAPPING']    = false;
    $settings['MODULES']['REW_IDX_STREETVIEW'] = false;
    $settings['MODULES']['REW_IDX_DIRECTIONS'] = false;
    $settings['MODULES']['REW_IDX_ONBOARD']    = false;
}

// Create Page
$page = $app->get(PageInterface::class);

// REW Beacon Module
if (!empty($settings['MODULES']['REW_BEACON']) && $user->isValid()) {
    $page->container('ajax')->addModule('rew-beacon');
}

// Create Application
$application = $app->make(REW\Core\Application::class);
$application->run($page);

// User Tracking
require_once $settings['DIRS']['BACKEND'] . 'inc/php/routine.userTracking.php';

// Memory snapshot
Profile::memory()->snapshot('Page End');

// Show Log to REW Office
//if (Settings::isREW()) Log::display();

// Show Profiler
if (Settings::getInstance()->PROFILER !== false && Settings::isREW() !== false) {
    echo Profile::report()->getHTML();
}
