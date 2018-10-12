<?php

// Load default "module.js.php"
$javascript = $this->locateFile('module.js.php', __FILE__);
if (!empty($javascript)) {
    require_once $javascript;
}

if (!empty(Settings::getInstance()->MODULES['REW_IDX_DRIVE_TIME']) && in_array('drivetime', Settings::getInstance()->ADDONS)) {
    $dt_javascript = $this->locateFile('../../../../js/idx/drive_time.js');
    if (!empty($dt_javascript)) {
        require_once $dt_javascript;
    }
}

?>
/* <script> */
(function () {
    'use strict';

    // Toggle advanced search panels
    var $filters = $('.search__filters')
    var $submit = $('#quicksearch-submit');
    var $form = $('#<?=$this->getUID() ; ?>');
    var $advanced = $form.find('.adv-search');
    $form.find('a[data-expand]').on('click', function () {
        var $link = $(this);
        if ($link.attr('href')) return true;
        var isOpen = !$link.hasClass('-is-active');
        var label = isOpen ? 'Less Options' : 'More Options';
        $link.find('span').not('.badge').text(label);
        $link.toggleClass('-is-active', isOpen);
        $submit.toggleClass('-is-active', isOpen);
        $filters.toggleClass('-is-active', isOpen);
        $advanced.toggleClass('-is-hidden', !isOpen);
        $advanced.toggleClass('expanded', isOpen);
        return false;
    });

    // Switch currently selected IDX feed
    var $links = $('.idx-feed-select a[data-feed]');
    $links.on('click', function () {
        var $link = $(this);
        var feed = $link.data('feed');
        var href = $link.attr('href');
        $form.find('input[name="idx"]').val(feed);
        $form.find('input[name="feed"]').val(feed);
        $form.find('input.autocomplete').Autocomplete('refresh');
        $form.find('a[data-expand][href]').attr('href', href + '?advanced');
        $links.closest('li').removeClass('-is-current');
        $link.closest('li').addClass('-is-current');
        return false;
    });

    // Switch sort order
    var $sortOrders = $('ul#sort-orders a[data-value!=\'\'][data-value]');
    $sortOrders.on('click', function (event) {
        event.preventDefault();
        var $sortOrder = $(this);
        $sortOrder.closest('ul#sort-orders').siblings('button').text($sortOrder.text());
        $form.find('input[name="sortorder"]').val($sortOrder.data('value'));
        $form.submit();
    });

})();
/* </script> */