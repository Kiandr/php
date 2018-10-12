<?php

// Exclude from IDX Snippets
if (empty($_REQUEST['snippet'])) {

    // Backend user
    $backend_user = Auth::get();
    $search_message_text = '';

    // Backend: Create Saved Search
    if (!empty($_REQUEST['create_search']) && $backend_user->isValid() && !empty($lead)) {
        $search_message_text = 'Creating search for: <a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/searches/?id=' . $lead['id'] . '">' . Format::htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']) . '</a>';

    // Backend: Edit Saved Search
    } else if (!empty($saved_search) && !empty($_REQUEST['edit_search']) && $backend_user->isValid() && !empty($lead)) {
        $edit_controls = true;
        $search_message_text = 'Refining search for: <a href="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/searches/?id=' . $lead['id'] . '">' . Format::htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']) . '</a>';

    }

    if (!empty($search_message_text)) {
        echo '<div class="uk-alert" data-uk-alert><a href="" class="uk-alert-close uk-close"></a><p>'
                .$search_message_text
                .'</p></div>';
    }

}
