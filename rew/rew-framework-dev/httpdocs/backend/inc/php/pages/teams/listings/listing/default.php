<?php

// Check for Team Management
$_GET['team'] = isset($_POST['team']) ? $_POST['team'] : $_GET['team'];
$team = Backend_Team::load($_GET['team']);

// Listing ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Listing Feed
$_GET['feed'] = isset($_POST['feed']) ? $_POST['feed'] : $_GET['feed'];

// Get Authorization Managers
$settings = Settings::getInstance();
$teamAuth = new REW\Backend\Auth\TeamsAuth($settings);
$teamSubdomainAuth = Container::getInstance()->get(\REW\Backend\Auth\Team\SubdomainAuth::class);

/* Throw Missing Team Exception */
if (empty($team) || !$team instanceof Backend_Team) {
    throw new \REW\Backend\Exceptions\MissingId\MissingTeamException();
}

// Authorized to Manage All Teams
if (!$teamAuth->canManageTeams($authuser)) {
    // Authorized to Manage Own Teams
    if (!$teamAuth->canManageOwn($authuser) && $team->getPrimaryAgent() == $authuser->info('id')) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to edit this team')
        );
    }
}

// DB connection
$db = DB::get();

// Success
$success = array();

// Errors
$errors = array();

// AJAX request: update community order
if (!empty($_POST['ajax']) && !empty($_POST['order'])) {
    $order = 0;
    $agent_ids = $_GET['items'];
    if (!empty($agent_ids) && is_array($agent_ids)) {
        $save_order = $db->prepare("UPDATE `" . TABLE_TEAM_LISTINGS . "` SET `order` = :order WHERE "
            . "`team_id` = :team_id"
            . " AND `agent_id` = :agent_id"
            . " AND `listing_id` = :listing_id"
            . " AND `listing_feed` = :listing_feed");
        foreach ($agent_ids as $agent_id) {
            $save_order->execute([
                'team_id' => $_GET['team'],
                'agent_id' => $agent_id,
                'listing_id' => $_GET['id'],
                'listing_feed' => $_GET['feed'],
                'order' => ++$order
            ]);
        }
    }
    // Return JSON response
    header('Content-type: application/json');
    die(json_encode(array()));
}

// Delete community
$delete = $_GET['delete'];
if (!empty($delete)) {
    try {
        // Find featured community in database
        $find_agent = $db->prepare("SELECT * FROM `" . TABLE_TEAM_LISTINGS . "` WHERE "
            . "`team_id` = :team_id"
            . " AND `agent_id` = :agent_id"
            . " AND `listing_id` = :listing_id"
            . " AND `listing_feed` = :listing_feed");
        $find_agent->execute([
            'team_id' => $_GET['team'],
            'agent_id' => $_GET['delete'],
            'listing_id' => $_GET['id'],
            'listing_feed' => $_GET['feed']
        ]);
        $agent = $find_agent->fetch();
        if (empty($agent)) {
            throw new UnexpectedValueException(__('The selected agent is not featured on this listing.'));
        }

        // Delete featured community from database
        $unassign_agent = $db->prepare("DELETE FROM `" . TABLE_TEAM_LISTINGS . "` WHERE "
            . "`team_id` = :team_id"
            . " AND `agent_id` = :agent_id"
            . " AND `listing_id` = :listing_id"
            . " AND `listing_feed` = :listing_feed");
        $unassign_agent->execute([
            'team_id' => $_GET['team'],
            'agent_id' => $_GET['delete'],
            'listing_id' => $_GET['id'],
            'listing_feed' => $_GET['feed']
        ]);
        $success[] = __('Featured agent has successfully been deleted.');

    // Validation error
    } catch (UnexpectedValueException $e) {
        $errors[] = $e->getMessage();
    } catch (PDOException $e) {
        $errors[] = __('Error occurred while deleting featured community.');
        //$errors[] = $e->getMessage();
    }

    // Save notices and redirect to list
    $authuser->setNotices($success, $errors);
    header('Location: ?team=' . $_GET['team'] . '&id=' . $_GET['id'] . '&feed=' . $_GET['feed']);
    exit;
}

// Load Team Agents
$possible_agents = $team->getAgents([Backend_Team::PERM_FEATURE_LISTINGS]);

// Load Listing
if ($_GET['feed'] != 'cms') {
    // Get idx feed
    Util_IDX::switchFeed($_GET['feed']);
    $idx = Util_IDX::getIdx();
    $db_idx = Util_IDX::getDatabase();

    // Get Agent Id Array
    $idx_ids = [];
    foreach ($possible_agents as $possible_agent) {
        $possible_agent = Backend_Agent::load($possible_agent);
        if (!empty($possible_agent->info('agent_id'))) {
            $feed_ids = json_decode($possible_agent->info('agent_id'), true);
            foreach ($feed_ids as $feed => $feed_id) {
                if ($feed != $_GET['feed']) {
                    continue;
                }
                $idx_ids[$feed_id] = $possible_agent->getId();
            }
        }
    }

    // Get Listing query
    $listing_query = $db_idx->query(
        "SELECT SQL_CACHE " . $idx->selectColumns('`tf`.')
        . " FROM `" . $idx->getTable() . "` `tf`"
        . " WHERE `tf`.`" . $idx->field('ListingMLS') . "` = '". $db_idx->cleanInput($_GET['id']). "'"
    );
    $listing = $db_idx->fetchArray($listing_query);
    $listing_title = $listing['Address'] . ' (' . Lang::write('MLS_NUMBER') . $listing['ListingMLS'] .')';
    $owning_agent_id = $idx_ids[$listing[$idx->field('ListingAgentID')]];
} else {
    // Get Listing query
    $listing_query = $db->prepare(
        "SELECT *"
        . " FROM `" . TABLE_LISTINGS . "`"
        . " WHERE `id` =  :id;"
    );
    $listing_query->execute(['id' => $_GET['id']]);
    $listing = $listing_query->fetch();
    $listing_title = $listing['title'];
    $owning_agent_id = $listing['agent'];
}

// Load featured agents
$featured_agents_query = $db->prepare("SELECT `a`.`id`, `a`.`agent_id`, `a`.`title`, `a`.`image`, CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS `name`, `order`"
    . ", `a`.`first_name`, `a`.`last_name`"
    . " FROM `" . TABLE_TEAM_LISTINGS . "` `tf`"
    . " JOIN `" . LM_TABLE_AGENTS . "` `a` ON `tf`.`agent_id` = `a`.`id`"
    . " WHERE `a`.`id` != :agent_id AND `tf`.`team_id` = :team_id AND `listing_id` = :listing_id AND `listing_feed` = :listing_feed"
    . " ORDER BY `order`;");

$featured_agents_query->execute([
    'agent_id'     => $owning_agent_id,
    'team_id'      => $team->getId(),
    'listing_id'   => $_GET['id'],
    'listing_feed' => $_GET['feed']
]);
$featured_agents = $featured_agents_query->fetchAll();
$included_agents = array_map(function ($agent) {
    return $agent['id'];
}, $featured_agents);

// Current Listing Owner
if (!empty($owning_agent_id)) {
    $owning_agent = Backend_Agent::load($owning_agent_id);
}

//Current Team Agents
$possible_agents = array_filter($team->getAgents([Backend_Team::PERM_FEATURE_LISTINGS]), function ($agent) use ($owning_agent_id, $included_agents) {
    if ($agent == $owning_agent_id) {
        return false;
    }
    if (in_array($agent, $included_agents)) {
        return false;
    }
    return true;
});
