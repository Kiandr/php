<?php

// App DB
$db = DB::get();

// App Settings
$settings = Settings::getInstance();

// Success
$success = array();

// Error
$errors = array();

// Lead ID
$_GET['id'] = isset($_POST['id']) ? $_POST['id'] : $_GET['id'];

// Query Lead
$agent = Backend_Agent::load($_GET['id']);

// Throw Missing Agent Exception
if (empty($agent)) {
    throw new \REW\Backend\Exceptions\MissingId\MissingAgentException();
}

// Get Agent Timezone and Backend_Agent
$timezoneQuery = $db->prepare("SELECT `name` AS `timezone` FROM `" . LM_TABLE_TIMEZONES . "` WHERE `id` = :id;");
$timezoneQuery->execute(['id' => $agent['timezone']]);
$timezone = $timezoneQuery->fetchColumn();

// Get Agent Authorization
$agentAuth = new REW\Backend\Auth\Agents\AgentAuth($settings, $authuser, $agent);

// Not authorized to view agent history
if (!$agentAuth->canViewAgent()) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to view this agent.')
    );
}

// Can Edit
$can_edit = $agentAuth->canEditAgent();

// Can Email
$can_email = $agentAuth->canEmailAgent();

// Agent Office
if (!empty($agent['office'])) {
    try {
        $office = $db->fetch("SELECT * FROM `" . TABLE_FEATURED_OFFICES . "` WHERE `id` = :id;", ['id' => $agent['office']]);
    } catch (PDOException $e) {}
}

// Admin Mode or Agent's Own Summary
$leads = array();
if ($agentAuth->canManageAgent()) {
    // Round Robin (Auto Assign / Auto Rotate) Status
    try {
        $settings = $db->fetch("SELECT `auto_assign`, `auto_rotate` FROM `" . TABLE_SETTINGS . "` WHERE `agent` = 1;");
    } catch (PDOException $e) {}

    // Leads (by Status)
    $leads['unassigned'] = 0;
    $leads['pending']  = 0;
    $leads['accepted'] = 0;
    $leads['closed']   = 0;
    $leads['total']    = 0;

    // Load Leads (by Status)
    try {
        foreach($db->fetchAll("SELECT COUNT(`u`.`id`) AS `total`, `status` FROM `" . LM_TABLE_LEADS . "` `u` WHERE `u`.`agent` = :agent GROUP BY `status`;", ['agent' => $agent['id']]) as $row) {
            $leads[$row['status']] = $row['total'];
            $leads['total'] += $row['total'];
        }
    } catch (PDOException $e) {
        $errors[] = __('Error Loading Agent Leads');
    }

    // Rejected Leads
    try {
        $rejected = $db->fetch("SELECT COUNT(*) AS `total` FROM `users_rejected` WHERE `agent_id` = :agent_id;", ['agent_id' => $agent['id']]);
        $leads['rejected'] = $rejected['total'];
    } catch (PDOException $e) {
        $errors[] = __('Error Loading Rejected Leads');
    }
}

// Format Agent ID
if (!empty($agent['agent_id'])) {
    $agent_id = json_decode($agent['agent_id'], true);
    if (!is_array($agent_id)) {
        $agent_id = array(
            Settings::getInstance()->IDX_FEED => $agent['agent_id'],
        );
    }
    $agent['agent_id'] = $agent_id;
}

// Agent ID Default
if (empty($agent['agent_id'])) {
    $agent['agent_id'] = array(Settings::getInstance()->IDX_FEED => '');
}

// Subdomain Addons
$agent['cms_addons'] = explode(',', $agent['cms_addons']);
sort($agent['cms_addons']);
