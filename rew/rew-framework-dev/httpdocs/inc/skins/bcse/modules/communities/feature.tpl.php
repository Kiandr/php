<?php

// Module Disabled
if (empty(Settings::getInstance()->MODULES['REW_FEATURED_COMMUNITIES'])) return;

// No communities added
if (empty($communities)) return;

// URL to view all communities
$url_communities = '/communities.php';

?>
<h2><?=Format::htmlspecialchars($this->config('heading')); ?></h2>
<h3 class="small-caps"><?=Format::htmlspecialchars($this->config('subheading')); ?></h3>
<a class="buttonstyle absolute-right view-communities-btn small-caps" href="<?=$url_communities; ?>">View All Communities</a>
<div class="articleset">
	<?php foreach ($communities as $community) { ?>
		<article>
			<?=(!empty($community['url']) ? '<a href="' . $community['url'] . '">' : ''); ?>
				<img src="<?=$placeholder; ?>" data-src="<?=$community['image']; ?>" alt="<?=Format::htmlspecialchars($community['title']); ?>" />
				<div class="fc-content">
					<h3>
						<span><?=Format::htmlspecialchars($community['title']); ?></span>
						<span class="listing-number icon-home">Listings</span>
					</h3>
				</div>
			<?=(!empty($community['url']) ? '</a>' : ''); ?>
		</article>
	<?php } ?>
</div>