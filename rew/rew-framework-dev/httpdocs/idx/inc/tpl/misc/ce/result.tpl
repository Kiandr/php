<?php

// Result details
$details = [];
if (Format::number($result['NumberOfBedrooms'])) $details[] = Format::number($result['NumberOfBedrooms']) . ' Beds';
if (Format::number($result['NumberOfBathrooms'])) $details[] = Format::number($result['NumberOfBathrooms']) . ' Baths';
if (Format::number($result['NumberOfSqFt'])) $details[] = Format::number($result['NumberOfSqFt']) . ' Sf';
if(in_array(strtolower($result['ListingType']), array('land', 'lot'))) {
    if (Format::number($result['NumberOfAcres'])) $details[] = Format::number($result['NumberOfAcres']) . ' Acres';
}

// Listing address
$address = implode(', ', array_filter([
    Format::trim($result['Address']),
    Format::trim($result['AddressCity'])
]));

// Result flags
$flags = [];

// Enhanced listing flag
if (!empty($result['enhanced'])) {
    $flags[] = ['type' => 'enhanced', 'text' => 'Enhanced'];
}

// New listing flag
if (!is_null($result['ListingDOM']) && $result['ListingDOM'] <= 7) {
    $flags[] = ['type' => 'new', 'text' => 'New Listing'];
}

// Price Reduction flag
$reduced = !empty($result['ListingPriceOld']) && ($result['ListingPrice'] < $result['ListingPriceOld']);
$reduced = $reduced ? abs(round((($result['ListingPrice'] - $result['ListingPriceOld']) / $result['ListingPriceOld']) * 100)) : NULL;
if ($reduced > 0 && !empty($_COMPLIANCE['flags']['hide_price_reduction'])) {
    $flags[] = ['type' => 'reduced', 'text' => sprintf('Reduced %d%%', $reduced)];
}

?>
<div class="article column -width-1/3 -width-1/2@md -width-1/1@sm -width-1/1@xs" id="listing-<?=$result['ListingMLS']; ?>">
    <a class="article__photo hero hero--landscape" href="<?=preg_replace('/\/listing\//', '/listing' . (($result['idx'] !== 'cms') ? '-'. $result['idx'] : '') . '/', $result['url_details']); ?>" target="_parent">
        <img data-src="<?=IDX_Feed::thumbUrl($result['ListingImage'], IDX_Feed::IMAGE_SIZE_MEDIUM); ?>" data-srcset="<?=IDX_Feed::thumbUrl($result['ListingImage'], IDX_Feed::IMAGE_SIZE_LARGE); ?> 2x" src="/img/util/35mm_landscape.gif" alt="Photo of Listing #<?=($result['idx'] == 'cms' ? $result['ListingMLSNumber'] : $result['ListingMLS']); ?>">
    </a>
    <div class="article__body">
        <?php if (!empty($flags)) { ?>
            <div class="article__flags -text-left">
                <?php foreach ($flags as $flag) { ?>
                    <div class="article__flag flag--<?=$flag['type']; ?>"><?=$flag['text']; ?></div>
                <?php } ?>
            </div>
        <?php } ?>
        <?php if(!empty($_COMPLIANCE['results']['provider_first']) && $_COMPLIANCE['results']['provider_first']($result)) {
            echo '<div class="mls-compliance -text-xs">';
            \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result);
            echo '</div>';
        } ?>
        <h3 class="-mar-bottom-sm">
            <a href="<?=$result['url_details']; ?>" target="_parent"><?=$address; ?></a>
            <?php if ($result['idx'] !== 'cms') { ?>
                <a href="" title="Save Listing" class="save action-save<?=!empty($bookmarked[$result['ListingMLS']]) ? ' saved' : ''; ?>" data-save='<?=json_encode([
                    'div'  => '#listing-' . $result['ListingMLS'],
                    'mls'  => $result['ListingMLS'],
                    'feed' => $result['idx']
                ]); ?>'><svg class="icon"><use xlink:href="/inc/skins/ce/img/assets.svg#icon--star" /></svg></a>
            <?php } ?>
        </h3>
        <div>
            <?=!empty($result['ListingPrice']) ? '$' . Format::number($result['ListingPrice']) : ''; ?>
            <?=($details ? ' - ' . join(', ', $details) : ''); ?>
        </div>
        <p class="-text-xs -mar-bottom-sm">
            <?php if ($result['idx'] !== 'cms' || ($result['idx'] === 'cms' && !empty($result['ListingMLSNumber']))) { ?>
                <?=Lang::write('MLS_NUMBER'). ' ' . ($result['idx'] === 'cms' ? $result['ListingMLSNumber'] : $result['ListingMLS']); ?>
            <?php } ?>
        </p>
    </div>
    <div class="article__foot -text-xs">
        <?php if(empty($_COMPLIANCE['results']['provider_first'])) { ?>
        <div class="mls-compliance">
            <?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result); ?>
        </div>
        <?php } ?>
    </div>
</div>