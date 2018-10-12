<?php


$class = $this->config('class');
$class = isset($class) ? $class : 'idx-details-gallery-container';

$viewall_class = $this->config('viewall_class');
$viewall_class = isset($viewall_class) ? $viewall_class : 'idx-details-gallery-viewall';

$slider_config = array(
    "centerMode" => true,
    "centerPadding" => "60px",
    "slidesToShow" => (count($images) > 3 ? 3 : count($images) == 2 ? 1 : 2),
    "adaptiveHeight" => true,
    "variableWidth" => true,
    "arrows" => true,
    "prevArrow" => "<img class=idx-gallery-arrow-left src='" . Format::htmlspecialchars($this->getPage()->getSkin()->getUrl()) . "/img/idx-gallery-arrow-left.png'>",
    "nextArrow" => "<img class=idx-gallery-arrow-right src='" . Format::htmlspecialchars($this->getPage()->getSkin()->getUrl()) . "/img/idx-gallery-arrow-right.png'>"
);
$config = $this->config('slider_config');
if (is_array($config)) {
    $slider_config = array_merge($slider_config, $config);
}

?>
<div class="uk-position-relative <?=$class ?>">
    <div class="uk-position-bottom-right uk-position-z-index">
            <a class="idx-details-gallery-button js-view-all-photos" data-display-photos<?= Settings::getInstance()->SETTINGS['registration_on_more_pics'] ? ' data-register="true"' : ''; ?>>See All<?= Settings::getInstance()->SETTINGS['registration_on_more_pics'] ? '' : ' ' . ((int) count($images)); ?> Photos</a>
    </div>
    <div id="idx-details-gallery" class="fw-slider" data-fw-slider-config="<?= Format::htmlspecialchars(json_encode($slider_config)); ?>">
    <?php foreach ($images as $photo) { ?>
            <div style="background-image:url('<?= Format::htmlspecialchars($photo); ?>'); " class="listing-slides"></div>
    <?php } ?>
    </div>
</div>

<div class="js-idx-details-gallery-overlay uk-position-z-index uk-hidden <?=$viewall_class ?>">
    <div class="idx-details-gallery-overlay"></div>
    <div class="uk-container uk-container-center uk-margin-large-top uk-margin-large-bottom idx-details-gallery-overlay-inner">
        <div class="uk-container uk-container-center uk-width-xsmall-4-4 uk-width-medium-3-4">
            <h3 class="uk-float-left idx-gallery-overlay-title"><?=htmlspecialchars($title) ?></h3>
            <a class="uk-float-right idx-details-gallery-overlay-close js-idx-details-gallery-overlay-close" data-uk-toggle="{target: '.js-idx-details-gallery-overlay'}"><i class="uk-icon uk-icon-remove"></i></a>
        </div>
        <div class="uk-container uk-container-center uk-width-xsmall-4-4 uk-width-medium-3-4">
            <div class="uk-slidenav-position" data-uk-slideshow>
                <div data-uk-slideset="{default: 5}">
                    <div class="uk-slidenav-position uk-hidden-small">
                        <ul class="uk-slideset uk-grid uk-grid-collapse uk-flex-center uk-grid-width-1-5">
                            <?php foreach ($images as $i => $photo) { ?>
                                <li data-uk-slideshow-item="<?= $i; ?>">
                                	<img class="uk-thumbnail idx-details-gallery-overlay-thumb" src="<?= Format::htmlspecialchars($photo); ?>">
                                </li>
                            <?php } ?>
                        </ul>
                        <a href="" class="uk-slidenav uk-slidenav-previous" data-uk-slideset-item="previous"></a>
                        <a href="" class="uk-slidenav uk-slidenav-next" data-uk-slideset-item="next"></a>
                    </div>
                </div>
                <ul class="uk-slideshow">
                    <?php foreach ($images as $photo) { ?>
                        <li><img class="uk-invisible idx-details-gallery-overlay-large" src="<?= Format::htmlspecialchars($photo); ?>"></li>
                    <?php } ?>
                </ul>
                <ul>
                    <a href="" class="uk-slidenav uk-slidenav-contrast uk-slidenav-previous" data-uk-slideshow-item="previous"></a>
                    <a href="" class="uk-slidenav uk-slidenav-contrast uk-slidenav-next" data-uk-slideshow-item="next"></a>
                </ul>
            </div>
        </div>
    </div>
</div>
