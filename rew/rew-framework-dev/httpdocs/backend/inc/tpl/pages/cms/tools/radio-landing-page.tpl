<form id="pod-form" action="?submit" method="post">

	<h2><?= __('Radio Landing Page'); ?></h2>

	<p><?= __('This tool controls the content of the #radio-landing-page# module snippet.'); ?></p>



	<div class="block block--bg marB">
		<div id="pod-contents">
			<h3><?= __('Active Content Pods'); ?> <small><?= __('These will display on the front-end of the site'); ?></small></h3>
			<ul id="pods-active" class="nodes pods-sortable" style="min-height: 24px">
				<?php
		        foreach ($pods['active'] as $pod) {
		        	echo $pod['output'];
		        }
		        ?>
			</ul>
		</div>
	</div>

	<div class="block block--bg marB">
		<h3><?= __('Inactive Content Pods'); ?> <small><?= __('Return pods to this section to disable them'); ?></small> </h3>
		<ul id="pods-inactive" class="nodes pods-sortable">
			<?php
	        foreach ($pods['inactive'] as $pod) {
	        	echo $pod['output'];
	        }
	        ?>
		</ul>
	</div>


	<div class="btns">
		<a id="add-custom-pod" class="btn" href="#"><svg class="icon icon-add"><use xlink:href="/backend/img/icos.svg#icon-add"/></svg> <?= __('Add Custom Blank Pod'); ?></a>
	</div>
	<br><br>

	<fieldset>
		<h3><?= __('As Heard On'); ?></h3>

		<p><?= __('Set of logos to display within the following pods:'); ?>
			<?=$as_heard_on_tag; ?>
			<?= __('Recommended Dimensions'); ?>: 75x35</p>

	<div id="aho-uploader">
		<?php if (!empty($uploads['landing_radio_aho'])) { ?>
		<div class="file-manager">
			<ul>
				<?php foreach ($uploads['landing_radio_aho'] as $upload) { ?>
				<li>
					<div class="wrap"> <img src="/thumbs/95x95/uploads/<?=$upload['file']; ?>" border="0">
						<input type="hidden" name="uploads[]" value="<?=$upload['id']; ?>">
					</div>
				</li>
				<?php } ?>
			</ul>
		</div>
		<?php } ?>
	</div>

	</fieldset>

	<div class="btns btns--stickyB"> <span class="R">
		<button class="btn btn--positive"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?> </button>
		</span>
	</div>

	<h3><?= __('Pages Used On'); ?></h3>
	<?php if (!empty($used_on_pages)) { ?>

		<?php foreach ($used_on_pages as $pg) { ?>
			<div><a href="<?=$pg['href']; ?>">
			<?=$pg['text']; ?>
			</a></div>
		<?php } ?>

	<?php } else { ?>

		<p><?= __('This snippet is currently not being used on any pages.'); ?></p>

	<?php } ?>


</form>