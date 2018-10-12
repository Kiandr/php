<script>
var cb = function() {
    var l = null;
    var h = null;

    <?php

    // Stylesheet Resources
    foreach ($this->getStylesheets() as $stylesheet) {
        $stylesheetUrl = NULL;
        if ($stylesheet instanceof \Source_Link) {
            $stylesheetUrl = $stylesheet->getLink();
        } elseif ($stylesheet instanceof \Source_File) {
            $stylesheetUrl = str_replace($_SERVER['DOCUMENT_ROOT'], '', $stylesheet->getFile());
        }
        if (empty($stylesheetUrl)) {
            continue;
        }
    ?>
        l = document.createElement('link'); l.rel = 'stylesheet';
        l.href = '<?= Format::htmlspecialchars($stylesheetUrl); ?>';
        h = document.getElementsByTagName('head')[0];
        h.appendChild(l);
    <?php } ?>
};

var raf = requestAnimationFrame || mozRequestAnimationFrame ||
webkitRequestAnimationFrame || msRequestAnimationFrame;
if (raf) raf(cb);
else window.addEventListener('load', cb);
</script>

<?php

// JavaScript Resources
foreach ($this->getJavascripts() as $javascript) {
    if (((get_class($javascript) !== 'Source_Code') || (get_class($javascript) == 'Source_Code' && !$javascript->isCritical())) && !$javascript->isAsync()) echo $javascript->execute() . PHP_EOL;
}

?>

<!--[if (gte IE 6)&(lte IE 8)]><script src="/inc/skins/brew/js/selectivizr-min.js"></script><![endif]-->
<?php

// HitTail Tracker
if ($this->page->info('tracking.hittail')) {

?>
<script>
  (function(){ var ht = document.createElement('script');ht.async = true;
    ht.type='text/javascript';ht.src = '//<?php echo $this->page->info('tracking.hittail'); ?>.hittail.com/mlt.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ht, s);})();
</script>
<?php

}

?>
