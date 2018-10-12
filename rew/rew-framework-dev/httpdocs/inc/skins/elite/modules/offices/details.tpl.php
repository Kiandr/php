<?php if (empty($office)) { ?>
    <div class="uk-alert uk-alert-danger">
        <p>We're sorry, but the office you were looking for could not be found.</p>
    </div>

<?php } else { ?>

    <div class="office detailed<?=(!empty($class) ? ' ' . $class : ''); ?>">

        <h1><?= Format::htmlspecialchars($office['title']); ?></h1>

        <div class="uk-article">

            <div class="uk-grid">
                <div class="uk-width-1-1 uk-width-small-1-2 uk-width-medium-1-3 uk-width-large-1-4">
                    <span><img class="uk-align-center uk-width-1-1" data-src="<?= Format::htmlspecialchars($office['image']); ?>" src="<?= Format::htmlspecialchars($office_placeholder); ?>" alt=""></span>
                </div>

                <div class="uk-width-1-1 uk-width-small-1-2 uk-width-medium-2-3 uk-width-large-3-4">

                    <p class="description"><?= Format::htmlspecialchars($office['description']); ?></p>

                    <ul class="uk-list">
                        <?php if (!empty($office['location'])) { ?>
                            <li class="keyval location"><strong>Address</strong> <span> <?= Format::htmlspecialchars($office['location']); ?></span></li>
                        <?php } ?>
                        <?php if (!empty($office['phone'])) { ?>
                            <li class="keyval phone"><strong>Phone #</strong> <span><?= Format::htmlspecialchars($office['phone']); ?></span></li>
                        <?php } ?>
                        <?php if (!empty($office['fax'])) { ?>
                            <li class="keyval fax"><strong>Fax #</strong> <span><?= Format::htmlspecialchars($office['fax']); ?></span></li>
                        <?php } ?>
                        <?php if (!empty($office['email'])) { ?>
                            <li class="keyval email"><strong>Email</strong> <span><a href="mailto:<?= $office['email']; ?>"><?= $office['email']; ?></a></span></li>
                        <?php } ?>
                    </ul>

                </div>
            </div>

        </div>

    </div>

    <?php if (!empty($office['agents'])) { ?>
        <div class="articleset agents">
            <h2>Agents in this Office</h2>
            <?php foreach ($office['agents'] as $agent) { ?>
                <article class="uk-article">
                    <div class="uk-grid">
                        <div class="uk-width-1-4">
                            <?php if (!empty($agent['link'])) { ?><a href="<?= sprintf(Settings::getInstance()->URLS['URL_AGENT'], $agent['link']); ?>" class="uk-thumbnail"><?php } ?>
                                <img data-resize='{ "ratio" : "1:1" }' data-src="<?= Format::htmlspecialchars($agent['image']); ?>" src="<?= Format::htmlspecialchars($placeholder); ?>" alt="">
                                <?php if (!empty($agent['link'])) { ?></a><?php } ?>
                        </div>
                        <div class="uk-width-3-4">
                            <h1 class="uk-article-title"><?=Format::htmlspecialchars($agent['name']) . (!empty($agent['title']) ? ' - <small>' . Format::htmlspecialchars($agent['title']) . '</small>' : ''); ?></h1>
                            <?php
                            $truncate = !empty($this->config['truncate']) && is_int($this->config['truncate']) ? $this->config['truncate'] : 125;
                            $agent['remarks'] = Format::stripTags(Format::truncate($agent['remarks'] = nl2br(trim(Format::htmlspecialchars($agent['remarks']), "\r\n ")), $truncate, '&hellip;'));
                            ?>
                            <p class="uk-article-lead"><?= $agent['remarks']; ?></p>
                            <ul class="uk-list uk-list-line uk-list-space">
                                <?php if (!empty($agent['office_phone'])) { ?>
                                    <li><strong>Office</strong>:    <a href="tel:<?=Format::htmlspecialchars($agent['office_phone']); ?>"><?=Format::htmlspecialchars($agent['office_phone']); ?></a></li>
                                <?php } ?>
                                <?php if (!empty($agent['cell_phone'])) { ?>
                                    <li><strong>Cell</strong>: <a href="tel:<?=Format::htmlspecialchars($agent['cell_phone']); ?>"><?=Format::htmlspecialchars($agent['cell_phone']); ?></a></li>
                                <?php } ?>
                                <?php if (!empty($agent['home_phone'])) { ?>
                                    <li><strong>Home</strong>: <?=Format::htmlspecialchars($agent['home_phone']); ?></li>
                                <?php } ?>
                                <?php if (!empty($agent['fax'])) { ?>
                                    <li><strong>Fax</strong>: <?=Format::htmlspecialchars($agent['fax']); ?></li>
                                <?php } ?>
                                <?php if (!empty($agent['email'])) { ?>
                                    <li><strong>Email</strong>: <a href="mailto:<?= $agent['email']; ?>"><?= $agent['email']; ?></a></li>
                                <?php } ?>
                                <?php if (!empty($agent['website'])) { ?>
                                    <li><strong>Website</strong>: <a href="<?= Format::htmlspecialchars($agent['website']); ?>" target="_blank"><?=Format::htmlspecialchars($agent['website']); ?></a></li>
                                <?php } ?>
                            </ul>

                            <?php if (!empty($agent['link'])) { ?>
                                <a class="uk-button" href="<?= sprintf(Settings::getInstance()->URLS['URL_AGENT'], $agent['link']); ?>">Read More <i class="uk-icon uk-icon-chevron-right"></i></a>
                            <?php } ?>
                        </div>
                    </div>
                    <hr class="uk-article-divider">
                </article>
            <?php } ?>
        </div>
    <?php } ?>

<?php } ?>

<div class="uk-margin-top">
    <a class="uk-button" href="<?= strstr(Settings::getInstance()->URLS['URL_OFFICE'], '?', true) ?: Settings::getInstance()->URLS['URL_OFFICE']; ?>">View All Offices</a>
</div>
