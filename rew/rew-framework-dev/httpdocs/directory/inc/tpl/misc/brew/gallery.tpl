<?php if (!empty($entry['thumbnails'])) { ?>

	<div id="gallery">
		<div class="gallery">
			<div class="slideset">
		        <?php foreach ($entry['thumbnails'] as $count => $photo) { ?>
		        	<div class="slide" data-slide="<?=$count; ?>"><img src="/img/util/35mm_landscape.gif" data-src="<?=$photo; ?>" alt=""></div>
		        <?php } ?>
		        <img src="/img/util/dig_landscape.gif" class="ph">
			</div>
		    <a class="prev" href="javascript:void(0);"><i class="icon-chevron-left"></i></a>
		    <a class="next" href="javascript:void(0);"><i class="icon-chevron-right"></i></a>
		</div>
		<div class="carousel hidden">
			<div class="slideset">
		        <?php foreach ($entry['thumbnails'] as $count => $photo) { ?>
		        	<div class="slide" data-slide="<?=$count; ?>"><a><img src="/thumbs/108x70/img/util/35mm_landscape.gif" data-src="<?=$photo; ?>" alt=""></a></div>
		        <?php } ?>
			</div>
		    <a class="prev" href="javascript:void(0);"><i class="icon-chevron-left"></i></a>
		    <a class="next" href="javascript:void(0);"><i class="icon-chevron-right"></i></a>
		</div>
	</div>

	<br>

	<?php ob_start(); ?>
	/* <script> */

		// Container
		var $wrap = $('#gallery');

		// Gallery
		var $gallery = $wrap.find('.gallery').Gallery();

		// Carousel
		var $carousel = $wrap.find('.carousel').Carousel({
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

<?php } ?>