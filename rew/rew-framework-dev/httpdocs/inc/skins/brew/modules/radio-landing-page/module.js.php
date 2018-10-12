/* <script> */
'use strict';

(function() {

	// Add Landing Page Body Class
	$('body').addClass('landing-<?=$this->config['type']; ?>');

	// Load Up audio.js
	$(document).ready(function () {
		audiojs.events.ready(function() {
			var as = audiojs.createAll();
		});
	});

	// Clickable Tabs - Tabbed Content Pod
	if ($('.pod.sellerProgram').length > 0) {
		$('.pod.sellerProgram .tabset ul li a').on('click', function () {
			var $this = $(this),
				$tab = $this.attr('href');

			// Only Show Selected Tab's Content
			$('.pod.sellerProgram .tab-contents').addClass('hidden');
			$($tab).removeClass('hidden');

			// Add "current" Class to Selected Tab
			$('.pod.sellerProgram .tabset ul li').removeClass('current');
			$this.parent('li').addClass('current');

			return false;
		});
	}

})();