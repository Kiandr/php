<?php

    $this->includeFile('tpl/misc/header.tpl.php');

    // Feature background
    $featureAttrs = [];
    $featureClasses = [];
    $backgroundClasses = [];
    $background = $this->variable('background');
    $tintOverlay = $this->variable('background.tint');

    // Background photo
    if ($background === 'photo') {
        $image = $this->variable('background.image');
        $featureAttrs['style'] = sprintf('background-image: url(\'/thumbs/1920x/r%s\');', $image);
        $backgroundClasses[] = 'hero--photo';
        $featureClasses[] = 'hero--photo';

    // Background slideshow
    } else if ($background === 'slideshow') {
        $slides = array_map(function ($image) { $image["image"] = "/thumbs/1920x/r" . $image["image"]; return $image; }, $this->variable('background.slides'));

    // Background video
    } else if ($background === 'video') {
        $videoId = $this->variable('background.video_id');
        $videoAutoplay = $this->variable('background.video_autoplay');
        $videoAutopause = $this->variable('background.video_pause');
        $videoMute = $this->variable('background.video_mute');
        $videoInteract = $this->variable('background.video_interact');
        if ($videoMute) $featureAttrs['data-video-mute'] = true;
        if ($videoAutoplay) $featureAttrs['data-video-autoplay'] = $videoAutoplay;
        if ($videoAutopause) $featureAttrs['data-video-autopause'] = $videoAutopause;
        if ($videoId) $featureAttrs['data-video-id'] = $videoId;
        if (empty($videoInteract) && !empty($videoId)) {
            $backgroundClasses[] = '-is-pointer-disabled';
        }

    // Panoramic background photo
    } else if ($background === 'pano') {
        $panoImage = "/thumbs/x983/r" . $this->variable('background.pano_image');
        $featureAttrs['data-pano-src'] = $panoImage;
        $backgroundClasses[] = '-is-grabbable';

    // 360 background photo
    } else if ($background === '360') {
        $image360 = $this->variable('background.360_image');
        $featureAttrs['data-vr-src'] = $image360;

    }

    // Build HTML
    $htmlAttrs = [];
    foreach ($featureAttrs as $attrName => $attrValue) {
        $htmlAttrs[] = sprintf('%s="%s"', $attrName, $attrValue);
    }

    use REW\Backend\Partner\Inrix\DriveTime;
    use REW\Core\Interfaces\LogInterface;

    // Defaults
    $dt_defaults = [
        'direction' => (!empty($_REQUEST['dt_direction']) ? $_REQUEST['dt_direction'] : 'D'),
        'arrival_time' => (!empty($_REQUEST['dt_arrival_time']) ? $_REQUEST['dt_arrival_time'] : '08:15'),
        'travel_duration' => ((!empty($_REQUEST['dt_travel_duration'])) ? (int) $_REQUEST['dt_travel_duration'] : 30),
    ];

    // Arrival Times - Options
    $arrival_time_options = DriveTime::getArrivalTimeOptions();

    // Travel Duration - Options
    $duration_options = DriveTime::getTravelDurationOptions();

?>
<div id="feature" class="hero hero--cover <?=implode(' ', $featureClasses); ?>"<?=implode(' ', $htmlAttrs); ?>>
    <div class="hero__fg">
        <div class="hero__body -pad-vertical-lg -flex -pad-0">
            <div class="container -pad-vertical-lg -pad-vertical-xxl -<?=$this->variable('foreground.vertical');?>">
                <div id="container_search" class="container -lg -pad-0 -pad-top-xl -pad-top-lg@sm -pad-top-lg@xs -<?=$this->variable('foreground.horizontal');?> -text-<?=$this->variable('foreground.text-align'); ?>">
                    <div class="container -sm">
                        <div class="-mar-bottom-sm -text-xl"><span class="-font-fantasy -text-3x"><?=$this->variable('foreground.preheading'); ?></span></div>
                        <h1 class="hero__heading -txtInvert -mar-bottom-0"><?=Format::truncate($this->variable('foreground.heading'), 150) ;?></h1>
                        <p class="hero__intro -txtInvert"><?=Format::truncate($this->variable('foreground.intro'), 200); ?></p>
                    </div>
                    <?php if ($this->variable('foreground') == 'search') { ?>
                        <?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_DRIVE_TIME']) && in_array('drivetime', Settings::getInstance()->ADDONS)) { ?>
                        <div id="search_options" class="search__options">
                            <button class="button button--ghost button--pill" data-action="search_default" data-active="true">Regular Search</button>
                            <button class="button button--ghost button--pill" data-action="search_drivetime" data-active="false">Drive Time</button>
                        </div>
                        <?php } ?>
                        <form id="search_wrap" action="/idx/" method="get">
                            <input type="hidden" name="feed" value="">
                            <div id="search_default" class="input -pill search" data-active="true">
                                <?php

                                // Display location search
                                echo IDX_Panel::get('Location', [
                                    'inputClass' => 'autocomplete location -pill',
                                    'placeholder'    => sprintf(
                                        'City, %s, Address, %s or %s #',
                                        Locale::spell('Neighborhood'),
                                        Locale::spell('Zip'),
                                        Lang::write('MLS')
                                    ),
                                    'toggle' => false,
                                ])->getMarkup();

                                ?>
                                <button type="submit" class="button button--strong button--pill">Go</button>
                            </div>
                            <?php if (!empty(Settings::getInstance()->MODULES['REW_IDX_DRIVE_TIME']) && in_array('drivetime', Settings::getInstance()->ADDONS)) { ?>
                            <div id="search_drivetime" class="search search--drivetime" data-active="false">
                                <input type="hidden" name="place_zip" value="">
                                <input type="hidden" name="place_lat" value="">
                                <input type="hidden" name="place_lng" value="">
                                <input type="hidden" name="place_zoom" value="">
                                <input type="hidden" name="place_adr" value="">
                                <div class="input -pill">
                                    <div class="search--drivetime__directions">
                                        <label class="-mar-right-xs"><input type="radio" name="dt_direction" value="A" <?=('A' === $dt_defaults['direction'] ? ' checked' : ''); ?> disabled /> To</label>
                                        <label><input type="radio" name="dt_direction" value="D" <?=('D' === $dt_defaults['direction'] ? ' checked' : ''); ?> disabled /> From</label>
                                    </div>
                                    <div class="search--drivetime__wrap -mar-right-sm">
                                        <input placeholder="Enter a Location" class="drivetime-ac-search search--drivetime__location" name="dt_address" value="<?=$_REQUEST['dt_address']; ?>" disabled />
                                        <span class="drivetime-ac-search-tooltip hidden"><span class="dt_caret"></span>You must select from the drop-down list.</span>
                                    </div>
                                    <div class="search--drivetime__duration">
                                        <label class="-mar-right-xs">Arriving at</label>
                                        <div>
                                        <select name="dt_arrival_time" disabled>
                                            <?php
                                                foreach ($arrival_time_options as $option) {
                                                    echo sprintf(
                                                        '<option value="%s"%s>%s</option>',
                                                        $option['value'],
                                                        ($option['value'] === $dt_defaults['arrival_time'] ? ' selected' : ''),
                                                        $option['display']
                                                    );
                                                }
                                            ?>
                                        </select>
                                        <label class="-mar-left-xs -mar-right-xs">in</label>
                                        <select name="dt_travel_duration" disabled>
                                            <?php
                                                foreach ($duration_options as $option) {
                                                    echo sprintf(
                                                        '<option value="%s"%s>%s</option>',
                                                        $option['value'],
                                                        ($option['value'] === $dt_defaults['travel_duration'] ? ' selected' : ''),
                                                        $option['display']
                                                    );
                                                }
                                            ?>
                                        </select>
                                        </div>
                                    </div>
                                    <button class="button button--strong button--pill button--drivetime">
                                        <span class="search--drivetime__label">Search</span>
                                        <svg viewBox="0 0 32 32" class="icon -is-hidden--lg">
                                            <title>Search</title>
                                            <path d="M24.627 23.305l-4.166-4.168c0.979-1.247 1.568-2.815 1.568-4.523 0-4.050-3.281-7.333-7.328-7.333s-7.328 3.283-7.328 7.333 3.281 7.333 7.328 7.333c1.618 0 3.109-0.531 4.322-1.42l4.191 4.192 1.413-1.414zM9.401 14.614c0-2.924 2.378-5.303 5.3-5.303 2.923 0 5.3 2.379 5.3 5.303s-2.378 5.303-5.3 5.303c-2.923 0-5.3-2.379-5.3-5.303z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <?php } ?>
                        </form>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div id="cover__background" class="hero__bg <?=implode(' ', $backgroundClasses); ?>">
        <div class="cloak<?=(!empty($tintOverlay) ? ' cloak--' . $tintOverlay : ''); ?>"></div>
        <?php if ($background === 'slideshow') { ?>
            <div data-slideshow>
                <?php foreach ($slides as $i => $image) { ?>
                    <div class="slide<?=($i === 0 ? ' active' : ''); ?>" style="background-image: url('<?=$image['image']; ?>'); filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=$image['image']; ?>', sizingMethod='scale');"></div>
                <?php } ?>
            </div>
            <?php if(count($slides) > 1) { ?>
            <div class="slider-controls">
                <button class="button button--ghost -left -pad-sm">
                    <svg class="prev" viewBox="0 0 9.9 16.8" width="60" height="60" role="img" aria-labelledby="title">
                        <title>Previous</title>
                        <desc>View the Previous slide</desc>
                        <path d="M309.14,324.53a1.5,1.5,0,0,1-1.06-2.56l5.84-5.84-5.84-5.84a1.5,1.5,0,0,1,2.12-2.12l6.9,6.9a1.5,1.5,0,0,1,0,2.12l-6.9,6.9A1.5,1.5,0,0,1,309.14,324.53Z" transform="translate(-307.64 -307.72)"></path>
                    </svg>
                </button>
                <button class="button button--ghost -right -pad-sm">
                    <svg class="next" id="icon--left-arrow" viewBox="0 0 9.9 16.8" width="60" height="60" role="img" aria-labelledby="title">
                        <title>Next</title>
                        <desc>View the next slide</desc>
                        <path d="M309.14,324.53a1.5,1.5,0,0,1-1.06-2.56l5.84-5.84-5.84-5.84a1.5,1.5,0,0,1,2.12-2.12l6.9,6.9a1.5,1.5,0,0,1,0,2.12l-6.9,6.9A1.5,1.5,0,0,1,309.14,324.53Z" transform="translate(-307.64 -307.72)"></path>
                    </svg>
                </button>
            </div>
            <?php } ?>
        <?php } ?>
    </div>

</div>

<div id="body" class="-pad-vertical">
    <div id="content">
        <div class="container">
            <?php if ($this->container('content-features')->countModules() > 0) { ?>
                <?php $this->container('content-features')->loadModules(); ?>
            <?php } ?>
            <?php $this->container('content')->loadModules(); ?>
            <?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
        </div>
    </div>
</div>

<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>
