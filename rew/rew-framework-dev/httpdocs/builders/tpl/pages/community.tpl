<?php namespace BDX; ?>

<?php if (!empty($community)) { ?>

	<?php require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/misc/breadcrumbs.tpl'); ?>

	<div id="bdx-community-detail" community-id="<?=$community['SubdivisionID'];?>">

		<h1><?=$community['SubdivisionName']; ?></h1>

		<div class="bdx-col1">
			<div id="bdx-gallery">
				<?php
					require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/misc/community-gallery.tpl');
				?>
			</div>

			<div class="bdx-description">
				<p><?=htmlspecialchars(strip_tags($community['SubDescription'])); ?></p>
			</div>

			<?php if (!empty($community['DrivingDirections'])) { ?>
				<div class="bdx-driving-directions">
					<h4>Driving Directions</h4>
					<p><?=htmlspecialchars(strip_tags($community['DrivingDirections'])); ?></p>
				</div>
			<?php } ?>

			<div class="bdx-features-container">
				<h4>Community Features</h4>
				<ul class="list-group">
					<?php foreach ($features as $label => $value) { ?>
						<?php if (empty($value) || $value == 'N') continue; ?>
						<li><?=htmlspecialchars($label);?></li>
					<?php } ?>
				</ul>
			</div>

		</div>

		<div class="bdx-col2">
			<div class="bdx-properties-cta-container">
				<?php require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/misc/community-properties-cta.tpl'); ?>
			</div>

			<div class="bdx-agent-cta-container">
				<?php require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/misc/agent-cta.tpl'); ?>
			</div>

			<?php if (Settings::getInstance()->FRAMEWORK && !empty(\Settings::getInstance()->MODULES['REW_LENDERS_MODULE'])) { ?>
				<div class="bdx-lender-cta-container">
					<?php require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/misc/lender-cta.tpl'); ?>
				</div>
			<?php } ?>

		</div> <!--/.col2-->

		<?php if (!empty($results) && is_array($results)) { ?>
			<div id="community-listings" class="community-listings-container">
				<h3>Properties in this Community</h3>
				<div class="bdx-listings-grid">
					<?php foreach ($results as $result) {
						require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/results/listing.tpl');
					} ?>
				</div>

				<div class="community-listings-pagination-container">
					<?php
						require(Settings::getInstance()->DIRS['BUILDER'] . 'tpl/misc/pagination.tpl');
					?>
				</div>

				<div class="bdx-provider"><?=Settings::getInstance()->DISCLAIMER;?></div>

			</div>
		<?php } ?>
	</div>

<?php } else { ?>
	<div class="error-message">Selected community not be found.</div>
<?php } ?>

<?php // Output tracker data ?>
<script>var _trackerData = <?=json_encode($BDXTracker);?></script>