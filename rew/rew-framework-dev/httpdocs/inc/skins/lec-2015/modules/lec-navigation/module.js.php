/* <script> */
(function () {

	// Search location autocomplete
	var $module = $('#<?=$this->getUID(); ?>');
	$module.find('input[name="search_location"]').Autocomplete({
		multiple: true
	});

})();