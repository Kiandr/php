<?php namespace BDX; ?>

<?php if (!empty($listing)) { ?>

	<?php
		require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/misc/breadcrumbs.tpl');
	?>

	<div id="bdx-listing-detail" listing-id="<?=$listing['ListingID'];?>">
		<h1><?=$listing['PlanName']; ?> (<?=$listing['ListingType'];?>)</h1>

		<div class="bdx-col1">
			<div id="bdx-gallery">
				<?php
					require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/misc/listing-gallery.tpl');
				?>
			</div>

			<div class="listing-description">
				<p><?=$listing['Description']; ?></p>
			</div>

			<?php if (!empty($listingDetails) && is_array($listingDetails)) { ?>
				<div class="bdx-listing-info">
					<?php foreach ($listingDetails as $listingDetail) { ?>
						<div class="listing-detail-section">
							<h3><?=$listingDetail['heading'];?></h3>
							<ul>
								<?php foreach ($listingDetail['fields'] as $listingDetailSection) { ?>
									<li>
										<strong><?=$listingDetailSection['title'];?>: </strong>
										<span>
											<?php if(!empty($listing['BrandNameLink']) && $listingDetailSection['title'] == 'Builder Name') { ?>
												<a href="<?=$listing['BrandNameLink'];?>"><?=$listingDetailSection['value'];?></a>
											<?php } else { ?>
												<?=$listingDetailSection['value'];?>
											<?php } ?>
										</span>
									</li>
								<?php } ?>
							</ul>
						</div>
					<?php } ?>
				</div>
			<?php } ?>

			<?php if (!empty($communityDetails) && is_array($communityDetails) && !empty($community)) { ?>
				<div class="bdx-community-info">
					<?php foreach ($communityDetails as $communityDetail) { ?>
						<div class="community-detail-section">
							<h3><?=$communityDetail['heading'];?></h3>
							<ul>
								<?php foreach ($communityDetail['fields'] as $communityDetailSection) { ?>
									<li>
										<strong><?=$communityDetailSection['title'];?>: </strong>
										<span>
											<?php if(!empty($communityDetailSection['link'])) { ?>
												<a href="<?=$community[$communityDetailSection['link']];?>"><?=$communityDetailSection['value'];?></a>
											<?php } else { ?>
												<?=$communityDetailSection['value'];?>
											<?php } ?>
										</span>
									</li>
								<?php } ?>
							</ul>
						</div>
					<?php } ?>
				</div>
			<?php } ?>

		</div><!--/.col1-->

		<div class="bdx-col2">
			<div class="bdx-agent-cta-container">
				<?php require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/misc/agent-cta.tpl'); ?>
			</div>
			
			<?php if (Settings::getInstance()->FRAMEWORK && !empty(\Settings::getInstance()->MODULES['REW_LENDERS_MODULE'])) { ?>
				<div class="bdx-lender-cta-container">
					<?php require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/misc/lender-cta.tpl'); ?>
				</div>
			<?php } ?>

		</div> <!--/.col2-->
	</div>

	<div class="bdx-similar-listings-container">
		<h3>Similar Properties</h3>
		<?php if (!empty($results) && is_array($results)) { ?>
		<div class="bdx-listings-grid">
			<?php foreach ($results as $result) {
				require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/results/listing.tpl');
			} ?>
		</div>

		<div class="similar-listings-pagination-container">
			<?php
				require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/misc/pagination.tpl');
			?>
		</div>

		<?php }else{ ?>
			<div class="error-message">No Listings Found.</div>
		<?php } ?>

		<div class="bdx-provider"><?=Settings::getInstance()->DISCLAIMER;?></div>

	</div>

<?php } else { ?>
	<div class="error-message">Selected listing could not be found.</div>
<?php } ?>

<?php // Output tracker data ?>
<script>var _trackerData = <?=json_encode($BDXTracker);?></script>