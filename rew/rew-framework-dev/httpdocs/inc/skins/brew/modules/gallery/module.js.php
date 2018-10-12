/* <script> */
(function () {

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
			content: '<div class="gallery">' + originalHtml + '</div>',
			closeOnClick: false,
			onOpen: function (win) {
				var $gallery = win.getWindow().addClass('gallery').find('.gallery').Gallery({
					autoplay: false,
					current : slide
				})

				// Display compliance
				var $compliance = $gallery.find('.compliance');
				if ($compliance.length) {
					$compliance.removeClass('hidden');
					win.position();
				}
			}
		});
	});

	// Gallery
	var $gallery = $wrap.find('.gallery'), originalHtml = $gallery.html();
	$gallery.Gallery ({
		onChange : function() {
			if ($carousel.length > 0) {
				$thumbs = $carousel.find('.slide');
				$active = $thumbs.eq(this.current);
				$thumbs.removeClass('active').filter($active).addClass('active');
				if ($active.is(':first-child')) {
					$carousel.Carousel('setRow', $active.parent().index());
				}
			}
		}
	});

	// Carousel
	var $carousel = $wrap.find('.carousel');
	if ($carousel.length > 0) {
		$carousel.Carousel({
			columns	: 4,
			onClick	: function (el) {
				var $slide = $(el), slide = $slide.data('slide');
				$slide.addClass('active').siblings().removeClass('active');
				$gallery.Gallery('setSlide', slide, true);
			}
		});
	}

})();
/* </script> */