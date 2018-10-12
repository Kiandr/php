<div id="directory-details" class="uk-grid">
    <?php if (!empty($entry['logo'])) { ?>
	    <div class="uk-width-small-1-1 uk-width-medium-2-4 uk-width-large-2-5"><img src="/thumbs/190x100/<?=$entry['logo']; ?>" alt="" class="uk-width-*"></div>
		<div class="uk-width-small-1-1 uk-width-medium-2-4 uk-width-large-3-5">
	<?php } else { ?>
		<div class="uk-width-small-1-1 uk-width-medium-2-4 uk-width-large-3-5">
	<?php } ?>
		<h1 class="uk-margin-small-bottom"><?=Format::htmlspecialchars($entry['business_name']); ?></h1>
		<ul class="uk-list uk-margin-remove">
			<?php if (!empty($entry['address'])) { ?>
				<li class="keyval uk-margin-bottom uk-h3">
					<span><?=Format::htmlspecialchars($entry['address']); ?></span>
				</li>
			<?php } ?>
			<?php if (!empty($entry['phone'])) { ?>
				<li class="keyval">
					<strong>Phone #</strong>
					<span><?=Format::htmlspecialchars($entry['phone']); ?></span>
				</li>
			<?php } ?>
			<?php if (!empty($entry['alt_phone'])) { ?>
				<li class="keyval">
					<strong>Secondary Phone #</strong>
					<span><?=Format::htmlspecialchars($entry['alt_phone']); ?></span>
				</li>
			<?php } ?>
			<?php if (!empty($entry['toll_free'])) { ?>
				<li class="keyval">
					<strong>Toll Free #</strong>
					<span><?=Format::htmlspecialchars($entry['toll_free']); ?></span>
				</li>
			<?php } ?>
			<?php if (!empty($entry['fax'])) { ?>
				<li class="keyval">
					<strong>Fax #</strong>
					<span><?=Format::htmlspecialchars($entry['fax']); ?></span>
				</li>
			<?php } ?>
			<?php if (!empty($entry['website'])) { ?>
				<li class="keyval">
					<strong>Website</strong>
					<span><?=$entry['website']; ?></span>
				</li>
			<?php } ?>
		</ul>
	</div>
	<div class="description uk-width-1-1 uk-margin-large"><?=$entry['description']; ?></div>
	<?php

		// Include Photo Gallery
		include $page->locateTemplate('directory', 'misc', 'gallery');

	?>
</div>
<?php

// Show Map
if (!empty($entry['latitude']) && !empty($entry['longitude']) && !empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) {
?>

	<div class="uk-width-1-1 uk-margin-large" id="directory-map" class="uk-hidden" data-latitude="<?= $entry['latitude']; ?>" data-longitude="<?= $entry['longitude']; ?>"></div>

<?php } ?>
