<?php

// Module Disabled
if (empty(Settings::getInstance()->MODULES['REW_FEATURED_COMMUNITIES'])) return;

// No communities added
if (empty($communities)) return;

// URL to view all communities
$url_communities = '/neighborhoods.php';

// Find last index
end($communities);
$last_index = key($communities);

?>

<!-- THIS BLOCK VISIBLE TO 959 -->
<div class="uk-hidden-large featured-communities">
    <!-- THIS TITLE IS VISIBLE up to 767 --> 
    <div class="uk-visible-small">
        <div class="featured-communities-title-box uk-position-relative">
            <div class="featured-communities-title">
                <h3><?= Format::htmlspecialchars($this->config('heading')); ?></h3>
                <a href="<?= Format::htmlspecialchars($url_communities); ?>"><?= Format::htmlspecialchars($this->config('subheading')); ?> <i class="uk-icon-angle-right"></i></a>
            </div>
        </div>
    </div>
    <!-- THIS TITLE VISIBLE FROM 768 TO 959 -->	
    <div class="uk-visible-medium">
        <div class="uk-container uk-container-center uk-margin-bottom-remove uk-margin-top-remove uk-padding-bottom-remove uk-padding-top-remove">
            <div class="featured-communities-title-box uk-position-relative">
                <div class="featured-communities-title">
                    <h3 class="uk-align-left"><?= Format::htmlspecialchars($this->config('heading')); ?></h3>
                    <a class="uk-align-right" href="<?= Format::htmlspecialchars($url_communities); ?>">View All Community Guides <i class="uk-icon-angle-right"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="uk-container uk-container-center">
        <div class="uk-grid uk-grid-small">
            <?php foreach ($communities as $index => $community) { ?>
            <?php $width = $index == $last_index && count($communities) % 2 == 1 ? 1 : 2; ?>
            <div class="uk-width-small-1-<?= $width; ?> uk-width-medium-1-<?= $width; ?> uk-position-relative">
                <div class="uk-cover-background featured-community-box uk-position-relative deferred" style="background-image: url('<?= Format::htmlspecialchars($community['image']); ?>');">
                    <img width="600" height="400" alt="" src="<?= Format::htmlspecialchars($placeholder); ?>" class="uk-invisible">
                    <a href="<?= Format::htmlspecialchars($community['url']); ?>" title="<?= Format::htmlspecialchars($community['title']); ?>" class="uk-position-cover uk-flex uk-flex-center uk-flex-bottom featured-communities-overlay"></a>
                    <a href="<?= Format::htmlspecialchars($community['url']); ?>" class="uk-position-bottom uk-text-center featured-community-title"><?= Format::htmlspecialchars($community['title']); ?></a>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php

$template_file = '';
while (!$template_file) {
    $template_file = $this->locateFile('grids/feature-c' . count($communities) . '.tpl.php');

    if (!$template_file) {
        array_pop($communities);
    }
}

require $template_file;
?>
