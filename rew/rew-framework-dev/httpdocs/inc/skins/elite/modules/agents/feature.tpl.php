<?php if (!empty($agents)) { ?>
    <?php foreach ($agents as $i => $agent) { ?>
        <div class="uk-width-1-1 uk-width-medium-1-2 uk-width-large-1-4 fa-container">
            <div class="uk-grid uk-grid-collapse fa-inner-container">
                <div class="uk-width-1-3 uk-width-small-1-5 uk-width-medium-1-3 uk-width-large-1-1 fw-container fw-container-photo">
                    <?php if (!empty($agent['link'])) { ?>
                    <a href="<?=$agent['link']; ?>" title="<?= Format::htmlspecialchars($agent['name']); ?>">
                    <?php } ?>
                    <?php if ($this->config('deferImages')) { ?>
                        <div class="uk-cover-background uk-position-relative agent-photo-container deferred" data-fw-deferred-img-config="<?= Format::htmlspecialchars(json_encode(array('src' => $agent['image'], 'sizes' => $this->getPage()->getSkin()->getPhotoSizes($agent['image']), 'style' => array('backgroundImage' => 'url(' . Format::htmlspecialchars($agent['image']) . ')')))); ?>"></div>
                    <?php } else { ?>
                        <div class="uk-cover-background uk-position-relative agent-photo-container" style="background-image: url('<?= Format::htmlspecialchars($agent['image']); ?>');"></div>
                    <?php } ?>
                    <?php if (!empty($agent['link'])) { ?>
                    </a>
                    <?php } ?>
                </div>
                <div class="uk-width-2-3 uk-width-small-4-5 uk-width-medium-2-3 uk-width-large-1-1 fw-container fw-container-details">
                    <div class="agent-info-container">
                        <?php if (!empty($agent['link'])) { ?>
                        <h5><a href="<?=$agent['link']; ?>" style="text-decoration:none;" title="<?= Format::htmlspecialchars($agent['name']); ?>"><?=Format::htmlspecialchars($agent['first_name']); ?> <span><?=Format::htmlspecialchars($agent['last_name']); ?></span></a></h5>
                        <?php } else { ?>
                        <h5><span style="text-decoration:none;" title="<?= Format::htmlspecialchars($agent['name']); ?>"><?=Format::htmlspecialchars($agent['first_name']); ?> <span><?=Format::htmlspecialchars($agent['last_name']); ?></span></span></h5>
                        <?php } ?>
                        <div class="agent-social-media">
                            <?php foreach ((new Backend_Agent($agent))->getSocialNetworks() as $network) { ?>
                                <a href="<?= Format::htmlspecialchars($network['url']); ?>" target="_blank" title="<?= ucfirst(Format::htmlspecialchars($network['slug'])); ?>"><i class="uk-icon-button uk-icon-<?= Format::htmlspecialchars($network['slug']); ?>"></i></a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /.fa-container -->
    <?php } ?>
<?php } ?>
