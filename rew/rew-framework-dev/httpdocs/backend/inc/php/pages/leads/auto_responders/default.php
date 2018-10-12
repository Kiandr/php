<?php

// Get Authorization Manager
$leadsAuth = new REW\Backend\Auth\LeadsAuth(Settings::getInstance());

// Authorized to Manage Lead Keywords
if (!$leadsAuth->canManageAutoresponders($authuser)) {
    throw new \REW\Backend\Exceptions\UnauthorizedPageException(
        __('You do not have permission to manage autoresponders.')
    );
}

// Errors
$errors = array();

// Auto-Responders
$autoresponders = array();

// Select Auto-Responders
$query = "SELECT `id`, `title`, `from`, `from_name`, `from_email`, `active` FROM `auto_responders` ORDER BY `title`;";
if ($result = mysql_query($query)) {
    // Build Collection
    while ($row = mysql_fetch_assoc($result)) {
        $autoresponders[] = $row;
    }

    // Super Admin Details
    $result = mysql_query("SELECT `id`, `first_name`, `last_name` FROM `" . LM_TABLE_AGENTS . "` WHERE `id` = 1;");
    $super_admin = mysql_fetch_assoc($result);

// Query Error
} else {
    $errors[] = __('Error Occurred while loading Auto-Responders.');
}
