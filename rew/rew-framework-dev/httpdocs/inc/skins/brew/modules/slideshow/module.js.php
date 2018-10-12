<?php

// Slideshow options
$galleryOptions = array(
	'internal' => !empty($this->config['interval']) && is_numeric($this->config['interval']) ? $this->config['interval'] : 5000,
	'paginate' => in_array($this->config['paginate'], array('dots', 'nums')) ? $this->config['paginate'] : NULL
);

?>
/* <script> */
(function () {
	'use strict';

	// Initiate slideshow
	$('#<?=$this->getUID(); ?>').Gallery(<?=json_encode($galleryOptions); ?>);

})();
