<?php

// Module Disabled
if (empty(Settings::getInstance()->MODULES['REW_FEATURED_COMMUNITIES'])) return;

// URL to view all communities
$url_communities = '/communities.php';

// Image sizes
$size_image = '352x240/f';
$size_thumb = '107x73/f';

// Require featured community
$community = array_shift($communities);
if (empty($community)) return;

?>
<div class="community">
	<div class="wrap">
		<div id="<?=$this->getUID() ; ?>">
			<h1 class="community-title"><?=$community['title']; ?></h1>
			<h2 class="community-subtitle"><?=$community['subtitle']; ?></h2>
			<a href="<?=$url_communities; ?>" class="buttonstyle absolute-right view-communities-btn">View All Communities</a>
			<div class="grid_12">
				<div class="x4 o0">
					<h3 class="small-caps">Overview</h3>
					<div class="description"><?=$community['description']; ?></div>
				</div>
				<div class="x4 o4">
					<?php if (!empty($community['stats'])) { ?>
						<h3 class="small-caps"><?=$community['stats_heading']; ?></h2>
						<table>
							<tr>
								<td><?=$community['stats_total']; ?>:</td>
								<td class="stats"><?=Format::number($community['stats']['total']); ?></td>
							</tr>
							<tr class="even">
								<td><?=$community['stats_average']; ?>:</td>
								<td class="stats">$<?=Format::number($community['stats']['average']); ?></td>
							</tr>
							<tr>
								<td><?=$community['stats_highest']; ?>:</td>
								<td class="stats">$<?=Format::number($community['stats']['max']); ?></td>
							</tr>
							<tr class="even">
								<td><?=$community['stats_lowest']; ?>:</td>
								<td class="stats">$<?=Format::number($community['stats']['min']); ?></td>
							</tr>
						</table>
					<?php } ?>
					<div class="community-links">
						<a class="buttonstyle colored-bg mini" href="<?=$community['anchor_one_link']; ?>"><?=$community['anchor_one_text']; ?></a>
						<a class="buttonstyle colored-bg mini" href="<?=$community['anchor_two_link']; ?>"><?=$community['anchor_two_text']; ?></a>
					</div>
				</div>
				<?php if (!empty($community['images'])) { ?>
					<div class="x4 o8 last">
						<div class="community-image">
							<?php foreach ($community['images'] as $i => $image) { ?>
								<img
									data-src="<?=str_replace('/' . $thumbnails . '/', '/' . $size_image . '/', $image); ?>"
									src="<?=str_replace('/' . $thumbnails . '/', '/' . $size_image . '/', $placeholder); ?>"
									class="image<?=$i; ?><?=($i === 0) ? '' : ' hidden'; ?>">
							<?php } ?>
						</div>
						<?php if (count($community['images']) > 1) { ?>
							<div class="community-thumbnails">
								<?php foreach ($community['images'] as $i => $image) { ?>
									<a href="#image<?=$i; ?>"><img data-src="<?=str_replace('/' . $thumbnails . '/', '/' . $size_thumb . '/', $image); ?>" src="<?=$placeholder; ?>"></a>
								<?php } ?>
							</div>
						<?php } ?>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>