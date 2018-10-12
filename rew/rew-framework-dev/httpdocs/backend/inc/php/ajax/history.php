<?php

// Include Backend Configuration
include_once dirname(__FILE__) . '/../../../common.inc.php';

// Errors
$errors = array();

// JSON Response
$json = array();

// Get container instance
$container = Container::getInstance();

// Create GUID is no Timeline GUID passed
if (empty($_POST['timeline_id'])) {
    $lastPage = null;
} else {
    // Load Page Timeline
    $timelineFactory = $container->get(\REW\Backend\Page\TimelineFactory::class);
    $lastPage = $timelineFactory->load($_POST['timeline_id']);
}

try {
    // Timeline Page must by present
    if (!isset($_POST['current_page'])) {
        throw new InvalidArgumentException('Page could not be decoded!');
    } else {
        /** @var \REW\Backend\Page\Timeline $currentPage */
        $currentPage = $container->make(
            \REW\Backend\Page\Timeline::class,
            ['url' => $_POST['current_page']['url'], 'get' => $_POST['current_page']['get'] ?: []]
        );
    }

    // If A Previous Page Exists
    if (isset($lastPage)) {
        // Ignore Repeated Pages
        if ($currentPage->compare($lastPage)) {
            $currentPage = $lastPage;
        } else {
            $mode = $_POST['timeline_mode'];
            if (isset($mode) && $mode == 'back') {
                if ($prevPage = $lastPage->getLast()) {
                    if ($currentPage->compare($prevPage)) {
                        $currentPage = $prevPage;
                    }
                }
            } else {
                $currentPage->setLast($lastPage);
            }
        }
    }

    // Return Timeline Id
    $currentPage->save();
    $json['timeline_id'] = $currentPage->getGUID();

    // Return Prev/Next Links
    if ($past = $currentPage->getLast()) {
        $json['last_page']   = $past->getLink('back');
    }
} catch (InvalidArgumentException $e) {
    $errors[] = 'Error! ' . $e->getMessage();
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
