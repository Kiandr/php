<?php namespace BDX; ?>

<?php if ($search === 'homes') { ?>
	<?php if (empty($app->snippet)) { ?>
		<div class="listing-results-container">
	<?php } ?>

			<?php
				if (empty($app->snippet)) {
					require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/misc/breadcrumbs.tpl');
				}
			?>
	
			<?php if (empty($app->snippet)) { ?>
				<div class="search-title-container">
					<div class="search-title">
						<?=$searchTitle?>
						<?php if (!empty($total['Communities'])) { ?>
							<p>Currently viewing matching homes. <a href="<?=$communitySearchUrl;?>">View <?=$total['Communities'];?> matching <?=($total['Communities'] == '1' ? 'community' : 'communities');?> &raquo;</a></p>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
	
			<div class="btnset bdx-search-toggle">
				<a href="#" class="btn">Refine Search</a>
			</div>
	
			<?php if (!empty($results) && is_array($results)) { ?>
				<div class="bdx-listings-grid">
					<?php foreach ($results as $result) {
						require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/results/listing.tpl');
					} ?>
				</div>
				
				<div class="search-pagination-container">
					<?php
						require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/misc/pagination.tpl');
					?>
				</div>
	
				<div class="bdx-provider"><?=Settings::getInstance()->DISCLAIMER;?></div>
				
			<?php } else { ?>
				<div class="error-message">No Listings Found.</div>
			<?php } ?>
		
	<?php if (empty($app->snippet)) { ?>
		</div>
	<?php } ?>



<?php } else { ?>

	<?php if (empty($app->snippet)) { ?>
		<div class="community-results-container">
	<?php } ?>
		
			<?php
				if (empty($app->snippet)) {
					require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/misc/breadcrumbs.tpl');
				}
			?>
	
			<?php if (empty($app->snippet)) { ?>
				<div class="search-title-container">
					<div class="search-title">
						<?=$searchTitle?>
						<?php if (!empty($total['Listings'])) { ?>
							<p>Currently viewing matching communities. <a href="<?=$homeSearchUrl;?>">View <?=$total['Listings'];?> matching <?=($total['Listings'] == '1' ? 'home' : 'homes');?> &raquo;</a></p>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
	
			<div class="btnset bdx-search-toggle">
				<a href="#" class="btn">Refine Search</a>
			</div>
	
			<?php if (!empty($results) && is_array($results)) {
				foreach ($results as $result) {
					require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/results/community.tpl');
				} ?>
	
				<div class="search-pagination-container">
					<?php
						require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/misc/pagination.tpl');
					?>
				</div>
	
			<?php }else{ ?>
				<div class="error-message">No Communities Found.</div>
			<?php } ?>
			
	<?php if (empty($app->snippet)) { ?>
		</div>
	<?php } ?>
	
<?php } ?>

<?php // Output tracker data ?>
<script>var _trackerData = <?=json_encode($BDXTracker);?></script>
