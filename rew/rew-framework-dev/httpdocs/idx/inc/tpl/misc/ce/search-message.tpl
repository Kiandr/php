<?php

// Backend user
$backend_user = Auth::get();

// Backend: Create Saved Search
if (!empty($_REQUEST['create_search']) && $backend_user->isValid() && !empty($lead)) {
    echo sprintf(
        '<div class="notice -mar-vertical-sm results">Creating search for: <a href="%s">%s</a></div>',
        Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/searches/?id=' . $lead['id'],
        Format::htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name'])
    );


// Backend: Edit Saved Search
} else if (!empty($saved_search) && !empty($_REQUEST['edit_search']) && $backend_user->isValid() && !empty($lead)) {
    echo sprintf(
        '<div class="notice -mar-vertical-sm results">Refining search for: <a href="%s">%s</a></div>',
        Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/searches/?id=' . $lead['id'],
        Format::htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name'])
    );
}