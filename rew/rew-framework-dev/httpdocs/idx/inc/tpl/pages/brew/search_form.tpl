<?php

// Add Core Stylesheet
$this->getSkin()->addStylesheet('css/core/idx/search_form.less');

?>

<div class="tabset">
	<ul class="clearfix">
		<li<?=(empty($_REQUEST['search_by']) || $_REQUEST['search_by'] == 'city' ? ' class="current"' : ''); ?>><a href="<?=Settings::getInstance()->URLS['URL_IDX']; ?>">By City</a></li>
		<li<?=($_REQUEST['search_by'] == 'subdivision' ? ' class="current"' : ''); ?>><a href="<?=Settings::getInstance()->URLS['URL_IDX']; ?>search_subdivision.html">By Subdivision</a></li>
		<li<?=($_REQUEST['search_by'] == 'zip' ? ' class="current"' : ''); ?>><a href="<?=Settings::getInstance()->URLS['URL_IDX']; ?>search_zip.html">By <?=Locale::spell('Zip Code'); ?></a></li>
		<li<?=($_REQUEST['search_by'] == 'mls' ? ' class="current"' : ''); ?>><a href="<?=Settings::getInstance()->URLS['URL_IDX']; ?>search_mls.html">By <?=Lang::write('MLS'); ?> Number</a></li>
		<?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_MAPPING'])) { ?>
			<li><a href="<?=Settings::getInstance()->URLS['URL_IDX_MAP']; ?>">By Map</a></li>
		<?php } ?>
	</ul>
</div>

<form id="searchForm" action="<?=Settings::getInstance()->SETTINGS['URL_IDX_SEARCH']; ?>" method="get">

	<input type="hidden" name="refine" value="true">
	<input type="hidden" name="search_by" value="<?=Format::htmlspecialchars($_REQUEST['search_by']); ?>">

	<?php if ($_REQUEST['search_by'] == 'mls') { ?>

		<div class="field x12 o0">

			<h5 class="legend">Search by <?=Lang::write('MLS'); ?> Number</h5>

			<div class="fieldgroup extended">
				<span class="prelabel"><?=Lang::write('MLS'); ?> Number:</span>
				<input name="search_mls" value="<?=Format::htmlspecialchars($_REQUEST['search_mls']); ?>">
				<span class="tip">To search multiple <?=Lang::write('MLS'); ?> numbers, separate values with commas.</span>
			</div>

		</div>

		<div class="btnset clearfix">
			<input type="hidden" name="union" value="true">
			<button type="submit" name="search_submit" value="Search" class="strong"><?=Lang::write('IDX_SEARCH_BUTTON');?></button>
		</div>

	<?php } else { ?>

		<?php if ($_REQUEST['search_by'] == "zip") { ?>

			<div class="field x12 o0">

				<h5 class="legend">1. Search By <?=Locale::spell('Zip Code'); ?></h5>

				<div class="fieldgroup extended">
					<span class="prelabel"><?=Locale::spell('Zip Code'); ?>:</span>
					<input class="autocomplete location" name="search_zip" value="<?=Format::htmlspecialchars($_REQUEST['search_zip']); ?>">
					<span class="tip">To search multiple <?=Locale::spell('Zip Codes'); ?>, separate values with commas.</span>
				</div>

			</div>

		<?php } elseif ($_REQUEST['search_by'] == "subdivision") { ?>


			<div class="field x12 o0">

				<h5 class="legend">1. Search By Subdivision</h5>

				<div class="fieldgroup extended">
					<span class="prelabel">Subdivision:</span>
					<input class="autocomplete location" name="search_subdivision" value="<?=Format::htmlspecialchars($_REQUEST['snippet_subdivision']); ?>">
					<span class="tip">To search multiple Subdivisions, separate values with commas.</span>
				</div>

			</div>

		<?php } else { ?>

			<div class="field x12 o0">

				<h5 class="legend">1. Choose Your Cities</h5>

				<div class="field toggle">
					<div class="toggleset">
						<?php if ($city && ($options = $city->getOptions())) { ?>
							<?php foreach ($options as $option) { ?>
								<label><input type="checkbox" name="search_city[]" value="<?=$option['value']; ?>"<?=(is_array($_REQUEST['search_city']) && in_array_nocase($option['value'], $_REQUEST['search_city'])) ? ' checked' : ''; ?>> <?=$option['title']; ?></label>
							<?php } ?>
						<?php } ?>
					</div>
				</div>

			</div>

		<?php } ?>

		<div class="field x12 o0">

			<h5 class="legend">2. Select a Property Type</h5>

			<div class="fieldgroup toggle clearfix">
				<?php if ($type && ($options = $type->getOptions())) { ?>
					<?php foreach ($options as $option) { ?>
						<label><input type="radio" name="search_type" value="<?=$option['value']; ?>"<?=(is_array($_REQUEST['search_type']) && in_array_nocase($option['value'], $_REQUEST['search_type'])) ? ' checked' : ''; ?>> <?=$option['title']; ?></label>
					<?php } ?>
				<?php } ?>
				<input type="hidden" name="idx" value="<?=$idx->getLink(); ?>">
			</div>

		</div>

		<div class="field x12 o0">

			<h5 class="legend">3. Choose Your Property Features</h5>

			<?php $price->display(); ?>

			<div class="field">
				<label>Rooms</label>
				<span class="min">
					<span class="prelabel"><abbr title="Minimum Bedrooms">Min. Beds</abbr></span>
					<?=$rooms->getMinBeds(); ?>
				</span>
				<span class="max">
					<span class="prelabel"><abbr title="Minimum Bathrooms">Min. Baths</abbr></span>
					<?=$rooms->getMinBaths(); ?>
				</span>
			</div>

			<div class="field toggle">

				<h5 class="legend">4. Extra Options</h5>

				<?php if ($sqft && $sqft->isAvailable()) { ?>
					<span class="min">
						<span class="prelabel"><abbr title="Minimum Square Footage">Living Space</abbr></span>
						<?=$sqft->getMinMarkup(); ?>
					</span>
				<?php } ?>

				<?php if ($acres && $acres->isAvailable()) { ?>
					<span class="max">
						<span class="prelabel"><abbr title="Minimum Acreage">Lot Size</abbr></span>
						<?=$acres->getMinMarkup(); ?>
					</span>
				<?php } ?>

				<?php if ($year && $year->isAvailable()) { ?>
					<span class="min">
						<span class="prelabel">Built After</span>
						<?=$year->getMinMarkup(); ?>
					</span>
				<?php } ?>

			</div>

		</div>

		<div class="btnset clearfix">
			<button type="submit" name="search_submit" value="Search" class="strong"><?=Lang::write('IDX_SEARCH_BUTTON');?></button>
		</div>

	<?php } ?>

</form>

<?php ob_start(); ?>
/* <script> */
(function () {
	'use strict';

	// Refine Form
	var $form = $('#searchForm');

	// Range Inputs
	$form.find('.range').each(function () {
		var $range = $(this), $min = $range.find('.min select'), $max = $range.find('.max select');
		if ($min.length > 0 && $max.length > 0) {
			$min.on('change', function () {
				var min = parseInt($min.val()), max = parseInt($max.val());
				if (min > max) $max.val('');
				return true;
			});
			$max.on('change', function () {
				var min = parseInt($min.val()), max = parseInt($max.val());
				if (min > max) $min.val('');
				return true;
			});
		}
	});

	// Autocomplete Inputs
	$form.find('input.autocomplete').each(function () {
		var $input = $(this), multiple = $input.hasClass('single') ? false : true;
		$input.Autocomplete({
			multiple : multiple,
			params : function () {
				return {
					search_city : $.map($form.find('input[name="search_city[]"]:checked'), function (input) {
						return $(input).val();
					}),
					feed : $form.find('input[name="idx"]').val()
				};
			}
		});
	});

	// Property Type Change
	$form.find('input[name="search_type"]').on('change', function () {
		var $input = $(this), value = $input.val(), checked = $input.attr('checked') ? true : false;

		// Price Ranges
		var $prices = $form.find('#field-price'),
			$sale = $prices.find('.sale'),
			$rent = $prices.find('.rent');

	    // Rental Prices
	    if (value in { 'Rental' : true, 'Rentals' : true, 'Lease' : true, 'Residential Lease' : true, 'Commercial Lease' : true, 'Residential Rental' : true }) {
			$rent.removeClass('hidden').find('select').removeAttr('disabled');
			$sale.addClass('hidden').find('select').attr('disabled', 'disabled');

	    // Sale Prices
	    } else {
	        $sale.removeClass('hidden').find('select').removeAttr('disabled');
	        $rent.addClass('hidden').find('select').attr('disabled', 'disabled');

	    }

		// Return true
		return true;
	}).filter(':checked').trigger('change');

})();
/* </script> */
<?php $page->writeJS(ob_get_clean()); ?>