<?php

// Agent subdomain only
if (!isset(Settings::getInstance()->SETTINGS['team'])) return;

// Background image
$container = $this->getContainer();
$page = $container->getPage();
$img = $page->info('feature_image');
if (!empty($img) && file_exists(DIR_FEATURED_IMAGES . $img)) {
	$img = URL_FEATURED_IMAGES . $img;
} else {
	$img = $page->getSkin()->getUrl() . '/img/agent-bg.jpg';
}

?>
<div class="gallery">
	<div class="slideset">
		<div class="slide">
			<img class="defer" src="/img/util/slideshow.png" data-src="<?=$img; ?>" alt="">
		</div>
	</div>
</div>