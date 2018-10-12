<div id="gallery">
    <div class="gallery">
        <div class="slideset">
            <?php foreach ($listing['thumbnails'] as $count => $photo) { ?>
                <div class="slide" data-slide="<?=$count; ?>"><img src="/img/util/35mm_landscape.gif" data-src="<?=$photo; ?>" alt=""></div>
            <?php } ?>
            <img src="/img/util/dig_landscape.gif" class="ph">
        </div>
        <?php if (!empty($_COMPLIANCE['details']['show_below_photos'])) {?>
        <div style="display:none;">
            <?php if (!empty($_COMPLIANCE['results']['show_mls'])) { ?>
            <p class="val mls"><?=Lang::write('MLS_NUMBER'); ?><?=($result['idx'] == 'cms' ? $listing['ListingMLSNumber'] : $listing['ListingMLS']); ?></p>
            <?php } ?>
            <?=\Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showProvider($listing);?>
        </div>
        <?php } ?>
        <a class="prev" href="javascript:void(0);"><i class="icon-chevron-left"></i></a>
        <a class="next" href="javascript:void(0);"><i class="icon-chevron-right"></i></a>
    </div>
    <div class="carousel hidden">
        <div class="slideset">
            <?php foreach ($listing['thumbnails'] as $count => $photo) { ?>
                <div class="slide" data-slide="<?=$count; ?>"><a><img src="/thumbs/108x70/img/util/35mm_landscape.gif" data-src="<?=IDX_Feed::thumbUrl($photo, IDX_Feed::IMAGE_SIZE_SMALL); ?>" alt=""></a></div>
            <?php } ?>
        </div>
        <a class="prev" href="javascript:void(0);"><i class="icon-chevron-left"></i></a>
        <a class="next" href="javascript:void(0);"><i class="icon-chevron-right"></i></a>
    </div>

    <div class="btnset mini hidden-phone">
        <a class="btn all" href="javascript:void(0);">All Photos</a>
    </div>

</div>
