<div<?= Skin_ELITE::buildSearchAttributesForView($view, array('grid' => 'uk-width-small-1-1 uk-width-medium-1-2 uk-width-large-1-4 idx-listings idx-listings-grid', 'detailed' => 'uk-width-1-1 idx-listings idx-listings-list')); ?>>

    <div class="idx-listings-wrapper uk-margin-bottom<?= ($result['flag'] ? ($result['flag'] == 'Reduced' && !empty($_COMPLIANCE['flags']['hide_price_reduction']) ? '' : ' selected' ) : ''); ?>">
        <div<?= Skin_ELITE::buildSearchAttributesForView($view, array('detailed' => 'uk-grid uk-grid-collapse', 'grid' => '')); ?>>
            <div<?= Skin_ELITE::buildSearchAttributesForView($view, array('detailed' => 'uk-width-medium-2-5 uk-row-first', 'grid' => '')); ?>>
                <div class="uk-panel">
                    <?php if(!empty($_COMPLIANCE['results']['provider_first']) && $_COMPLIANCE['results']['provider_first']($result)) { ?>
                        <?= \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result); ?>
                    <?php } ?>
                    <div class="uk-panel-teaser<?= !empty($result['watermarked']) ? ' fw-img-watermarked' : ''; ?>">
                        <div class="uk-panel-teaser uk-margin-bottom-remove idx-listing-image" style="background-image: url(<?= IDX_Feed::thumbUrl($result['ListingImage'], IDX_Feed::IMAGE_SIZE_MEDIUM); ?>)"></div>
                        <div class="idx-listings-caption uk-text-contrast">
                            <?=!empty($result['ListingPrice']) ? '<span>$' . Format::number($result['ListingPrice']) . '</span>' : ''; ?>
                            <div class="uk-float-right">
                            <?php if ($result['idx'] != 'cms') { ?>
                                <a title="Hide" class="action-dismiss" data-dismiss='<?= Format::htmlspecialchars(json_encode(array(
                                    'mls'		=> $result['ListingMLS'],
                                    'feed'		=> $result['idx']
                                ))); ?>'><i class="<?=(is_array($dismissed) && isset($dismissed[$result['ListingMLS']]) ? 'uk-icon-eye-slash' : 'uk-icon-eye' )?>" aria-hidden="true"></i>
                                </a>
                                <a title="Favorite" class="action-save" data-save='<?= Format::htmlspecialchars(json_encode(array(
                                    'mls'		=> $result['ListingMLS'],
                                    'feed'		=> $result['idx'],
                                    'remove'	=> 'Remove',
                                    'add'		=> 'Save'
                                ))); ?>'>
                                <?=(!empty($bookmarked[$result['ListingMLS']]) ? '<i class="uk-icon-heart" aria-hidden="true"></i>' : '<i class="uk-icon-heart-o" aria-hidden="true"></i>'); ?>
                                </a>
                            <?php } ?>
                            </div>
                        </div>
                        <a<?= isset($_REQUEST['popup']) ? ' target="_parent" ' : ''; ?> href="<?=$result['url_details']; ?>" class="idx-background-image-link" title="View more information about <?=Format::htmlspecialchars($result['Address']); ?>.">&nbsp;</a>
                    </div>
                </div>
            </div>
            <div<?= Skin_ELITE::buildSearchAttributesForView($view, array('detailed' => 'uk-width-medium-3-5 idx-details-wrapper', 'grid' => '')); ?>>

                <div class="idx-default">

                    <h4><?=Format::htmlspecialchars(implode(', ', array_filter(array($result['Address'], $result['AddressCity'])))); ?></h4>

                    <div class="idx-listings-stats">
                    <?php if (!empty($result['NumberOfBedrooms'])) { ?>
                        <?=Format::number($result['NumberOfBedrooms']); ?> <?=Format::plural($result['NumberOfBedrooms'], 'Beds', 'Bed'); ?>,
                    <?php } ?>
                    <?php if (floatval($result['NumberOfBathrooms']) > 0) { ?>
                        <?=Format::fraction($result['NumberOfBathrooms']); ?> <?=Format::plural($result['NumberOfBathrooms'], 'Baths', 'Bath'); ?>,
                    <?php } ?>
                    <?php if (!empty($result['NumberOfSqFt'])) { ?>
                        <?=(intval($result['NumberOfSqFt']) > 9999 ?  Format::shortNumber($result['NumberOfSqFt']) : Format::number($result['NumberOfSqFt'])); ?> SqFt
                    <?php } ?>
                    </div>

                    <div class="idx-listings-area"><a href="<?=$result['url_details']; ?>"<?= isset($_REQUEST['popup']) ? ' target="_parent"' : ''; ?> title="View more information about <?=Format::htmlspecialchars($result['Address']); ?>."><span>View Listing</span> <i class="ion-ios-arrow-right"></i></a></div>

                    <div<?= Skin_ELITE::buildSearchAttributesForView($view, array('detailed' => 'idx-listings-description uk-margin-bottom', 'grid' => 'uk-hidden')); ?>>
                        <?= Format::truncate(Format::htmlspecialchars($result['ListingRemarks']), 450, '', false); ?>
                    </div>

                    <span class="mediaProvider">
                    <?php if (isset($result['ListingFeed'])) \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->load($result['ListingFeed']); ?>
                    <?php if(empty($_COMPLIANCE['results']['provider_first']) || $_COMPLIANCE['results']['provider_first']($result) == false) { ?>
                         <?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProviderResult($result); ?>
                    <?php } ?>
                    <?=(!empty($_COMPLIANCE['results']['show_mls']) ? '<p>' . Lang::write('MLS_NUMBER') . ($result['idx'] == 'cms' ? $result['ListingMLSNumber'] : $result['ListingMLS']) . '</p>' : ''); ?>
                    </span>
                </div>
                <?php if ($result['flag']) { ?>
                    <div class="idx-listings-status"><?= Format::htmlspecialchars($result['flag']); ?></div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
