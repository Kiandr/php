<form method="get" class="import">

	<?php if (!empty($show_form)) { ?>
    <div class="bar">
        <div class="bar__title"><?= __('Import MLS&reg; Listing'); ?></div>
        <div class="bar__actions">
            <a class="bar__action" href="/backend/listings/"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
        </div>
    </div>
	<?php } else { ?>

    <input type="hidden" name="feed" value="<?=Settings::getInstance()->IDX_FEED; ?>">

    <div class="bar">
        <div class="bar__title"><?= __('Import MLS&reg; Listing from %s', $searching); ?></div>
        <div class="bar__actions">
            <a class="bar__action" href="/backend/listings/import/"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
        </div>
    </div>

	<div class="btns btns--stickyB"> <span class="R">
		<button class="btn btn--positive group_action" type="submit" disabled>
            <svg class="icon icon-check mar0"><use xlink:href="/backend/img/icos.svg#icon-check"/></svg>
            <span><?= __('Import'); ?></span>
        </button>
        <a href="<?=URL_BACKEND; ?>listings/import" class="btn hidden return"><?= __('Return'); ?></a>
	</div>

	<?php } ?>


	<?php if (!empty($show_form)) { ?>
	<div class="block">
	<p><?= __('You are only permitted to import listings that belong to you'); ?>.</p>
        <div class="cols">
	<?php if (!empty(Settings::getInstance()->IDX_FEEDS)) { ?>
	<div class="field col w1/3">
		<label class="field__label"><?= __('Select Your Feed'); ?></label>
		<select name="feed" class="w1/1" onchange="this.form.submit();">
			<?php foreach (Settings::getInstance()->IDX_FEEDS as $feed => $settings) { ?>
			<?php $selected = Settings::getInstance()->IDX_FEED == $feed ? 'selected' : '';?>
			<option <?=$selected;?> value="<?=htmlspecialchars($feed);?>">
			<?=$settings['title'];?>
			</option>
			<?php } ?>
		</select>
	</div>
	<?php } ?>
	<?php

	// Select Office
	$query = "SELECT SQL_CACHE COUNT(`id`) AS `Listings`, `" . $idx->field('ListingOffice') . "`, `" . $idx->field('ListingOfficeID') . "` FROM `" . $idx->getTable() . "` WHERE `" . $idx->field('ListingOfficeID') . "` IS NOT NULL GROUP BY `" . $idx->field('ListingOfficeID') . "` ORDER BY `" . $idx->field('ListingOfficeID') . "` ASC;";
	if ($result = $db_idx->query($query)) {
		echo '<div class="field col w1/3">';
		echo '<label class="field__label">' . __('Import MLS&reg; Listings by Office') . ':</label>';
		echo '<select class="w1/1" name="office_id" onchange="this.form.submit();">';
		echo '<option value="">--- ' . __('Select Office') . ' ---</option>';
		while ($office = $db_idx->fetchArray($result)) {
			echo '<option value="' . htmlspecialchars($office['ListingOfficeID']) . '">' . htmlspecialchars($office['ListingOfficeID'] . (!empty($office['ListingOffice']) ? ' - ' . $office['ListingOffice'] : '')) . ' (' . Format::number($office['Listings']) . ' ' . Format::plural($office['Listings'], 'Listings', 'Listing') . ')</option>';
		}
		echo '</select>';
		echo '</div>';
	}

	// Select Agent
	$query = "SELECT SQL_CACHE COUNT(`id`) AS `Listings`, `" . $idx->field('ListingAgent') . "`, `" . $idx->field('ListingAgentID') . "` FROM `" . $idx->getTable() . "` WHERE `" . $idx->field('ListingAgentID') . "` IS NOT NULL GROUP BY `" . $idx->field('ListingAgentID') . "` ORDER BY `" . $idx->field('ListingAgentID') . "` ASC;";
	if ($result = $db_idx->query($query)) {
		echo '<div class="field col w1/3">';
		echo '<label class="field__label">' . __('Import MLS&reg; Listings by Agent') . ':</label>';
		echo '<select class="w1/1" name="agent_id" onchange="this.form.submit();">';
		echo '<option value="">--- ' . __('Select Agent') . ' ---</option>';
		while ($agent = $db_idx->fetchArray($result)) {
			echo '<option value="' . htmlspecialchars($agent['ListingAgentID']) . '">' . htmlspecialchars($agent['ListingAgentID'] . (!empty($agent['ListingAgent']) ? ' - ' . $agent['ListingAgent'] : '')) . ' (' . Format::number($agent['Listings']) . ' ' . n__('Listing', 'Listings', $agent['Listings']) . ')</option>';
		}
		echo '</select>';
		echo '</div>';
	}

    ?>
        </div>
	</div>

	<?php } ?>
	<?php if (!empty($listings)) { ?>
	<section id="progress" class="block hidden">
		<h3 id="import-status-title"><?= __('Importing Listings'); ?>&hellip;</h3>
		<div class="field">
			<div class="progress"><span class="ui-progressbar-text"></span></div>
		</div>
		<div class="field">
			<div id="import-errors" class="ui-state-error hidden"></div>
		</div>
	</section>

    <section id="next-import-steps" class="block hidden">
        <a href="<?=$settings->URLS['URL_BACKEND']; ?>listings/import/" class="btn btn--strong"><?=__('Import More Listings'); ?></a>
        <a href="<?=$settings->URLS['URL_BACKEND']; ?>listings/" class="btn btn--default"><?=__('View Listings'); ?></a>
    </section>

	<section id="import-listings">
	<?php
			// Pagination Links
			if (!empty($pagination['links'])) {
				echo '<div class="rewui nav_pagination">';
				if (!empty($pagination['prev'])) echo '<a href="' . $pagination['prev']['url'] . '" class="prev">&lt;&lt;</a>';
				if (!empty($pagination['links'])) {
					foreach ($pagination['links'] as $link) {
						echo '<a href="' . $link['url'] . '" ' . (!empty($link['active']) ? ' class="current"' : '') . '>' . $link['link'] . '</a>';
					}
				}
				if (!empty($pagination['next'])) echo '<a href="' . $pagination['next']['url'] . '" class="next">&gt;&gt;</a>';
				echo '</div>';
			}

   ?>



    <div class="block">
        <?php if (!empty($new_listings)) { ?>
            <label class="toggle">
                <input type="checkbox" id="check_all" class="checkbox">
                <span class="toggle__label">
                    <?= n__('Found %s MLS&reg; Listing','Found %s MLS&reg; Listings', Format::number($count['total']), Format::number($count['total'])); ?>
                </span>
            </label>
        <?php } else { ?>
            <label><?=__('%s listings have already been imported.', $searching); ?></label>
        <?php } ?>
    </div>

  <div class="nodes">
	<ul class="nodes__list">
		<?php foreach ($listings as $listing) { ?>
		<li <?=(!empty($listing['imported']) ? ' class="nodes__branch ui-state-disabled" title="' . __('This listing has already been imported.') . '"' : ' class="nodes__branch listing"'); ?>>


            <div class="nodes__wrap">
                <div class="nodes__toggle"><input type="checkbox" value="<?=Format::htmlspecialchars($listing['ListingMLS']); ?>" name="listings[]"<?=(!empty($listing['imported']) ? ' disabled' : ''); ?>></div>
            	<div class="article">
                    <div class="article__body">
                        <div class="article__thumb thumb thumb--medium"><img src="<?=Format::thumbUrl($listing['ListingImage'], '60x60'); ?>" alt=""></div>
                        <div class="article__content">
                            <a class="text text--strong" href="<?=$listing['url_details']; ?>" target="_blank"> <?=Format::htmlspecialchars($listing['Address']); ?> (<?= __('MLS&reg; #'); ?><?=Format::htmlspecialchars($listing['ListingMLS']); ?>)</a>
 							<div class="text text--mute">$<?=Format::number($listing['ListingPrice']); ?>, <?=Format::htmlspecialchars($listing['ListingType']); ?> -
							<?=!empty($listing['AddressSubdivision']) ? Format::htmlspecialchars(ucwords(strtolower($listing['AddressSubdivision']))) . ', ' : ''; ?>
							<?=Format::htmlspecialchars(ucwords(strtolower($listing['AddressCity']))); ?>,
							<?=Format::htmlspecialchars(ucwords(strtolower($listing['AddressState']))); ?>
							<?=Format::htmlspecialchars($listing['AddressZipCode']); ?>
                        </div>
                    </div>
            	</div>
            	<div class="nodes__actions"></div>
            </div>



		</li>
		<?php } ?>
	</ul>
</div>

	<?php
			// Pagination Links
			if (!empty($pagination['links'])) {
				echo '<div class="rewui nav_pagination">';
				if (!empty($pagination['prev'])) echo '<a href="' . $pagination['prev']['url'] . '" class="prev">&lt;&lt;</a>';
				if (!empty($pagination['links'])) {
					foreach ($pagination['links'] as $link) {
						echo '<a href="' . $link['url'] . '" ' . (!empty($link['active']) ? ' class="current"' : '') . '>' . $link['link'] . '</a>';
					}
				}
				if (!empty($pagination['next'])) echo '<a href="' . $pagination['next']['url'] . '" class="next">&gt;&gt;</a>';
				echo '</div>';
			}

       ?>
	<?php } ?>
	</section>
</form>
