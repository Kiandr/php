<?php

// Full Page
$body_class = 'full';

// Get Authorization Managers
$textAuth = new REW\Backend\Auth\TextAuth(Settings::getInstance());

// Authorized to text any leads
if (!$textAuth->canTextLeads($authuser)) {
    // Require permission to text own leads
    if (!$textAuth->canTextOwnLeads($authuser)) {
        throw new \REW\Backend\Exceptions\UnauthorizedPageException(
            __('You do not have permission to set up text autoresponders.')
        );
    } else {
        header('Location: edit/');
        exit;
    }
}

try {
    // DB connection
    $db = DB::get();

    // User feedback
    $errors = array();
    $success = array();

    // Load auto-responders
    $query = $db->query("SELECT `ta`.*, `a`.`id` AS `agent_id`, CONCAT(`a`.`first_name`, ' ', `a`.`last_name`) AS `agent_name` FROM `agents` `a` LEFT JOIN `twilio_autoresponder` `ta` ON `ta`.`agent_id` = `a`.`id` ORDER BY IF(`ta`.`id` IS NULL, 0, 1) DESC, `ta`.`active` DESC, `agent_name` ASC;");
    $autoresponders = $query->fetchAll();

    // If there is an agent without an auto-responder - show the setup button
    $can_setup = array_reduce($autoresponders, function ($can_setup, $autoresponder) {
        return $can_setup || empty($autoresponder['id']);
    });

// Database error occurred
} catch (PDOException $e) {
    $errors[] = __('An error occurred while working with the database.');
    //$errors[] = $e->getMessage();
}
