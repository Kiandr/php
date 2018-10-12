/* <script> */
(function () {
	'use strict';

	// Open video play
	$('#<?=$this->getUID(); ?>').on('click', 'a[data-video-id]', function () {
		var $this = $(this)
			, id = $this.data('video-id') || ''
			, title = $this.data('video-title') || ''
			, type = $this.data('video-type') || ''
			, src = (type == 'vimeo' ? 'https://player.vimeo.com/video/' + id + '?title=0&byline=0&portrait=0&autoplay=1' : 'https://www.youtube.com/embed/' + id + '?controls=1&showinfo=0&autoplay=1')
		;
		// Require video ID
		if (id && (id.length > 0 || id > 0)) {
			$.Window({
				title: title,
				closeOnClick: true,
				content: '<iframe src="'  + src + '" width="560" height="315" title=""></iframe>'
			});
		}
	});

})();
/* </script> */