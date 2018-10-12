<?php

if ($show_advanced) {
    require $this->locateFile('ajax.tpl.php');
    return;
}
?>

<div class="fw-idx-filter-container" data-ajax-url="<?= Format::htmlspecialchars($ajax_url); ?>" data-ajax-current-request="<?= Format::htmlspecialchars($current_request); ?>"></div>
