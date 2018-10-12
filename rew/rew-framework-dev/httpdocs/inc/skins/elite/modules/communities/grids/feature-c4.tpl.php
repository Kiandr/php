<?php $primary_community = array_shift($communities); ?>
<!-- THIS BLOCK VISIBLE FROM 960 AND LARGER - 4 COMMUNITIES -->
<div class="uk-visible-large featured-communities">
    <div class="uk-container uk-container-center">
        <div class="uk-grid">
            <div class="uk-width-1-2">
                <div class="uk-grid">
                    <div class="uk-width-1-2">
                        <div class="featured-communities-title-box uk-position-relative">
                            <div class="featured-communities-title">
                                <h3><?= Format::htmlspecialchars($this->config('heading')); ?></h3>
                                <a href="<?= Format::htmlspecialchars($url_communities); ?>"><?= Format::htmlspecialchars($this->config('subheading')); ?> <i class="uk-icon-angle-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <?php foreach ($communities as $index => $community) { ?>
                        <div class="uk-width-1-2">
                            <div class="uk-cover-background featured-community-box uk-position-relative" style="background-image: url('<?= Format::htmlspecialchars($community['image']); ?>');">
                                <img width="600" height="400" alt="" src="<?= Format::htmlspecialchars($placeholder); ?>" class="uk-invisible">
                                <a href="<?= Format::htmlspecialchars($community['url']); ?>" title="<?= Format::htmlspecialchars($community['title']); ?>" class="uk-position-cover uk-flex uk-flex-center uk-flex-bottom featured-communities-overlay"></a>
                                <a href="<?= Format::htmlspecialchars($community['url']); ?>" class="uk-position-bottom uk-text-center featured-community-title"><?= Format::htmlspecialchars($community['title']); ?></a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="uk-width-1-2">
                <div class="uk-cover-background featured-community-box primary uk-position-relative" style="background-image: url('<?= Format::htmlspecialchars($primary_community['image']); ?>');">
                    <img width="600" height="400" alt="" src="<?= Format::htmlspecialchars($placeholder); ?>" class="uk-invisible">
                    <a href="<?= Format::htmlspecialchars($primary_community['url']); ?>" title="<?= Format::htmlspecialchars($primary_community['title']); ?>" class="uk-position-cover uk-flex uk-flex-center uk-flex-bottom featured-communities-overlay"></a>
                    <a href="<?= Format::htmlspecialchars($primary_community['url']); ?>" class="uk-position-bottom uk-text-center featured-community-title"><?= Format::htmlspecialchars($primary_community['title']); ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
