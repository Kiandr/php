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

// Process Submit
if (isset($_GET['submit'])) {
    // Required Fields
    $required   = array();
    $required[] = array('value' => 'agent_id', 'title' => 'Agent');

    // Process Required Fields
    foreach ($required as $require) {
        if (empty($_POST[$require['value']])) {
            $errors[] = __('%s is a required field.', $require['title']);
        }
    }

    // Check Errors
    if (empty($errors)) {
        // Get Order To Insert
        $order = $db->prepare("SELECT MAX(`order`) FROM `" . TABLE_TEAM_LISTINGS . "`"
            . " WHERE `team_id` = :team_id AND `listing_id` = :listing_id AND `listing_feed` = :listing_feed");
        $order->execute([
            'team_id'      => $team->getId(),
            'listing_id'   => $_GET['id'],
            'listing_feed' => $_GET['feed']
        ]);

        $order = $order->fetchColumn();
        $order = (isset($max_order)) ? ($max_order+1) : 0;

        try {
            // Load featured agents
            $insert_agents_query = $db->prepare("INSERT INTO `" . TABLE_TEAM_LISTINGS . "` SET "
                . "`team_id`			= :team_id,"
                . "`agent_id`			= :agent_id,"
                . "`listing_id`		    = :listing_id,"
                . "`listing_feed`		= :listing_feed,"
                . "`order`	        	= :order;");
            $insert_agents_query->execute([
                'team_id'      => $team->getId(),
                'agent_id'     => $_POST['agent_id'],
                'listing_id'   => $_GET['id'],
                'listing_feed' => $_GET['feed'],
                'order'        => $order
            ]);

        // Query Error
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }

        $authuser->setNotices($success, $errors);

        // Redirect to Edit Form
        header('Location: ../?id='.$_GET['id'].'&feed='.$_GET['feed'].'&team='.$team->getId());
        exit;
    }
}

// Load Team Agents
$possible_agents = $team->getAgents([Backend_Team::PERM_FEATURE_LISTINGS]);

// Load Listing
if ($_GET['feed'] != 'cms') {
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

    // Get idx feed
    Util_IDX::switchFeed($_GET['feed']);
    $idx = Util_IDX::getIdx();
    $db_idx = Util_IDX::getDatabase();

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

// Load Already Featured Agents
$included_agents_query = $db->prepare("SELECT `agent_id` AS 'id'"
    . " FROM `" . TABLE_TEAM_LISTINGS . "`"
    . " WHERE `team_id` = :team_id AND `listing_id` = :listing_id AND `listing_feed` = :listing_feed");
$included_agents_query->execute([
    'team_id'      => $team->getId(),
    'listing_id'   => $_GET['id'],
    'listing_feed' => $_GET['feed']
]);
$included_agents = $included_agents_query->fetchAll(PDO::FETCH_COLUMN);

if (!empty($owning_agent_id)) {
    $owning_agent = Backend_Agent::load($owning_agent_id);
}

if (!empty($possible_agents)) {
    $agents_query = $db->prepare(
        "SELECT `a`.`id`, CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS `name`"
        . " FROM `" . LM_TABLE_AGENTS . "` `a`"
        . " WHERE `id` != ?"
        . " AND `id` IN (" . implode(', ', array_fill(0, count($possible_agents), '?')) . ")"
        . (!empty($included_agents)
            ? " AND `id` NOT IN (" . implode(', ', array_fill(0, count($included_agents), '?')) . ")"
            : "")
        . " ORDER BY `last_name` ASC;"
    );
    $agents_query->execute(array_merge([$owning_agent_id], array_values($possible_agents), $included_agents));
    $new_agents = $agents_query->fetchAll();
}
