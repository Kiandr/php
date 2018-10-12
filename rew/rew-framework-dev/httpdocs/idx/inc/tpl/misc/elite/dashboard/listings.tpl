<section class="section section-listings uk-margin-small-left">
    <h3>My Listings</h3>
    <div class="uk-nbfc uk-clearfix uk-margin-bottom">
        <?= $feed_switcher; ?>
        <ul class="uk-subnav uk-subnav-line uk-float-left">
            <?php foreach ($listing_filters as $listing_filter) { ?>
            <?php $selected = $current_filter['link'] == $listing_filter['link'] ? ' class="uk-active"' : ''; ?>
                <li<?= $selected; ?>>
                    <a href="<?= Format::htmlspecialchars($listing_filter['url']); ?>">
                        <?= Format::htmlspecialchars($listing_filter['title']); ?>
                        <?php if (isset($listing_filter['count'])) { ?>
                            (<?= Format::number($listing_filter['count']); ?>)
                        <?php } ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
    <?php if (!empty($listings)) { ?>
        <?php
            $result_tpl = $page->locateTemplate('idx', 'misc', 'result');
        ?>
        <div class="dashboard-idx-listings idx-data">
            <div class="uk-grid uk-grid-medium">
                <?php foreach ($listings as $result) { ?>
                    <?php require $result_tpl; ?>
                <?php } ?>
            </div>
        </div>
        <?php if (!empty($pagination)) { ?>
            <div class="pagination uk-text-center">
                <?php if (!empty($pagination['prev'])) { ?>
                    <a class="prev uk-text-large" href="<?= Format::htmlspecialchars($pagination['prev']); ?>"><i class="uk-icon uk-icon-angle-double-left"></i> Previous</a>
                <?php } ?>
                <?php if (!empty($pagination['next'])) { ?>
                    <a class="next uk-text-large" href="<?= Format::htmlspecialchars($pagination['next']); ?>">Next <i class="uk-icon uk-icon-angle-double-right"></i></a>
                <?php } ?>
            </div>
        <?php } ?>
    <?php } else { ?>
        <div class="uk-alert uk-alert-danger uk-margin-top">
            You currently have no <?= Format::htmlspecialchars($current_filter['link']); ?> listings
            <?php if (!empty($selected_feed)) echo ' in ' . Format::htmlspecialchars($selected_feed); ?>
        </div>
    <?php } ?>
    <div><?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?></div>
</section>
