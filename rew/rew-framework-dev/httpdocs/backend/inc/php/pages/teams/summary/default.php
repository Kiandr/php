<?php

// Check for Team Management
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];
if (isset($_GET['id'])) {
    $team = Backend_Team::load($_GET['id']);
}

// Get Authorization Managers
$settings = Settings::getInstance();
$teamAuth = new REW\Backend\Auth\TeamsAuth($settings);
$teamSubdomainEnabled = $settings['MODULES']['REW_TEAM_CMS'];

/* Throw Missing Team Exception */
if (empty($team) || !$team instanceof Backend_Team) {
    throw new \REW\Backend\Exceptions\MissingId\MissingTeamException();
}

// Authorized to Manage All Teams
if (!$teamAuth->canManageTeams($authuser)) {
    // Authorized to Manage Own Teams
    $teamAgents = $team->getAgentCollection();
    if (!($teamAuth->canViewTeams($authuser) || $teamAuth->canManageOwn($authuser)) || !$teamAgents->checkAgentInCollection($authuser->info('id'))) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to view this team')
        );
    }
}

// Authorized to Assign/Unassign/Edit Team Members
if ($teamAuth->canManageTeams($authuser) ||
    ($teamAuth->canManageOwn($authuser)
    && $team->getPrimaryAgent() == $authuser->info('id'))) {
    // Allow for assigning, unassigning, deletion or updating the team
    $can_assign = true;
    $can_unassign = true;
    $can_edit = true;
    $can_delete = true;
}

// DB connection
$db = DB::get();

// Success
$success = array();

// Errors
$errors = array();

// Remove a team
if (isset($_GET['delete'])) {
    if (!$can_unassign) {
        $errors[] = __('Error! You do not have the required permissions to remove agents from in this team!');
    }

    // Check that the agent is resolved
    $agent = Backend_Agent::load($_GET['delete']);
    if (!isset($agent)) {
        $errors[] = __('Error! Agent could not be created from provided id!');
    } else if (!isset($team)) {
        $errors[] = __('Error! Team could not be found!');
    }

    // Unassign agent
    if (empty($errors)) {
        try {
            $team->unassignAgent($agent->getId());
            $success[] = __('The selected agent has been removed from this group.');

            // Log Event Users
            $event_users = [new History_User_Agent($authuser->info('id'))];
            if ($authuser->info('id') != $agent->getId()) {
                $event_users[] = new History_User_Agent($agent->getId());
            }

            // Log Event: Added to Team
            $event = new History_Event_Update_TeamRemove(array(
                'team' => ['id' => $team->getId(), 'name' => $team->info('name')],
                'removing_agent' => $authuser->info('id'),
                'secondary_agent' => $agent->getId()
            ), $event_users);

            // Save to DB
            $event->save();
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    }
}

$page_limit = 10;

// Get Agents Sharing with this Team
$agentCollection = $team->getAgentCollection()->cache();

// Get Permissions for Each Team Agent
$agents =  array_filter(array_map(function ($team_agent) use ($team) {
    if ($team_agent != $team->getPrimaryAgent()) {
        $permissions = $team->getAgentPermissions($team_agent);
        return array_merge(['agent_id' => $team_agent], $permissions);
    }
}, $agentCollection->getAllAgents()));

// Count Members
$count = ['total' => count($agents)];

$listingCollection = $team->getListingCollection(
    $agentCollection->filterByGrantingPermissions([Backend_Team::PERM_SHARE_FEATURE_LISTINGS])
);

// Get Listing Count
$listing_count = $listingCollection->countCms();

// Get Listings
$listings = $listingCollection->getCmsResults($page_limit + 1, 0);
$idx_count = $listingCollection->countIdx();

if ($listing_count < $page_limit + 1) {
    foreach ($idx_count as $feed => $idx_listing_count) {
        $remaining_page_limit = $page_limit + 1 - count($listings);
        $listings = array_merge($listings, $listingCollection->getIdxResults(
            $feed,
            $remaining_page_limit,
            0
        ));
    }
}

$moreListings = false;
if (count($listings) == $page_limit + 1) {
    $moreListings = true;
    array_pop($listings);
}

// Get Primary Agent
$owning_agent = Backend_Agent::load($team->getPrimaryAgent());

// Subdomain addons
$team['subdomain_addons'] = explode(',', $team['subdomain_addons']);
sort($team['subdomain_addons']);

