<?php

// Module Disabled
if (empty(Settings::getInstance()->MODULES['REW_FEATURED_COMMUNITIES'])) return;

// Require featured community
$community = array_shift($communities);
if (empty($community)) return;

// Require community stats
$stats = $community['stats'];
if (empty($stats)) return;
if (empty($stats['total'])) return;

// Over-ride $_REQUEST
$__request = $_REQUEST;
$_REQUEST = $community['search_criteria'];

// Render IDX search form
$idx_search = $this->getPage()->addContainer('idx-search')->addModule('idx-search', array(
	'className'	=> 'snippet-search',
	'button'	=> 'Refine',
	'hideFeed'	=> true,
	'hideSave'	=> true,
	'advanced'	=> true
))->display(false);

// Reset $_REQUEST
$_REQUEST = $__request;

?>
<div id="<?=$this->getUID() ; ?>">

	<h4><?=Format::htmlspecialchars($community['title']); ?> Real Estate</h4>

	<?php /* Property Stats */ ?>
	<h5>Average Home Statistics</h5>
	<div class="keyvalset horizontal">
		<?php if (!empty($stats['beds'])) { ?>
			<div class="keyval">
				<span class="val"><?=Format::number($stats['beds']); ?></span>
				<span class="key">Beds</span>
			</div>
		<?php } ?>
		<?php if (!empty($stats['baths'])) { ?>
			<div class="keyval">
				<span class="val"><?=Format::number($stats['baths']); ?></span>
				<span class="key">Baths</span>
			</div>
		<?php } ?>
		<?php if (!empty($stats['sqft'])) { ?>
			<div class="keyval">
				<span class="val"><?=Format::number($stats['sqft']); ?></span>
				<span class="key">Sqft</span>
			</div>
		<?php } ?>
		<?php if (!empty($stats['acres'])) { ?>
			<div class="keyval">
				<span class="val"><?=$stats['acres']; ?></span>
				<span class="key">Acres</span>
			</div>
		<?php } ?>
		<?php if (!empty($stats['built'])) { ?>
			<div class="keyval">
				<span class="val"><?=$stats['built']; ?></span>
				<span class="key">Built</span>
			</div>
		<?php } ?>
	</div>

	<?php

		$min = $stats['min'];
		$max = $stats['max'];
		$max = $max > 0 && $max < 1000000 ? $max : 1000000;
		$avg = ($stats['average'] / $max) * 100;
		$avg = $avg < 85 ? $avg : 85;
		$avg = $avg > 15 ? $avg : 15;

	?>

	<?php /* Pricing Statistics */ ?>
	<h5>Average Home Prices</h5>
	<div class="scale">
		<div class="scale-wrap">
			<div class="line" style="left: 10%; right: 10%;">
				<span class="keyval low">
					<span class="key">Low</span>
					<span class="val">$<?=Format::shortNumber($stats['min']); ?></span>
				</span>
				<?php if (!empty($stats['average'])) { ?>
					<span class="keyval avg" style="left: <?=$avg; ?>%;">
						<span class="key">Avg</span>
						<span class="val">$<?=Format::shortNumber($stats['average']); ?></span>
					</span>
				<?php } ?>
				<span class="keyval high">
					<span class="key">High</span>
					<span class="val">$<?=Format::shortNumber($stats['max']); ?></span>
				</span>
			</div>
		</div>
	</div>

	<?php

		$max = $stats['max_price_sqft'];
		$max = $max > 0 && $max < 1000 ? $max : 1000;
		$avg = ($stats['avg_price_sqft'] / $max) * 100;
		$avg = $avg < 90 ? $avg : 90;
		$avg = $avg > 10 ? $avg : 10;

	?>

	<?php /* Price Per Square Foot */ ?>
	<h5>Home Price / Sq. Ft</h5>
	<div class="scale">
		<div class="scale-wrap">
			<div class="line" style="left: 10%; right: 10%;">
				<span class="keyval low">
					<span class="key">Low</span>
					<span class="val">$<?=Format::number($stats['min_price_sqft'] ?: 0); ?></span>
				</span>
				<?php if (!empty($stats['avg_price_sqft'])) { ?>
					<span class="keyval avg" style="left: <?=$avg; ?>%;">
						<span class="key">Avg</span>
						<span class="val">$<?=Format::number($stats['avg_price_sqft']); ?></span>
					</span>
				<?php } ?>
				<span class="keyval high">
					<span class="key">High</span>
					<span class="val">$<?=Format::number($stats['max_price_sqft'] ?: 0); ?></span>
				</span>
			</div>
		</div>
	</div>

	<?php /* Community Search Results */ ?>
	<h4>Current Listings in <?=Format::htmlspecialchars($community['title']); ?></h4>
	<?=$idx_search; ?>
	<div class="colset colset-1-sm colset-2-md colset-2-lg colset-2-xl">
		<?php foreach ($community['listings'] as $result) include $result_tpl; ?>
	</div>

	<div class="colset">
		<a href="<?=$community['search_url']; ?>" class="col btn width-1">
			View all <?=Format::htmlspecialchars($community['title']); ?> Listings
		</a>
	</div>

</div>