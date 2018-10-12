/* <script> */
(function () {

	// Feature Image
	var bgImage = '<?=$this->config('image'); ?>';
	if (bgImage.length < 1) {
		bgImage = $('<div></div>').appendTo('body').addClass('featureImage').css('background-image');
		bgImage = (bgImage === 'none') ? '' : bgImage.replace(/^url\(["']?/, '').replace(/["']?\)$/, '');
	}

	// Feature Image
	$bgImage = $('#head, #feature').wrapAll($('<div class="bg" />'));

	// IE Fallback
	if ($('html').is('.ie.lte8')) $bgImage.after('<img src="' + bgImage + '" class="bgimg">');

	// Append Feature Styles
	$('body').append("<style>\
		@media only screen and (min-width: 480px) {\
			.bg {\
				background-image : url('" + bgImage + "');\
				background-position : <?=$this->config('position'); ?>;\
				background-size: cover;\
			}\
		}\
	</style>");

	// Live Count for Quick Search
	var $feature = $('#feature'), bindEvents = function () {
		$('#feature').live('change', 'form', function (e) {
			var $this = $(this), $form = $this.find('form'), $text = $('#results-count').text('Searching Properties...');
			 $.ajax({
			     'url'      : '/idx/inc/php/ajax/json.php?searchCount',
			     'data'     : $form.serialize(),
			     'type'     : 'POST',
			     'dataType' : 'json',
			     'cache'    : false,
			     'success'  : function (data, textStatus, XMLHttpRequest) {
					if (typeof data.count != 'undefined' && data.count > 0) {
						var properties_count = (data.count).format();
						$text.html(properties_count + ' Properties Match Your Criteria');
					} else {
						$text.html('No Properties Match Your Criteria');
					}
				}
			 });
		}).trigger('change');
	}; bindEvents();

	// IDX Feed Switcher (Re-Load Quick Search)
	var $tabset = $feature.find('.tabset'), $form = $feature.find('.idx-search');
	$tabset.on('click', 'a', function () {
		var $this = $(this), feed = $this.data('feed');
		$this.parent().addClass('current').siblings().removeClass('current');

		// Re-Load Quick Search
		$.ajax({
			url : '/idx/inc/php/ajax/html.php?module=idx-search',
			type : 'get',
			data : {
				'options' : <?=json_encode($this->config('idx-search')); ?>,
				//'search_location' : $form.find('input[name="search_location"]').val(),
				//'minimum_price' : $form.find('select[name="minimum_price"]').val(),
				//'maximum_price' : $form.find('select[name="maximum_price"]').val(),
				'feed' : feed
			},
			success : function (html) {
				if (html && html.length > 1) {

					// Update Form
					$form.replaceWith(html);
					$form = $feature.find('.idx-search');

					// Run Events
					bindEvents();

					// Run Parent JS
					BREW.Modules.IDX_SEARCH.init();

				}
			}
		});

		return false;
	});

})();
/* </script> */