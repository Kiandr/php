<meta charset="utf-8">
<title><?=$this->page->info('title'); ?></title>
<?php
if(!empty($this->getFaviconUrl())) { ?>
    <link rel="icon" href="<?= $this->getFaviconUrl(); ?>"/>
<?php }

// Meta Description
if ($this->page->info('meta.description')) echo '<meta name="description" content="' . $this->page->info('meta.description') . '">' . PHP_EOL;

// HTML5 Shiv (IE8)
echo '<!--[if lt IE 9]><script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->' . PHP_EOL;

// Tracking Codes
if ($this->page->info('tracking.verifyv1')) echo '<meta name="google-site-verification" content="' . $this->page->info('tracking.verifyv1') . '">' . PHP_EOL;
if ($this->page->info('tracking.msvalidate')) echo '<meta name="msvalidate.01" content="' . $this->page->info('tracking.msvalidate') . '">' . PHP_EOL;

// Open Graph Images
$images = $this->page->info('og:image');
if (!empty($images)) {
	$images = is_array($images) ? array_slice($images, 0, 10) : array($images);
	foreach ($images as $image) {
		echo '<meta property="og:image" content="' . $image . '">' . PHP_EOL;
	}
}

// Make Listing Info Available For Tracking
global $_COMPLIANCE;
if (!empty($_COMPLIANCE['tracking'])) {
    foreach ($_COMPLIANCE['tracking'] as $service => $data) {
        $trackable_listing = $this->page->info('trackable_listing' . $service);
        if (!empty($trackable_listing)) {
            echo '<meta name="tracking_' . $service . '" content="' . Format::htmlspecialchars(json_encode($trackable_listing)) . '">' . PHP_EOL;
        }
    }
}

// Blog RSS Feed & Pingback
if (!empty(Settings::getInstance()->MODULES['REW_BLOG_INSTALLED'])) {
	echo '<link rel="alternate" type="application/rss+xml" title="Blog RSS Feed" href="' . Http_Host::getDomainUrl() . 'blog/rss/">' . PHP_EOL;
	if ($this->page->info('app') == 'blog' && $_GET['page'] == 'entry') echo '<link rel="pingback" href="' . Http_Host::getDomainUrl() . 'blog/ping.php">' . PHP_EOL;
}

// Link tags
if ($this->page->info('link.canonical')) echo '<link rel="canonical" href="' . $this->page->info('link.canonical') . '">' . PHP_EOL;
if ($this->page->info('link.prev')) echo '<link rel="prev" href="' . $this->page->info('link.prev') . '">' . PHP_EOL;
if ($this->page->info('link.next')) echo '<link rel="next" href="' . $this->page->info('link.next') . '">' . PHP_EOL;

foreach ($this->getStylesheets() as $stylesheet) {
    if (get_class($stylesheet) == 'Source_Code') {
        echo $stylesheet->execute() . PHP_EOL;
    }
}

// Google Universal Analytics
if ($this->page->info('tracking.uacct')) {
?>
<script>
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
    ga('create', '<?=htmlspecialchars($this->page->info('tracking.uacct'));?>', 'auto');
    ga('send', 'pageview');
</script>
<?php
}

// Global Tracking Script
$ppc = Util_CMS::getPPCSettings();
if (!empty($ppc) && $ppc['enabled'] === 'true' && !empty($ppc['global-tracking-script'])) {
    echo $ppc['global-tracking-script'];
}

// JavaScript Resources
foreach ($this->getJavascripts() as $javascipt) {
    if ((get_class($javascipt) == 'Source_Code' && $javascipt->isCritical()) || $javascipt->isAsync()) echo $javascipt->execute() . PHP_EOL;
}

?>

<?php $this->renderCriticalStylesheets(); ?>
