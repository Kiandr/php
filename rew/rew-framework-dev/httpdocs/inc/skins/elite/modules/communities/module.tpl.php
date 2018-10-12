<?php

// Module Disabled
if (empty(Settings::getInstance()->MODULES['REW_FEATURED_COMMUNITIES'])) return;

// URL to view all communities
$url_communities = '/communities.php';

// Require featured community
$community = array_shift($communities);
if (empty($community)) return;

$stats = $community['stats'];

$community['galleryBlurb'] = isset($this->config['galleryBlurb']) ? $this->config['galleryBlurb'] : '';

?>

<!-- Slideshow Section -->
<?php if (empty($community['images'])) { ?>
<div class="fw fw-nbh-slideshow">
    <div class="slide"></div>
</div>

<?php } else { ?>

<div class="uk-clearfix fw fw-nbh-slideshow">
    <div class="slide-txt">
        <div class="slide-txt-inner">
            <h1><?=$community['title']; ?></h1>
            <?php if ($community['galleryBlurb']) { ?><span><?=$community['galleryBlurb']; ?></span><?php }?>
            <?php if ($community['video_link']) { ?>
                <a href="<?=$community['video_link']?>" data-uk-lightbox data-lightbox-type="iframe" class="btn slide-btn">Open Video</a>
            <?php }?>
        </div><!-- /.slide-txt-inner -->
    </div>

    <?php
        $this->getPage()->container('gallery')->module('gallery', array(
            'enlarge'		=> false,
            'images'		=> $community['images'],
            'class'			=> 'community-gallery',
            'title'			=> $community['title'],

            'slider_config'	=> array(
                "centerMode"	=> false,
                "centerPadding"	=> "60px",
                "slidesToShow"	=> 1,
                "adaptiveHeight"=> false,
                "variableWidth"	=> false,
                "autoplay"		=> true,
                "prevArrow" => "<img class=community-gallery-arrow-left src='" . Format::htmlspecialchars($this->getPage()->getSkin()->getUrl()) . "/img/idx-gallery-arrow-left.png'>",
                "nextArrow" => "<img class=community-gallery-arrow-right src='" . Format::htmlspecialchars($this->getPage()->getSkin()->getUrl()) . "/img/idx-gallery-arrow-right.png'>"
            ),
        ))->display();
    ?>
    </div>
<?php
} ?>
<div class="fw fw-about-neighborhood" id="<?=$this->getUID() ; ?>">
    <div class="uk-container uk-container-center">
        <h2><?= Format::htmlspecialchars($community['title']); ?></h2>
        <div class="uk-grid uk-grid-large">
            <div class="uk-width-1-1 uk-width-large-1-2">
                <h3><?=$community['subtitle']; ?></h3>
                <p><?=$community['description']; ?></p>

                <?php if (!empty($community['anchor_one_link'])) {?>
                    <p><a class="more-info-link" href="<?=$community['anchor_one_link']; ?>"><?=$community['anchor_one_text']; ?> <i class="uk-icon-angle-right"></i></a></p>
                <?php }?>
                <?php if (!empty($community['anchor_two_link'])) {?>
                    <p><a class="more-info-link" href="<?=$community['anchor_two_link']; ?>"><?=$community['anchor_two_text']; ?> <i class="uk-icon-angle-right"></i></a></p>
                <?php }?>

            </div><!-- /.uk-width-1-1 -->

            <?php if (!empty($stats)) {?>
            <div class="uk-width-1-1 uk-width-large-1-2 nbh-info-box">
                <h3>The price range for this <?=strtolower(Locale::spell('Neighborhood'));?> is from <span>$<?=Format::number($stats['min']); ?></span> to <span>$<?=Format::number($stats['max']); ?></span></h3>
                <div class="uk-grid">
                    <div class="uk-width-xsmall-1-1 uk-width-medium-1-2 uk-margin-bottom">
                        <div class="nbh-avg-info">
                            <span><?=Format::number($stats['total']); ?></span>
                            <span><?=$community['stats_total']; ?></span>
                        </div>
                    </div>
                    <div class="uk-width-xsmall-1-1 uk-width-medium-1-2 uk-margin-bottom">
                        <div class="nbh-avg-info">
                            <span>$<?=Format::number($stats['average']); ?></span>
                            <span><?=$community['stats_average']; ?></span>
                        </div>
                    </div>
                    <?php if (!empty($stats['sqft'])) { ?>
                        <div class="uk-width-xsmall-1-1 uk-width-medium-1-2 uk-margin-bottom">
                            <div class="nbh-avg-info">
                                <span><?=Format::number($stats['sqft']); ?></span>
                                <span>Average Square Feet</span>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if (!empty($stats['avg_price_sqft'])) { ?>
                        <div class="uk-width-xsmall-1-1 uk-width-medium-1-2 uk-margin-bottom">
                            <div class="nbh-avg-info">
                                <span><?=Format::number($stats['avg_price_sqft']); ?></span>
                                <span>Average Price / SqFt</span>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div><!-- /.uk-width-1-1 -->
            <?php } ?>
        </div><!-- /.uk-grid -->
    </div><!-- /.uk-container -->
</div><!-- /.fw /.fw-about-neighborhood -->

<style type="text/css">
    .fw-idx-listings .fw-idx-map {margin-bottom: 20px}
</style>
<div class="fw fw-idx-listings fw-nbh-listings">
    <div class="uk-container uk-container-center">
        <?= $idx_results ?>
    </div><!-- /.uk-container -->
</div><!-- /.fw /.fw-nbh-listings -->


<?php if (count($community['areas']) > 1) { ?>
    <div class="fw fw-nbh-neighbors">
        <div class="uk-container uk-container-center">
            <h4><?= Format::htmlspecialchars($community['title'])?> <?= Locale::spell('Neighborhoods')?></h4>
            <div class="similar-nbh">
                <ul>
                    <?php foreach ($community['areas'] as $name => $count) {?>
                    <li><a href="<?= Format::htmlspecialchars($community['search_url'] . '&search_area=' . rawurlencode($name)); ?>"><?= Format::htmlspecialchars($name) ?> <span><?= Format::number($count); ?></span></a></li>
                    <?php }?>
                </ul>
            </div><!-- /.similar-nbh -->
        </div><!-- /.uk-container -->
    </div><!-- /.fw /.fw-nbh-listings -->
<?php } ?>
