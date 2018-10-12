<div id="gallery">

	<?php
	// Compliance Auction Banner Display
	if (is_callable($_COMPLIANCE['is_up_for_auction']) && $_COMPLIANCE['is_up_for_auction']($listing)) {
		echo '<span class="flag flag-auction">AUCTION</span>';
	}
	?>

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

<?php ob_start(); ?>
/* <script> */

	// Container
	var $wrap = $('#gallery');

	// View All Photos
	$wrap.on('click', 'a.all', function () {
		// Slide Details
		var $slide = $gallery.find('.slide.current'), slide = $slide.data('slide');
		// Pause Gallery
		$gallery.Gallery('stop');
		// Gallery Popup
		$.Window({
			width: 800,
			title: document.title,
			content: '<div class="gallery">' + originalHtml + ($(originalHtml).find('.mls-provider').parent().show().html() ? $(originalHtml).find('.mls-provider').parent().show().html() : '') + '</div>',
			closeOnClick: false,
			onOpen: function (win) {
				win.getWindow().addClass('gallery').find('.gallery').Gallery({
					autoplay: false,
					current : slide
				});
			}
		});
	});

	// Gallery
	var $gallery = $wrap.find('.gallery'), originalHtml = $gallery.html();
	$gallery.Gallery();

	// Carousel
	var $carousel = $wrap.find('.carousel').Carousel({
	    columns	: 3,
		onHover : function (el) {
			var $slide = $(el), slide = $slide.data('slide');
			$slide.addClass('active').siblings().removeClass('active');
			$gallery.Gallery('setSlide', slide, true);
		},
		onClick : function (el) {
			var $slide = $(el), slide = $slide.data('slide');
			$slide.addClass('active').siblings().removeClass('active');
			$gallery.Gallery('setSlide', slide, true);
		}
	});

/* </script> */
<?php $page->writeJS(ob_get_clean()); ?>
