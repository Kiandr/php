<?php

if (!$_REQUEST['snippet'] && !in_array($_GET['load_page'], array('search', 'search_map'))) return;

// Map panels
$polygon = IDX_Panel::get('Polygon');
$radius = IDX_Panel::get('Radius');
$bounds = IDX_Panel::get('Bounds');

?>
<div class="fw fw-idx-map uk-margin-bottom <?= $hideMap ? ' uk-hidden' : ''; ?>"></div>
<form id="map-draw-controls" class="uk-hidden uk-margin-top">
    <div class="uk-button" id="field-polygon"><?= $polygon->getMarkup(); ?></div>
    <div class="uk-button" id="field-radius"><?= $radius->getMarkup(); ?></div>
    <div class="uk-button" id="field-bounds"><?= $bounds->getMarkup(); ?></div>
    <div class="uk-button" id="field-map-submit"><i class="uk-icon uk-icon-justify uk-icon-search"></i></div>
</form>
