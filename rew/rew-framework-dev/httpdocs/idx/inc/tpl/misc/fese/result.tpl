<?php

// Result details
$details = [];
if (Format::number($result['NumberOfBedrooms'])) $details[] = Format::number($result['NumberOfBedrooms']) . " Bed";
if (Format::number($result['NumberOfBathrooms'])) $details[] = Format::number($result['NumberOfBathrooms']) . " Bath";
if (Format::number($result['NumberOfSqFt'])) $details[] = Format::number($result['NumberOfSqFt']) . " Sf";
if (Format::number($result['NumberOfAcres'])) $details[] = Format::number($result['NumberOfAcres']) . " Acres";

// Result flags
$flags = [];

// Price Reduction flag
$reduced = !empty($result['ListingPriceOld']) && ($result['ListingPrice'] < $result['ListingPriceOld']);
$reduced = $reduced ? abs(round((($result['ListingPrice'] - $result['ListingPriceOld']) / $result['ListingPriceOld']) * 100)) : NULL;
if ($reduced > 0 && $_COMPLIANCE['flags']['hide_price_reduction'] != true) {
    $flags[] = sprintf('<span class="bdg reduced">Reduced %d%%</span>', $reduced);
}

// New listing flag
if (!is_null($result['ListingDOM']) && $result['ListingDOM'] <= 7) {
    $flags[] = '<span class="bdg new">New Listing</span>';
}

// Listing address
$address = implode(', ', array_filter([
    Format::trim($result['Address']),
    Format::trim($result['AddressCity'])
]));

// Include auction banner for MLS compliance
if (is_callable($_COMPLIANCE['is_up_for_auction']) && $_COMPLIANCE['is_up_for_auction']($result)) {
    $flags[] = '<span class="bdg auction">Auction</span>';
}

// More flags can be added to listing results...
//$flags[] = '<span class="bdg open-house">Open House</span>';
//$flags[] = '<span class="bdg sold">Sold</span>';

$saved = !empty($bookmarked[$result['ListingMLS']]) ? 'saved' : '';

?>

<?php // If content cannot appear over listing images b/c compliance requirements, load this layout ?>
<?php if (isset($_COMPLIANCE['images']['no_overlay']) && $_COMPLIANCE['images']['no_overlay']) { ?>
<div id="listing-<?=$result['ListingMLS']; ?>" class="listing listing-no-overlay col w1/3 w1/2-md w1/1-sm listing">
    <div class="listing-photo">
        <div class="img wFill h3/4 fade img--cover">
            <a href="<?=$result['url_details']; ?>" target="_parent">
                <img data-src="<?=IDX_Feed::thumbUrl($result['ListingImage'], IDX_Feed::IMAGE_SIZE_LARGE); ?>" src="/img/util/35mm_landscape.gif" alt="Photo of Listing #<?=($result['idx'] == 'cms' ? $result['ListingMLSNumber'] : $result['ListingMLS']); ?>">
            </a>
        </div>
    </div>
    <div class="listing-info">
         <div class="BM pad">
            <?php if(!empty($_COMPLIANCE['results']['provider_first']) && $_COMPLIANCE['results']['provider_first']($result)) {
                echo '<div class="provider">';
                \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result);
                echo '</div>';
            } ?>
            <p class="listing-info--keyvalset"><?=join(' &bull; ', $details); ?></p>
            <header>
				<h2 class="listing-info--price"><?=!empty($result['ListingPrice']) ? '$' . Format::number($result['ListingPrice']) : '&nbsp;'; ?></h2>
				<?php if ($result['idx'] !== 'cms') { ?>
		            <a class="grid-stacked--btn__save btn vanilla save action-save <?=$saved; ?>"  data-save='<?=json_encode([
		                'div'		=> '#listing-' . $result['ListingMLS'],
		                'mls'		=> $result['ListingMLS'],
		                'feed'		=> $result['idx'],
		                'remove'	=> 'Remove',
		                'add'		=> 'Save'
		            ]); ?>'></a>
		        <?php } ?>
				<?=!empty($flags) ? sprintf('<div class="listing-flag ">%s</div>', implode(PHP_EOL, $flags)) : ''; ?>
            </header>
			<p class="listing-info--address"><?=$address; ?></p>
            <p class="listing-info--remarks"><?=Format::htmlspecialchars(Format::truncate(ucwords(strtolower($result['ListingRemarks'])), 150)); ?></p>
            <a class="action-detail" href="<?=$result['url_details']; ?>" target="_parent">View Listing</a>
			<p class="mls-compliance">
            <?php if(empty($_COMPLIANCE['results']['provider_first']) || $_COMPLIANCE['results']['provider_first']($result) === false) { ?>
		        <?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result); ?>
		        <?php if (!empty($_COMPLIANCE['results']['show_mls']) && !empty($result['ListingMLS'])) { ?>
					<span class="mls-num">- <?=Lang::write('MLS_NUMBER') . $result['ListingMLS']; ?></span>
				<?php } ?>
            <?php } ?>
	        </p>
        </div>
    </div>
</div>

<? } else {  // Load original layout ?>

<div id="listing-<?=$result['ListingMLS']; ?>" class="listing listing-original col stk w1/3 w1/2-md w1/1-sm listing">
    <div class="listing-photo">
		<a class="action-detail" href="<?=$result['url_details']; ?>" target="_parent">View Listing</a>
        <div class="img wFill h3/4 fade img--cover">
            <a href="<?=$result['url_details']; ?>" target="_parent">
                <img data-src="<?=IDX_Feed::thumbUrl($result['ListingImage'], IDX_Feed::IMAGE_SIZE_LARGE); ?>" src="/img/util/35mm_landscape.gif" alt="Photo of Listing #<?=($result['idx'] == 'cms' ? $result['ListingMLSNumber'] : $result['ListingMLS']); ?>">
            </a>
        </div>
        <?php if ($result['idx'] !== 'cms') { ?>
            <a class="grid-stacked--btn__save btn vanilla save action-save <?=$saved; ?>"  data-save='<?=json_encode([
                'div'		=> '#listing-' . $result['ListingMLS'],
                'mls'		=> $result['ListingMLS'],
                'feed'		=> $result['idx'],
                'remove'	=> 'Remove',
                'add'		=> 'Save'
            ]); ?>'
            data-event='favorite' <?php $tracking = (IDX_Compliance::FormatListingTrackable($result)); foreach ($tracking as $service => $listing) { echo "data-listing-" . $service . "='" . Format::htmlspecialchars(json_encode($listing)) . "'"; } ?>></a>
        <?php } ?>
        <?=!empty($flags) ? sprintf('<div class="listing-flag">%s</div>', implode(PHP_EOL, $flags)) : ''; ?>
    </div>
    <div class="listing-info">
        <div class="TM pad"></div>
        <div class="BM pad">
            <?php if(!empty($_COMPLIANCE['results']['provider_first']) && $_COMPLIANCE['results']['provider_first']($result)) {
                echo '<div class="provider">';
                \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result);
                echo '</div>';
            } ?>
            <h4><?=join(' &bull; ', $details); ?></h4>
            <h2><?=!empty($result['ListingPrice']) ? '$' . Format::number($result['ListingPrice']) : '&nbsp;'; ?></h2>
            <p class="description"><?=$address; ?></p>
            <p class="listing-desc"><?=Format::htmlspecialchars(Format::truncate(ucwords(strtolower($result['ListingRemarks'])), 150)); ?></p>
            <a class="action-detail" href="<?=$result['url_details']; ?>" target="_parent">View Listing</a>
            <div class="mls-compliance">
            <?php if(empty($_COMPLIANCE['results']['provider_first']) || $_COMPLIANCE['results']['provider_first']($result) === false) { ?>
                <?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result); ?>
                <?php if (!empty($_COMPLIANCE['results']['show_mls']) && !empty($result['ListingMLS'])) { ?>
                    <span class="mls-num"><?=Lang::write('MLS_NUMBER'). ' ' . $result['ListingMLS']; ?></span>
                <?php } ?>
            <?php } ?>
            </div>
        </div>
    </div>
</div>
<? } ?>