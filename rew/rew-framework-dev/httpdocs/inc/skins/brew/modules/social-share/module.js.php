(function () {
	// Test for SVG Support
	var className = (!! document.createElementNS && !!document.createElementNS('http://www.w3.org/2000/svg', 'svg').createSVGRect) ? 'svg' : 'no-svg';
	$('html').addClass(className);
})();