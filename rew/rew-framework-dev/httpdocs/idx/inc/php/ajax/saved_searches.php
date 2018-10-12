<?php

// Include IDX Configuration
include_once $_SERVER['DOCUMENT_ROOT'] . '/idx/common.inc.php';

// Send as Plain Text
header("Content-Type: text/plain");

// Invalid User
if (!$user->isValid()) {
    return;
}

// Close session
@session_write_close();

// Search Fields
$search_fields = array();
$fields = search_fields($idx);
foreach ($fields as $k => $v) {
    $search_fields[$v['form_field']] = $v;
}

// Load Saved Searches
$searches = array();
$saved_searches = $db_users->query("SELECT * FROM `" . TABLE_SAVED_SEARCHES . "` WHERE `user_id` = '" . $user->user_id() . "' ORDER BY `timestamp_created` DESC");
while ($saved_search = $db_users->fetchArray($saved_searches)) {
    // Multi-IDX
    try {
        $search_idx = Util_IDX::getIdx($saved_search['idx']);
        $search_db = Util_IDX::getDatabase($saved_search['idx']);

    // Error occurred
    } catch (Exception $e) {
        Log::error($e);
        continue;
    }

    // Check IDX Resource
    if ($search_idx instanceof $idx) {
        $searches[] = $saved_search;
    }
}

?>
<?php if (!empty($searches)) : ?>
    <?php foreach ($searches as $search) :?>
    <div class="summary">
           <strong><a href="<?=sprintf(Settings::getInstance()->SETTINGS['URL_IDX_SAVED_SEARCH'], $search['id']); ?>"><?=$search['title']; ?></a></strong>
    </div>
    <?php endforeach; ?>
<?php else : ?>
    <div>
        <a href="javascript:void(0);"><em>You currently have no saved searches.</em></a>
    </div>
<?php endif; ?>