<?php

// Create instance of feed switcher module
$module = new REW\Backend\Module\FeedSwitcher\Module(
    Settings::getInstance()
);

// Get current IDX feed for use
$idxFeed = $module->getIdxFeed();

// URL parameters
$urlQuery = [];

// Include record ID
if (isset($_GET['id'])) {
    $urlQuery['id'] = $_GET['id'];
}

// Agent subdomain feeds
if (isset($_GET['agent'])) {
    $agentId = $_GET['agent'];
    $agent = Backend_Agent::load($agentId);
    $idxFeeds = $module->getAgentFeeds($agent);
    $urlQuery['agent'] = $_GET['agent'];

// Team subdomain feeds
} elseif (isset($_GET['team'])) {
    $teamId = $_GET['team'];
    $team = Backend_Team::load($teamId);
    $idxFeeds = $module->getTeamFeeds($team);
    $urlQuery['team'] = $_GET['team'];
} else {
    // Get available IDX feeds
    $idxFeeds = $module->getIdxFeeds();
}

// Append query parameters to feed link
$urlQuery = http_build_query($urlQuery);
$urlQuery = '&' . $urlQuery;
