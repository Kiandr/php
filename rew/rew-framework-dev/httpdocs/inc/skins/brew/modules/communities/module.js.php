// Module <script>
(function () {

	// Module Thumbnails
	var $module = $('#<?=$this->getUID(); ?>').Images({
		onLoad : false
	});

    var rolloverThumbs = function () {
        var img = this.hash.replace('#', '.');
        $module.find(img).removeClass('hidden').siblings('img').addClass('hidden');
    }

	// Thumbnails
	$module.on({
		mouseenter : rolloverThumbs,
		touchstart : rolloverThumbs,
		click : function () {
			return false;
		}
	}, '.community-thumbnails a');

})();