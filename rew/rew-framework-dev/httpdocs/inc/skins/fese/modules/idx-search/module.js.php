<?php

// Load default "module.js.php"
$javascript = $this->locateFile('module.js.php', __FILE__);
if (!empty($javascript)) {
    require_once $javascript;
}

// Require JavaScript Code
$this->addJavascript($_SERVER['DOCUMENT_ROOT'] . '/inc/js/idx/search_tags.js');

?>
/* <script> */
(function () {
    'use strict';

    // Search Form
    var $form = $('#<?=$this->getUID() ; ?>');

    // Toggle advanced search panels
    var $advanced = $form.find('.advanced-options');
    $form.find('.show-advanced').on('click', function (e) {
        e.preventDefault();

        // Toggle anchor text
        var $this = $(this)
            , $text = $this.find('.inner-text')
            , text = $this.data('text') || ''
        ;
        if (text.length < 1) {
            text = $text.text();
            $this.data('text', text);
        }

        // Hide advanced options
        if ($advanced.hasClass('hid')) {
            $advanced.removeClass('hid').addClass('expanded');
            $text.text('Less Options');

        // Show advanced
        } else {
            $advanced.addClass('hid').removeClass('expanded');
            $text.text(text);

        }
    });

    // Switch IDX feed
    var $links = $('.idx-feed-select a[data-feed]').on(BREW.events.click, function () {
        var $link = $(this), feed = $link.data('feed');
        $form.find('input[name="idx"]').val(feed);
        $form.find('input[name="feed"]').val(feed);
        $form.find('input.autocomplete').Autocomplete('refresh');
        $links.removeClass('selected');
        $link.addClass('selected');
    });

})();
/* </script> */
