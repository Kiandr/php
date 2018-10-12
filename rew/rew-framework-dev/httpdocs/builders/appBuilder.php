<?php

namespace BDX;

// Require Composer Vendor Auto loader
require_once __DIR__ . '/../../boot/app.php';

// Include BDX Settings
require_once __DIR__ . '/classes/Settings.php';

try {
    // Create app
    $app = new \Slim\ModifiedSlim(array('templates.path' => './'));

    // BDX Database
    $db_settings = Settings::getInstance()->DATABASES['bdx'];
    $app->db_bdx = new DB($db_settings['hostname'], $db_settings['username'], $db_settings['password'], $db_settings['database']);
        
    if (Settings::getInstance()->FRAMEWORK) {
        if ($_REQUEST['bdx-snippet']) {
            // Snippet View
            $app->config('view', new \Slim\View\SnippetView($app->db));
            
            // Set Snippet Flag
            $app->snippet = true;
        } else {
            // Framework View
            $app->config('view', new \Slim\View\FrameworkView($app->db));
        }
            
        // CMS Database
        $db_settings = \Settings::getInstance()->DATABASES['default'];
        $app->db = new DB($db_settings['hostname'], $db_settings['username'], $db_settings['password'], $db_settings['database']);
    } else {
        // BDX Middleware
        $app->add(new \Slim\Middleware\BDXMiddleware());
    }

    // Get Settings defined in the backend
    $app->bdx_settings = Settings::getBDXSettings($app->db);
        
    // State List
    $app->states = State::getStates();
    
    // Build state list from settings
    $states = array();
    if (Settings::getInstance()->STATES) {
        if (!empty($app->bdx_settings['states']) && is_array($app->bdx_settings['states'])) {
            if (is_array(Settings::getInstance()->STATES)) {
                foreach ($app->bdx_settings['states'] as $key => $val) {
                    if (in_array($key, Settings::getInstance()->STATES)) {
                        $states[] = $key;
                    }
                }
            } else {
                foreach ($app->bdx_settings['states'] as $key => $val) {
                    $states[] = $key;
                }
            }
        } else {
            if (is_array(Settings::getInstance()->STATES)) {
                foreach (Settings::getInstance()->STATES as $st) {
                    $states[] = $st;
                }
            }
        }
    } else {
        throw new \Exception("STATES MUST BE SET IN THE SETTINGS CLASS!");
    }
        
    // Builders XML
    $app->get('/xml/', function () use ($app) {
        
        // Get List of XML files
        $xml_files = array_diff(scandir(Settings::getInstance()->DIRS['BUILDER_XML']), array('..', '.'));
        
        // Get newest XML file
        $newest_xml_file = array_pop($xml_files);
        
        // Redirect to that XML file
        $app->redirect($newest_xml_file);
    });
    
    // Sitemap
    $app->get('/sitemap/', function () use ($app) {
        $app->render('php/pages/sitemap.php', array(
            'app' => $app,
            'page_name' => 'sitemap'
        ));
    });
    
    // Homepage
    $app->get('/', function () use ($app) {
        $app->render('php/pages/states.php', array(
            'app' => $app,
            'javascript' => array(
                'states'
            ),
            'page_name' => 'states'
        ));
    });
    
    // Community Inquiry
    $app->map('/community-inquire/', function () use ($app) {
        $app->render('php/forms/community-inquire.php', array(
            'app' => $app,
            'page_name' => 'community_inquire'
        ));
    })->via('GET', 'POST');
        
    // Listing Inquiry
    $app->map('/listing-inquire/', function () use ($app) {
        $app->render('php/forms/listing-inquire.php', array(
            'app' => $app,
            'page_name' => 'listing_inquire'
        ));
    })->via('GET', 'POST');
                
    // Show Cities in State
    $app->get('/:state/', function ($state) use ($app) {
                        
        // Find State Abbr
        foreach ($app->states as $abbr => $st) {
            if ($state === str_replace(' ', '-', strtolower($st))) {
                $app->stateName = $st;
                $app->state = $abbr;
                $found = true;
                break;
            }
        }
        
        // Not Found
        if (empty($found)) {
            $app->notFound();
        }
        
        $app->render('php/pages/cities.php', array(
            'app' => $app,
            'javascript' => array(
                'cities'
            ),
            'page_name' => 'cities'
        ));
    })->conditions(array(
            'state' => $matchStates
    ))->name('state');
    
    // Search New Homes & Communities
    $app->get('/:state/:search/', function ($state, $search) use ($app) {
                
        // Find State Abbr
        foreach ($app->states as $abbr => $st) {
            if ($state === str_replace(' ', '-', strtolower($st))) {
                $app->stateName = $st;
                $app->state = $abbr;
                $found = true;
                break;
            }
        }
        
        // Not Found
        if (empty($found)) {
            $app->notFound();
        }
        
        $app->render('php/pages/search.php', array(
            'app' => $app,
            'search' => $search,
            'javascript' => array(
                'search'
            ),
            'page_name' => 'search'
        ));
    })->conditions(array(
            'state'     => $matchStates,
    ))->name('search');
    
    // Community Details
    $app->map('/:state/:city/:community/', function ($state, $city, $community) use ($app, $states) {

        
        // State not in settings. 404.
        if (!empty($states)) {
            if (!in_array((array_search(ucwords(str_replace('-', ' ', $state)), $app->states)), $states)) {
                $app->notFound();
            }
        }
        
        $app->render('php/pages/community.php', array(
            'app' => $app,
            'state' => $state,
            'city' => $city,
            'community' => $community,
            'javascript' => array(
                'community'
            ),
            'page_name' => 'community'
        ));
    })->via('GET', 'POST')->name('community');
    
    // New Home Details
    $app->map('/:state/:city/:community/:listing/', function ($state, $city, $community, $listing) use ($app, $states) {
        
        // State not in settings. 404.
        if (!empty($states)) {
            if (!in_array((array_search(ucwords(str_replace('-', ' ', $state)), $app->states)), $states)) {
                $app->notFound();
            }
        }
            
        $app->render('php/pages/listing.php', array(
            'app' => $app,
            'state' => $state,
            'city' => $city,
            'community' => $community,
            'listing' => $listing,
            'javascript' => array(
                'listing'
            ),
            'page_name' => 'listing'
        ));
    })->via('GET', 'POST')->name('listing');
        
    // 404
    $app->notFound(function () use ($app) {
        $app->render('php/pages/404.php', array(
            'app' => $app,
            'page' => '404'
        ));
    });
            
// Error Occurred
} catch (Exception $e) {
    //Log::error($e);
}
