<?php namespace BDX; ?>

<h1>Builder <?=$meta_type;?> Sitemap</h1>

<div class="tabset pills sitemap-community-listing-switcher">
	<ul>
		<li class="sitemap-listing<?=(empty($_GET['type']) ? ' current' : '');?>"><a href="/builders/sitemap/">Listing</a></li>
		<li class="sitemap-community<?=(($_GET['type'] == 'community') ? ' current' : '');?>"><a href="?type=community">Community</a></li>
	</ul>
</div>

<div class="sitemap-results-container">
	<?php if (!empty($groups) && is_array($groups)) { ?>
		<?php foreach ($groups as $group => $results) { ?>
			<h2><?=$group;?></h2>
			<?php if (!empty($results) && is_array($results)) { ?>
				<ul>
					<?php foreach ($results as $result) { ?>
						<?php if ($result['Type'] == 'Listing') { ?>
							<li><a href="<?=$result['Link']; ?>">#<?=$result['ListingID'] . ' ' . $result['Address'] . ', ' . $result['City'] . ', ' . $result['State'];?></a></li>
						<?php } else { ?>
							<li><a href="<?=$result['Link']; ?>"><?=$result['SubdivisionName'] . ', ' . $result['City'] . ', ' . $result['State'];?></a></li>
						<?php } ?>
					<?php } ?>
				</ul>
			<?php } ?>
		<?php } ?>
	<?php } ?>
</div>

<div class="sitemap-pagination-container">
	<?php
		require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/misc/pagination.tpl');
	?>
</div>