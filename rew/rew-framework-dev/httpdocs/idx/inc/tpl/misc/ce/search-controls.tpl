<?php

// Can save search
$can_save = $idx->getLink() !== 'cms' && (
    isset($_GET['auto_save']) || ($search_results_count['total'] <= 500)
);

// Backend user
$backend_user = Auth::get();

// Backend: Create saved search
if (!empty($_REQUEST['create_search']) && $backend_user->isValid() && !empty($lead) && !empty($can_save)) {
    echo sprintf('<a href="#%s" class="button button--ghost save--search" id="save-search">', $idx->getLink());
	echo '<svg class="button__icon icon--heart">';
    echo '<title>Heart icon for Save Search</title>';
	echo '<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/inc/skins/ce/img/assets.svg#icon--heart"></use>';
	echo '</svg>';
	echo '<span class="button__label">Save Search</span></a>' . PHP_EOL;

// Backend: Edit saved search
} else if (!empty($_REQUEST['edit_search']) && $backend_user->isValid() && !empty($lead)) {

    // Must refine
    if (empty($can_save)) {
        echo sprintf('<a href="#%s" class="button button--ghost -inline button--sm -text-xs" title="%s"><s>%s</s></a>',
            $idx->getLink(),
            'Must Refine to Save Changes',
            'Save Changes'
        );

    // Can save
    } else {
        echo sprintf('<a href="#%s" class="button button--sm -text-xs button--ghost -inline" id="edit-search">Save Changes</a>', $idx->getLink()) . PHP_EOL;
        echo sprintf('<a href="#%s" class="button button--sm -text-xs button--ghost -inline" id="edit-search-email">Save and Email Results</a>', $idx->getLink()) . PHP_EOL;

    }

    // Link to delete saved search
    echo sprintf('<a class="button button--sm -text-xs button--ghost" href="%s" onclick="return confirm(\'%s\');">%s</a>',
        Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/lead/searches/?id=' . $lead['id'] . '&delete=' . $saved_search['id'] . '#' . $idx->getLink(),
        'Are you sure you want to delete this saved search?',
        'Delete'
    );

// Edit Saved Search
} else if (!empty($saved_search) && !empty($_REQUEST['edit_search'])) {
    echo sprintf('<a href="#%s" class="button button--sm -text-xs button--ghost" id="edit-search">Save Changes</a>', $idx->getLink()) . PHP_EOL;
    echo sprintf('<a href="#%s" class="button button--sm -text-xs button--ghost" id="edit-search-email">Save and Email Results</a>', $idx->getLink()) . PHP_EOL;

// Viewing saved search
} else if (!empty($saved_search)) {

    // Delete this saved search by sending POST request to IDX dashboard
    echo '<form class="-inline" id="form-delete-search" action="/idx/dashboard.html?view=searches" method="post" onsubmit="return confirm(\'Are you sure you want to remove this search?\');">';
    // Link to edit saved search
    echo sprintf('<a class="button button--sm -text-xs button--ghost button--ghost" href="%s">%s</a>',
        '?edit_search=true&saved_search_id=' . $saved_search['id'] . '#' . $idx->getLink(),
        'Edit Search'
    );
    echo '<a class="button button--sm -text-xs button--ghost" onclick="$(\'#form-delete-search\').submit()"><svg class="button__icon icon icon--xs"><use xlink:href="/inc/skins/ce/img/assets.svg#icon--trash"/></svg></a>';
    echo '<input name="delete" value="' . Format::htmlspecialchars($saved_search['id']) . '" type="hidden">';
    echo '</form>';

// Can save this search
} else if (!empty($can_save)) {
    echo sprintf('<a href="#%s" class="button button--sm -text-xs button--ghost save--search" id="save-search">', $idx->getLink());
	echo '<svg class="button__icon icon icon--xs icon--heart">';
    echo '<title>Heart icon for Save Search</title>';
	echo '<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/inc/skins/ce/img/assets.svg#icon--heart"></use>';
	echo '</svg>';
	echo 'Save Search</a>' . PHP_EOL;

// Show disabled button if IDX feed
} else if ($idx->getLink() !== 'cms') {
    echo '<a href="#' . $idx->getLink() . '" onclick="alert(\'Refine your Search to 500 results or less to Save.\'); return false;" class="-mar-horizontal-xs button button--sm -text-xs button--ghost save--search -is-disabled" id="save-search-disabled" title="Refine your Search to 500 results or less to Save.">';
	echo '<svg class="icon__button icon--heart icon icon--xs">';
    echo '<title>Heart icon for Save Search</title>';
	echo '<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/inc/skins/ce/img/assets.svg#icon--heart"></use>';
	echo '</svg>';
	echo 'Save Search</a>' . PHP_EOL;
}