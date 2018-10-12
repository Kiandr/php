<?php

// Full Page
$body_class = 'full';

// Get Authorization Managers
$reportsAuth = new REW\Backend\Auth\ReportsAuth(Settings::getInstance());

// Authorized to manage directories
if (!$reportsAuth->canViewAnalyticsReport($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to view analytics reports')
    );
}

// Success Collection
$success = array();

// Error Collection
$errors = array();

$db = DB::get();

// Not Connected
$connected = false;

// GA Profiles
$profiles = array();

// GA Segments
$segments = array();

$analyticsService = null;

try {
    // Google Token
    $token = $authuser->info('network_google_service_account');
    $token = !empty($token) ? json_decode($token, true) : false;

    // Require Token
    if (!empty($token)) {
        // Connect to Google
        /** @var Google_Service_Analytics $analyticsService */
        $analyticsService = \Container::getInstance()->get(Google_Service_Analytics::class);
        $client = $analyticsService->getClient();
        $client->setAuthConfig($token);
        $client->setScopes([
            Google_Service_Analytics::ANALYTICS_READONLY
        ]);
        $managementProfiles = $analyticsService->management_profiles->listManagementProfiles('~all', '~all');
        if ($managementProfiles) {
            // Connected
            $connected = true;

            foreach ($managementProfiles as $managementProfile) {
                /** @var Google_Service_Analytics_Profile $managementProfile */
                // GA Profile
                $id = 'ga:' . $managementProfile->getId();
                $profiles[$id] = array(
                    'id' => $id,
                    'title' => str_replace('Google Analytics Profile ', '', $managementProfile->getName())
                );
            }

            $managementSegments = $analyticsService->management_segments->listManagementSegments();
            foreach ($managementSegments as $managementSegment) {
                /** @var Google_Service_Analytics_Segment $managementSegment */
                // GA Segment
                $id = $managementSegment->getSegmentId();
                $segments[$id] = array(
                    'id'    => $id,
                    'title' => str_replace('Google Analytics Advanced Segment ', '', $managementSegment->getName())
                );
            }
        }
    }
} catch (Exception $e) {
    // Error Occurred
    $errors[] = 'Error occurred while connecting to Google Analytics.';
}

// Selected GA Profile
$ga_profile = isset($_POST['ga_profile']) ? $_POST['ga_profile'] : $_GET['ga_profile'];
$ga_profile = !empty($ga_profile) ? $ga_profile : key($profiles);

// Selected GA Segment
$ga_segment = isset($_POST['ga_segment']) ? $_POST['ga_segment'] : $_GET['ga_segment'];
$ga_segment = !empty($ga_segment) ? $ga_segment : key($segments);

// Start Date
$date_start = isset($_POST['date_start']) ? $_POST['date_start'] : $_GET['date_start'];
$date_start = !empty($date_start) ? $date_start : date('Y-m-d', strtotime('-1 month'));

// End Date
$date_end = isset($_POST['date_end']) ? $_POST['date_end'] : $_GET['date_end'];
$date_end = isset($date_end) ? $date_end : date('Y-m-d');

if (!function_exists('formatNumber')) {
    function formatNumber($number)
    {
        // Check for float, or value with scientific notation
        if (preg_match('/^(\d+\.\d+)|(\d+E\d+)|(\d+.\d+E\d+)$/', $number)) {
            return floatval($number);
        } else {
            return intval($number);
        }
    }
}

// AJAX Request
if (!empty($_POST['ajax']) && isset($_GET['load'])) {
    // JSON Response
    $json = array();

    // Load Data
    $data = array();
    $type = $_POST['type'];
    $entries = [];
    switch ($type) {
        // Visits & Pageviews by Date
        case 'visitors':
            $entries = $analyticsService->data_ga->get(
                $ga_profile,
                $date_start,
                $date_end,
                'ga:visits,ga:pageviews',
                ['dimensions' => 'ga:date', 'segment' => $ga_segment]
            );
            break;

        // Site Usage
        case 'usage':
            $entries = $analyticsService->data_ga->get(
                $ga_profile,
                $date_start,
                $date_end,
                'ga:visits,ga:pageviews,ga:pageviewsPerVisit,ga:avgSessionDuration,ga:percentNewVisits,ga:visitBounceRate',
                ['max-results' => '1', 'segment' => $ga_segment]
            );
            break;

        // Referring Sites
        case 'referers':
            $entries = $analyticsService->data_ga->get(
                $ga_profile,
                $date_start,
                $date_end,
                'ga:visits',
                ['dimensions' => 'ga:source', 'metrics' => 'ga:visits', 'filters' => 'ga:medium==referral', 'sort' => '-ga:visits', 'max-results' => 50, 'segment' => $ga_segment]
            );
            break;

        // Referring Keywords
        case 'keywords':
            $entries = $analyticsService->data_ga->get(
                $ga_profile,
                $date_start,
                $date_end,
                'ga:visits',
                ['dimensions' => 'ga:keyword', 'sort' => '-ga:visits', 'max-results' => 50, 'segment' => $ga_segment]
            );
            break;

        // Search Engine Traffic
        case 'sources':
            $entries = $analyticsService->data_ga->get(
                $ga_profile,
                $date_start,
                $date_end,
                'ga:visits',
                ['dimensions' => 'ga:source', 'sort' => '-ga:visits', 'max-results' => 50, 'segment' => $ga_segment, 'filters' => 'ga:medium==cpa,ga:medium==cpc,ga:medium==cpm,ga:medium==cpp,ga:medium==cpv,ga:medium==organic,ga:medium==ppc']
            );
            break;

        // Unknown Request
        default:
            $errors[] = __('Unknown Data Request');
            break;
    }

    if (is_object($entries)) {
        // ugh stupid google returns separated headings, combine them for less hackery
        $combinedEntries = [];
        $columnHeaders = [];
        foreach ($entries->columnHeaders as $columnHeader) {
            /** @var Google_Service_Analytics_GaDataColumnHeaders $columnHeader */
            $columnHeaders[] = str_replace('ga:', '', $columnHeader->getName());
        }
        foreach ($entries as $entry) {
            $combinedEntries[] = array_combine($columnHeaders, $entry);
        }
        $entries = $combinedEntries;
    }

    // Return Type
    $json['type'] = $type;

    // Check Errors
    if (empty($errors)) {

        /**
         * Load GA Data
         */
        try {
            // Data Chart
            $chart = array();

            // Data Table
            $table = array();

            // Table Columns
            switch ($type) {
                // Visits & Pageviews by Date
                case 'visitors':
                    // Chart Data
                    $chart['series'] = array(
                        0 => array('name' => __('Visits'), 'pointInterval' => 24 * 3600 * 1000, 'data' => array()),
                        1 => array('name' => __('Page Views'), 'pointInterval' => 24 * 3600 * 1000, 'data' => array())
                    );

                    // Chart Data
                    for ($r = 0; $r < count($entries); $r++) {
                        $v = $entries[$r];
                        $d = strtotime($v['date']) * 1000;
                        $chart['series'][0]['data'][] = array($d, formatNumber($v['visits']));
                        $chart['series'][1]['data'][] = array($d, formatNumber($v['pageviews']));
                    }

                    break;

                // Site Usage
                case 'usage':
                    // Extract Time
                    $secs  = $entries[0]['avgSessionDuration'];
                    $hours = floor($secs / 3600);
                    $mins  = floor(($secs - ($hours * 3600)) / 60);
                    $secs  = $secs % 60;

                    // Data
                    $table = array(
                        'visits'            => number_format($entries[0]['visits'], 0),
                        'pageviews'         => number_format($entries[0]['pageviews'], 0),
                        'pageviewsPerVisit' => number_format($entries[0]['pageviewsPerVisit'], 2),
                        'percentNewVisits'  => number_format($entries[0]['percentNewVisits'], 2) . '%',
                        'visitBounceRate'   => number_format($entries[0]['visitBounceRate'], 2) . '%',
                        'avgTimeOnSite'     => str_pad(intval($hours), 2, '0', STR_PAD_LEFT) . ':' . str_pad(intval($mins), 2, '0', STR_PAD_LEFT) . ':' . str_pad(intval($secs), 2, '0', STR_PAD_LEFT)
                    );
                    break;

                // Referring Sites
                case 'referers':
                    $table = '<ul>';
                    foreach ($entries as $entry) {
                        $table .= '<li class="groups">' . htmlspecialchars($entry['source']) . ' <span class="group -R count">' . number_format($entry['visits']) . '</span></li>';
                    }
                    $table .= '</ul>';
                    break;

                // Referring Keywords
                case 'keywords':
                    $table = '<ul>';
                    foreach ($entries as $entry) {
                        $table .= '<li class="groups">' . htmlspecialchars($entry['keyword']) . ' <span class="group -R count">' . number_format($entry['visits']) . '</span></li>';
                    }
                    $table .= '</ul>';
                    break;

                // Search Engine Traffic
                case 'sources':
                    $table = '<ul>';
                    foreach ($entries as $entry) {
                        $table .= '<li class="groups">' . htmlspecialchars($entry['source']) . ' <span class="group -R count">' . number_format($entry['visits']) . '</span></li>';
                    }
                    $table .= '</ul>';
                    break;
            }

            // Check Data Table
            if (!empty($table)) {
                $json['table'] = $table;
            }

            // Check Data Chart
            if (!empty($chart)) {
                $json['chart'] = $chart;
            }

        // Catch Errors
        } catch (Exception $e) {
            // Error Occurred
            $errors[] = 'Error occurred while retrieving Google Analytics data.';
        }
    }

    // Send as JSON
    header('Content-type: application/json');

    // JSON Success
    if (!empty($success)) {
        $json['success'] = $success;
    }

    // JSON Errors
    if (!empty($errors)) {
        $json['errors'] = $errors;
    }

    // Return JSON
    die(json_encode($json));
}
