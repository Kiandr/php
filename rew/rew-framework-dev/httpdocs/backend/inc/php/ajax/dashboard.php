<?php

use REW\Backend\Dashboard\EventListener\FormEvents\InquiryEventListener;
use REW\Backend\Dashboard\EventListener\FormEvents\SellingEventListener;
use REW\Backend\Dashboard\EventListener\FormEvents\ShowingEventListener;
use REW\Backend\Dashboard\EventListener\MessageEventListener;
use REW\Backend\Dashboard\EventListener\RegistrationEventListener;

// Set Limit
const LIMIT = 15;

// Include Backend Configuration
include_once dirname(__FILE__) . '/../../../common.inc.php';

// Require Authorization
if (!$authuser->isValid()) {
    die('{}');
}

// Get Authorization Managers
$settings = Settings::getInstance();
$dashboardAuth = Container::getInstance()->get(REW\Backend\Auth\DashboardAuth::class);

// Not authorized to view dashboard
if (!$dashboardAuth->canViewDashboard()) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        'You do not have permission to view the dashboard'
    );
}

// DB connection
$db = DB::get();

// Error Collection
$errors = array();

// Get Current Time
$timestamp = time();

// Process Inquire
if (isset($_GET['fetchAgent'])) {
    $lead = Backend_Lead::load($_GET['lead']);
    if (!empty($lead)) {
        try {
            $input = $_GET['input'];

            // Get Assignable Agents
            $agentsQuery = $db->prepare(
                "SELECT `a`.`id`,`a`.`image`, `a`.`first_name`, `a`.`last_name`, CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS 'name',"
                . " `a`.`auto_assign_time`, COUNT(*) AS 'leads'"
                . " FROM `agents` `a` LEFT JOIN `users` `u` ON `a`.`id` = `u`.`agent`"
                . (!empty($input) ? " WHERE CONCAT(`a`.`first_name`, ' ',  `a`.`last_name` ) LIKE :input" : '')
                . " GROUP BY `a`.`id`"
                . " ORDER BY"
                . (!empty($input)
                    ? " `a`.`first_name` ASC, `a`.`last_name` ASC"
                    : " `a`.`id` = :current_id DESC, `auto_assign_time` ASC")
                . " LIMIT 5;"
            );

            $agentsParams = [];
            if (empty($input)) {
                $agentsParams['current_id'] = $authuser->info('id');
            } else {
                $agentsParams['input'] = '%' . $input . '%';
            }
            $agentsQuery->execute($agentsParams);

            $agents = $agentsQuery->fetchAll();
            $agents = array_map(function ($agent) {
                if (empty($agent['image'])) {
                    $agent['value'] = $agent['id'];
                    $agent['defaultClass'] = strtolower($agent['last_name'][0]);
                    $agent['defaultText'] = strtoupper($agent['first_name'][0] . $agent['last_name'][0]);
                    unset($agent['first_name']);
                    unset($agent['last_name']);
                } else {
                    $agent['image'] = '/thumbs/60x60/uploads/agents/' . $agent['image'];
                }
                return $agent;
            }, $agents);
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }
    } else {
        $errors[] = 'An invalid lead id was provided.';
    }
    $json['data'] = $agents;

    // JSON Errors
    if (empty($errors)) {
        $json['success'] = 'Event response has been recorded.';
    }
}

// Process Inquire
if (isset($_GET['eventResponse'])) {
    if (!empty($_POST['event'])) {
        try {
            $inquiryRespondQuery = $db->prepare(
                'UPDATE ' . LM_TABLE_FORMS . ' `uf`'
                . ' SET  `uf`.`reply` = 1'
                . ' WHERE  `uf`.`id` = ?;'
            );
            $inquiryRespondQuery->execute([$_POST['event']]);
            if ($inquiryRespondQuery->rowCount() == 0) {
                $errors[] = 'Event response could not be recorded.';
            }
        } catch (\Exception $e) {
            $errors[] = 'Event response could not be recorded.';
        }
    } else {
        $errors[] = 'Event response could not be recorded.';
    }

    // JSON Errors
    if (empty($errors)) {
        $json['success'] = 'Event response has been recorded.';
    }
}

// Process Message
if (isset($_GET['messageResponse'])) {
    if (!empty($_POST['message'])) {
        try {
            // Re-Enable Message
            $enableMessageQuery = $db->prepare(
                'UPDATE ' . LM_TABLE_MESSAGES
                . ' SET  `agent_read` = \'Y\', `user_del` = \'N\''
                . ' WHERE  `id` = ?;'
            );
            $enableMessageQuery->execute([$_POST['message']]);
            if ($enableMessageQuery->rowCount() == 0) {
                $errors[] = 'Message response could not be recorded.';
            }
        } catch (\Exception $e) {
            $errors[] = 'Message response could not be recorded.';
        }
    } else {
        $errors[] = 'No Message response was provided.';
    }

    // JSON Errors
    if (empty($errors)) {
        $json['success'] = 'Message response has been recorded.';
    }
}

// Process Message
if (isset($_GET['eventDismissed'])) {
    if (!empty($_POST['event_id']) && !empty($_POST['event_mode'])) {
        try {
            // Re-Enable Message
            $dismissEventQuery = $db->prepare(
                'INSERT INTO `dashboard_dismissed` SET'
                . ' `agent`      = ?,'
                . ' `event_id`   = ?,'
                . ' `event_mode` = ?,'
                . ' `timestamp`  = NOW();'
            );
            $dismissEventQuery->execute([
                $authuser->info('id'),
                $_POST['event_id'],
                $_POST['event_mode']
            ]);
        } catch (\Exception $e) {
            $errors[] = 'Event could not be dismissed.';
        }
    } else {
        $errors[] = 'Both event id and event mode are required.';
    }

    // JSON Errors
    if (empty($errors)) {
        $json['success'] = ['Event has been dismissed.'];
    }
}

// Get Lead Event Listeners
$container = Container::getInstance();
$inquiryListener = $container->get(InquiryEventListener::class);
$sellingListener = $container->get(SellingEventListener::class);
$showingListener = $container->get(ShowingEventListener::class);
$messageListener = $container->get(MessageEventListener::class);
$registrationListener = $container->get(RegistrationEventListener::class);

/**
 * Get Events Created Since Page Load
 * @param itn timestamp Get events newer then timestamp
 */
if (isset($_GET['updateNew'])) {
    // Check for valid filter
    if (empty($_GET['timestamp'])) {
        $errors[] = 'A timestamp is required.';
    }

    // Throw Filter Errors
    if (!empty($errors)) {
        // Send failure as JSON
        header('Content-type: application/json');
        $json['errors'] = $errors;
        die(json_encode($json));
    }

    // Build Event List
    $eventListeners = [
        $messageListener,
        $registrationListener,
        $inquiryListener,
        $showingListener,
        $sellingListener
    ];

    // Get New Lead Event Ids
    $events = [];
    foreach ($eventListeners as $eventListener) {
        $events[$eventListener->getMode()] = [
            'eventIds' => $eventListener->getNewerEventIds($_GET['timestamp']),
            'factory' => $eventListener->getFactory()
        ];
    }

    $newEvents = [];
    do {
        $nextEvent = $nextTimestamp = null;
        foreach ($events as $mode => $event) {
            $eventId = $event['eventIds'][0];
            if (!empty($eventId) && (!isset($nextTimestamp) || $eventId->getTimestamp() > $nextTimestamp)) {
                $nextEvent = $mode;
                $nextTimestamp = $eventId->getTimestamp();
            }
        }
        if (isset($nextEvent)) {
            // Load Full Event
            $newEvent = array_pop($events[$nextEvent]['eventIds']);
            $newEvents[] = $events[$nextEvent]['factory']->getEvent($newEvent);
        }
    } while (isset($nextEvent));

    // JSON Errors
    if (empty($errors)) {
        $json['events'] = !empty($newEvents) ? $newEvents : [];
        $json['timestamp'] = $timestamp;
    }
}

/**
 * Load older events
 * @param string nextUnloadedRegister Next Register Cursor
 * @param string nextUnloadedMessage Next Message Cursor
 * @param string nextUnloadedInquiry Next Inquiry Cursor
 * @param string nextUnloadedShowing Next Showing Cursor
 * @param string nextUnloadedSelling Next Selling Cursor
 */
if (isset($_GET['fetchOldEvents'])) {
    // Build Event List
    $eventListeners = [
        $messageListener,
        $registrationListener,
        $inquiryListener,
        $showingListener,
        $sellingListener
    ];

    $events = [];
    foreach ($eventListeners as $eventListener) {
        if ($eventListener->getMode() == 'register' && !empty($_GET['nextUnloadedRegister'])) {
            list($nextEventTimestamp, $nextEventId) = explode('::', $_GET['nextUnloadedRegister']);
        }
        if ($eventListener->getMode() == 'message' && !empty($_GET['nextUnloadedMessage'])) {
            list($nextEventTimestamp, $nextEventId) = explode('::', $_GET['nextUnloadedMessage']);
        }
        if ($eventListener->getMode() == 'inquiry' && !empty($_GET['nextUnloadedInquiry'])) {
            list($nextEventTimestamp, $nextEventId) = explode('::', $_GET['nextUnloadedInquiry']);
        }
        if ($eventListener->getMode() == 'showing' && !empty($_GET['nextUnloadedShowing'])) {
            list($nextEventTimestamp, $nextEventId) = explode('::', $_GET['nextUnloadedShowing']);
        }
        if ($eventListener->getMode() == 'selling' && !empty($_GET['nextUnloadedSelling'])) {
            list($nextEventTimestamp, $nextEventId) = explode('::', $_GET['nextUnloadedSelling']);
        }
        $events[$eventListener->getMode()] = [
            'eventIds' => $eventListener->getOlderEventIds($nextEventTimestamp, $nextEventId, LIMIT),
            'eventsCount' => 0,
            'factory' => $eventListener->getFactory()
        ];
    }

    $oldEvents = [];
    for ($i = 0; $i < LIMIT; $i++) {
        $nextEvent = $nextTimestamp = null;
        foreach ($events as $mode => $event) {
            $eventId = $event['eventIds'][0];
            if (!empty($eventId) && (!isset($nextTimestamp) || $eventId->getTimestamp() > $nextTimestamp)) {
                $nextEvent = $mode;
                $nextTimestamp = $eventId->getTimestamp();
            }
        }
        if (isset($nextEvent)) {
            // Subtract from unloaded events count
            $events[$nextEvent]['eventsCount']++;

            // Load Full Event
            $oldEvent= array_shift($events[$nextEvent]['eventIds']);
            $oldEvents[] = $events[$nextEvent]['factory']->getEvent($oldEvent);
        }
    }

    // Send as JSON
    $json = ['events' => $oldEvents];

    // Gut Loaded Event Data
    foreach ($events as $mode => $event) {
        // Get Cursor & Count Names
        $cursorName = 'nextUnloaded' . ucfirst(strtolower($mode));
        $countName = 'loaded' . ucfirst(strtolower($mode)) . 'Count';

        $json[$countName] = $event['eventsCount'];
        if (!empty($event['eventIds'])) {
            $nextEvent = array_shift($event['eventIds']);
            $json[$cursorName] = $nextEvent->getCursor();
        }
    }
}

/**
 * Load a filtered array of older events
 * @param string filter Type of event to fetch
 * @param seting nextFilteredEvent Cursor indicating where to start fetching
 */
if (isset($_GET['fetchFilteredOldEvents'])) {
    // Check for valid filter
    if (empty($_GET['filter'])) {
        $errors[] = 'A filter string is required.';
    }

    // Build Event List
    if ($_GET['filter'] == $messageListener->getMode()) {
        $eventListener = $messageListener;
    } else if ($_GET['filter'] == $registrationListener->getMode()) {
        $eventListener = $registrationListener;
    } else if ($_GET['filter'] == $inquiryListener->getMode()) {
        $eventListener = $inquiryListener;
    } else if ($_GET['filter'] == $showingListener->getMode()) {
        $eventListener = $showingListener;
    } else if ($_GET['filter'] == $sellingListener->getMode()) {
        $eventListener = $sellingListener;
    } else {
        $errors[] = $_GET['filter'] . ' is not a valid filter.';
    }

    // Package Events
    if (empty($errors)) {
        list($nextEventTimestamp, $nextEventId) = explode('::', $_GET['nextFilteredEvent']);
        $event = [
            'eventIds' => $eventListener->getOlderEventIds($nextEventTimestamp, $nextEventId, LIMIT),
            'eventsCount' => 0,
            'factory' => $eventListener->getFactory()
        ];

        $nextEvents = [];
        for ($i = 0; $i < LIMIT; $i++) {
            $oldEvent= array_shift($event['eventIds']);
            if (empty($oldEvent)) {
                break;
            }
            $nextEvents[] = $event['factory']->getEvent($oldEvent);
            $event['eventsCount']++;
        }

        // Send as JSON
        $json = [
            'events' => $nextEvents,
            'loadedFilteredCount' => $event['eventsCount']
        ];
        if (!empty($event['eventIds'])) {
            $nextEvent = array_shift($event['eventIds']);
            $json['nextFilteredEvent'] = $nextEvent->getCursor();
        }
    }
}

/**
 * Build JSON Response
 */

// Send as JSON
header('Content-type: application/json');

// JSON Errors
if (!empty($errors)) {
    $json['errors'] = $errors;
}

// Return JSON Data
die(json_encode($json));
