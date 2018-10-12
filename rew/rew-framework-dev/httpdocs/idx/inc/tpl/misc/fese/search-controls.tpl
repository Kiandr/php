<?php

// Can save search
$can_save = ($idx->getLink() !== 'cms');

// Backend user
$backend_user = Auth::get();

// Backend: Create saved search
if (!empty($_REQUEST['create_search']) && $backend_user->isValid() && !empty($lead) && !empty($can_save)) {
    echo '<a class="mnu-item" id="save-search">Save Search</a>' . PHP_EOL;

// Backend: Edit saved search
} else if (!empty($_REQUEST['edit_search']) && $backend_user->isValid() && !empty($lead)) {

    // Must refine
    if (empty($can_save)) {
        echo sprintf('<a class="mnu-item" title="%s"><s>%s</s></a>',
            'Must Refine to Save Changes',
            'Save Changes'
        );

    // Can save
    } else {
        echo '<a class="mnu-item" id="edit-search">Save Changes</a>' . PHP_EOL;
        echo '<a class="mnu-item" id="edit-search-email">Save and Email Results</a>' . PHP_EOL;

    }

    // Link to delete saved search
    echo sprintf('<a class="mnu-item" href="%s" onclick="return confirm(\'%s\');">%s</a>',
        Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/searches/?id=' . $lead['id'] . '&delete=' . $saved_search['id'],
        'Are you sure you want to delete this saved search?',
        'Delete'
    );

// Edit Saved Search
} else if (!empty($saved_search) && !empty($_REQUEST['edit_search'])) {
    echo '<a class="mnu-item" id="edit-search">Save Changes</a>' . PHP_EOL;
    echo '<a class="mnu-item" id="edit-search-email">Save and Email Results</a>' . PHP_EOL;

// Viewing saved search
} else if (!empty($saved_search)) {

    // Link to edit saved search
    echo sprintf('<a class="mnu-item" href="%s">%s</a>',
        '?edit_search=true&saved_search_id=' . $saved_search['id'],
        'Edit Search'
    );

    // Delete this saved search by sending POST request to IDX dashboard
    echo '<form action="/idx/dashboard.html?view=searches" method="post" onsubmit="return confirm(\'Are you sure you want to remove this search?\');">';
    echo '<button type="submit" class="mnu-item">Delete</button>';
    echo '<input name="delete" value="' . Format::htmlspecialchars($saved_search['id']) . '" type="hidden">';
    echo '</form>';

// Can save this search
} else if (!empty($can_save)) {
    echo '<a class="mnu-item" id="save-search">Save Search</a>' . PHP_EOL;

}