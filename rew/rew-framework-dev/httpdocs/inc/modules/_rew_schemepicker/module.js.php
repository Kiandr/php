// <script>
<?php if (!isset($_GET['popup'])) { ?>
setTimeout(function () {
    $.ajax({
        url  : '<?=Settings::getInstance()->URLS['URL_IDX']; ?>inc/php/ajax/html.php',
        data : {
            ajax : true,
            module : '<?=$this->getID(); ?>',
            options : '<?=serialize($this->getConfig()); ?>'
        },
        success : function (html) {

            // Create Module
            $module = $('<div id="<?=$this->getUID(); ?>"></div>').html(html);

            // Module Styles
            $module.css({
                position: 'fixed',
                bottom: 0,
                left: 0,
                padding: '20px',
                background: '#FFFFFF',
                zIndex: 99999999
            });

            // Display Module
            $('body').append($module);

        }
    });
}, 1500);
<?php } ?>