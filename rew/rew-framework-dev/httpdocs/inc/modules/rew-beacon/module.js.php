// <script>
(function () {
	'use strict';
	var hidden = 'hidden'
		, interval = (30 * 1000)
		, pingIt = function () {
			$.ajax({
				url : '/idx/inc/php/ajax/html.php?module=rew-beacon',
				type : 'get',
				data : {
					options : {
						ajax : true
					}
				}
			});
		}
		, ping = setInterval(pingIt, interval)
	;
	// Page visibility API
	var onchange = function () {
		if (this[hidden]) {
			clearInterval(ping);
		} else {
			ping = setInterval(pingIt, interval);
		}
	};
	if (hidden in document) {
		document.addEventListener('visibilitychange', onchange);
	} else if ((hidden = 'mozHidden') in document) {
		document.addEventListener('mozvisibilitychange', onchange);
	} else if ((hidden = 'webkitHidden') in document) {
		document.addEventListener('webkitvisibilitychange', onchange);
	} else if ((hidden = 'msHidden') in document) {
		document.addEventListener('msvisibilitychange', onchange);
	}
})();