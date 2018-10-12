<?php

// Placeholder text
$placeholderText = Format::htmlspecialchars(sprintf(
    'City, %s, Address, %s or %s #',
    Locale::spell('Neighborhood'),
    Locale::spell('Zip'),
    Lang::write('MLS')
));

// IDX Feed
$idxFeed = Settings::getInstance()->IDX_FEED;


// Display IDX feed switcher
if ($this->getPage()->variable('showFeedSwitcher')) {
    $feed_output = $this->getContainer()->addModule('idx-feeds', array(
        'template' => 'idx-search.tpl.php',
        'disabled' => !!$this->config('advanced')
    ))->display(false);
}
// Display search form
$searchForm = <<<EOT
<div class="feat-search" style="opacity: 1 !important; position: static; margin-top: 10px;">
    <div class="wrp S4">
        <div class="feat-search-box">

            <form action="/idx/" method="get">
                <input type="hidden" name="feed" value="{$idxFeed}">
                <div class="idx-search-form" style="width: 100%;">
                    <div class="mmm search-input-container">
                        $feed_output
                        <div class="ac-input">
                            <input name="search_location" value="" placeholder="{$placeholderText}" class="autocomplete location" autocomplete="off">
                            <button type="submit" class="btn btn--primary">
                                <svg style="width:16px;height:16px;vertical-align: middle; position: relative; top: -1px; margin: -1px 0 0 0" viewBox="0 0 24 24">
                                    <path fill="#fff" d="M9.5,3A6.5,6.5 0 0,1 16,9.5C16,11.11 15.41,12.59 14.44,13.73L14.71,14H15.5L20.5,19L19,20.5L14,15.5V14.71L13.73,14.44C12.59,15.41 11.11,16 9.5,16A6.5,6.5 0 0,1 3,9.5A6.5,6.5 0 0,1 9.5,3M9.5,5C7,5 5,7 5,9.5C5,12 7,14 9.5,14C12,14 14,12 14,9.5C14,7 12,5 9.5,5Z"></path>
                                </svg>
                                Search
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
EOT;

// Display featured slides
$slides = $this->config['slides'];
if (!empty($slides) && is_array($slides)) {
    echo sprintf('<div id="%s">', $this->getUID());
    foreach ($slides as $slide) {
        if (empty($slide['image'])) {
            continue;
        }
        $slideClass = ++$count === 1 ? ' active' : '';
        $slideStyles = "background-image: url('%s'); filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='%s', sizingMethod='scale');";
        $slideStyles = sprintf($slideStyles, $slide['image'], $slide['image']);
        $slidePosition = sprintf('slide-%s%s', $slide['posVertical'], $slide['posHorizontal']);
        $slideSearch = !empty($slide['showSearchForm']) ? ' data-search="true"' : '';
        echo sprintf('<div class="slide%s" style="%s"%s>', $slideClass, $slideStyles, $slideSearch);
        $hasTitle = !empty($slide['title']);
        $hasHeading = !empty($slide['heading']);
        $hasParagraph = !empty($slide['paragraph']);
        $hasButton = !empty($slide['buttonText']) && !empty($slide['buttonLink']);
        echo sprintf('<div class="slide-cta %s">', $slidePosition);
        if ($hasTitle || $hasHeading || $hasParagraph || $hasButton) {
            if ($hasTitle) {
                echo sprintf('<span>%s</span>', $slide['title']);
            }
            if ($hasHeading) {
                echo sprintf('<h1>%s</h1>', $slide['heading']);
            }
            if ($hasParagraph) {
                echo sprintf('<p class="italic">%s</p>', $slide['paragraph']);
            }
            if ($hasButton) {
                echo sprintf('<a href="%s" class="slide-btn">%s</a>', $slide['buttonLink'], $slide['buttonText']);
            }
        }
        if (!empty($slide['showSearchForm'])) {
            echo $searchForm;
        }
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
}
