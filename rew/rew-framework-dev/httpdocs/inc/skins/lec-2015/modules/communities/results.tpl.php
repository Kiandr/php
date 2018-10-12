<?php

// Module Disabled
if (empty(Settings::getInstance()->MODULES['REW_FEATURED_COMMUNITIES'])) return;

// No communities added
if (empty($communities)) return;

// Image sizes
$size_image = '670x500/f';

// Available community tags
$community_tags = array();
foreach ($communities as $community) {
	if (!empty($community['tags'])) {
		$community_tags = array_merge($community_tags, $community['tags']);
	}
}

// Order tags by # of occurrences
$community_tags = array_count_values($community_tags);
arsort($community_tags);

// Search communities by keyword
$search_keywords = array_filter(Format::trim(explode(',', $_GET['search_keyword'])));
if (!empty($search_keywords)) {

	// Filter communities by keywords
	$featured_results = array_filter($communities, function ($community) use ($search_keywords) {

		foreach ($search_keywords as $search_keyword) {
			if (stristr($community['title'], $search_keyword) !== false) return true;
			if (!empty($community['tags']) && stristr(implode(', ', $community['tags']), $search_keyword) !== false) return true;
		}
		return false;
	});

	// Replace search keywords
	$replace_keywords ='#' . implode('|', array_map(function ($search_keyword) {
		return preg_quote($search_keyword);
	}, $search_keywords)) . '#i';

	// Highlight search keywords
	$featured_results = array_map(function ($community) use ($replace_keywords) {
		$community['title'] = preg_replace($replace_keywords, '<mark>\\0</mark>', Format::htmlspecialchars($community['title']));
		$community['tags'] = array_map(function ($tag) use ($replace_keywords) {
			return preg_replace($replace_keywords, '<mark>\\0</mark>', $tag);
		}, $community['tags']);
		return $community;
	}, $featured_results);

} else {

	// Show communities with photos as featured
	$featured_results = array_filter($communities, function ($community) {
		return !empty($community['image']) && !preg_match('#img\/404\.gif#', $community['image']);
	});

}

?>

<?php if (!empty($search_keywords)) { ?>
	<div id="sub-quicksearch" class="text-center">
		<div class="search-criteria">
			<?php foreach ($search_keywords as $search_keyword) { ?>
				<a href="<?=Http_Uri::getUri() . '?search_keyword=' . implode(',', array_diff($search_keywords, array($search_keyword))); ?>" class="buttonstyle mini icon-close">Keyword: "<?=Format::htmlspecialchars($search_keyword); ?>"</a>
			<?php } ?>
		</div>
	</div>
	<?php if (empty($featured_results)) { ?>
		<div class="text-center">
			<p>No featured communities were found matching your keyword.</p>
		</div>
	<?php } ?>
<?php } else if (!empty($community_tags)) { ?>
	<div class="tagset text-center">
		<a class="active" href="#All" data-tag>All (<?=Format::number(count($communities)); ?>)</a>
		<?php foreach ($community_tags as $community_tag => $count) { ?>
			<?php $community_tag = Format::htmlspecialchars($community_tag); ?>
			<a href="#<?=$community_tag; ?>" data-tag="<?=$community_tag; ?>"><?=$community_tag; ?> (<?=Format::number($count); ?>)</a>
		<?php } ?>
	</div>
<?php } ?>

<div class="colset colset-3-lg colset-3-xl">
	<?php foreach ($featured_results as $i => $community) { ?>
		<?php $url = $community['url'] ?: $community['search_url']; ?>
		<div class="col text-center" data-tagged='<?=json_encode($community['tags']); ?>'>
			<div class="photo photo-bordered">
				<?=(!empty($url) ? '<a href="' . $url . '">' : ''); ?>
				<?php if (!empty($community['image'])) { ?>
					<img src="/thumbs/<?=$thumbnails; ?>/img/util/dig_landscape.gif" data-src="<?=str_replace('/' . $thumbnails . '/', '/' . $size_image . '/', $community['image']); ?>" alt="">
				<?php } ?>
				<div class="body">
					<h4><?=$community['title']; ?></h4>
					<div class="tagset"><?=$community['tags'] ? implode(', ', $community['tags']) : '&nbsp;'; ?></div>
				</div>
				<?=(!empty($url) ? '</a>' : ''); ?>
			</div>
		</div>
	<?php } ?>
</div>

<?php

// Filter not in affect
if (empty($search_keywords)) {
	echo '<hr class="spacer">';

	// Re-order for display
	$num = count($communities);
	$max = 4;
	$tmp = array();
	$row = $col = $count = 0;
	$lengths = array();
	$remainder = $num % $max;
	for ($i = 0; $i < $max; $i++) {
		$lengths[$i] = floor($num/$max) + ($i < $remainder ? 1 : 0);
	}

	foreach ($communities as $community) {
		$tmp[$row][$col] = $community;
		$count++;
		$row++;

		if ($count >= $lengths[$col]){
			$row = 0;
			$col++;
			$count = 0;
		}
	}

	$communities = $tmp;


?>
<div class="well">
	<div class="colset colset-2 colset-4-lg colset-4-xl">
		<h3 class="text-center"><span class="filterText">All</span> Communities</h3>
		<div class="nav text-center">
			<ul>
				<?php $i = 0; $j = 0; ?>
				<?php while ($i < $num/$max) { ?>
					<?php while ($j < $max) { ?>
						<?php $url = $communities[$i][$j]['url'] ?: $communities[$i][$j]['search_url']; ?>
						<?php if(!empty($url)) { ?>
							<li data-tagged='<?=htmlspecialchars(json_encode($communities[$i][$j]['tags']));?>' style="float: left; width: 25%;">
								<a href="<?=$url; ?>"><?=Format::htmlspecialchars($communities[$i][$j]['title']); ?></a>
							</li>
						<?php } ?>
						<?php  $j++; ?>
					<?php } $i++; $j = 0;?>
				<?php } ?>
			</ul>
		</div>
	</div>
</div>
<?php
}
