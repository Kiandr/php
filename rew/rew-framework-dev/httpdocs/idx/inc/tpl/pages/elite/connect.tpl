<h1>Social Media Connect</h1>

<div class="modal-body">
    <?php include $page->locateTemplate('idx', 'misc', 'messages'); ?>
    <?php if (!empty($show_form)) { ?>

        <?php if (!empty(Settings::getInstance()->SETTINGS['copy_connect'])) { ?>
            <p class="uk-hidden-small">
                <?= Settings::getInstance()->SETTINGS['copy_connect']; ?>
            </p>
        <?php } ?>

        <div class="uk-grid">
            <div class="uk-width-1-1 uk-width-medium-1-3">
                <article>
                    <header>
                        <?php if (!empty($profile['link'])) { ?>
                            <h4><a href="<?= Format::htmlspecialchars($profile['link']); ?>" target="_blank"><?= Format::htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']); ?></a></h4>
                        <?php } else { ?>
                            <h4><?= Format::htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']); ?></h4>
                        <?php } ?>
                    </header>

                    <?php if (!empty($profile['image'])) { ?>
                        <div>
                            <?php if (!empty($profile['link'])) { ?>
                                <a href="<?= Format::htmlspecialchars($profile['link']); ?>"><img class="uk-width-1-1" src="<?= Format::htmlspecialchars($profile['image']); ?>" alt=""></a>
                            <?php } else { ?>
                                <img class="uk-width-1-1" src="<?= Format::htmlspecialchars($profile['image']); ?>" alt="">
                            <?php } ?>
                        </div>
                    <?php } ?>
                </article>
            </div>

            <div class="uk-width-1-1 uk-width-medium-2-3">

                <?php require $page->locateTemplate('idx', 'misc', 'register_form'); ?>

            </div>
        </div>

    </div>

    <?php } else { ?>

        <?php include $page->locateTemplate('idx', 'misc', 'messages');

        // Conversion tracking script
        $ppc = Util_CMS::getPPCSettings();
        if (!empty($ppc) && $ppc['enabled'] === 'true' && $is_rt && !empty($ppc['rt-register'])) {
            $this->getSkin()->includeFile('tpl/partials/tracking.tpl.php', [
                'trackingScript' => $ppc['rt-register']
            ]);
        } else if (!empty($ppc) && $ppc['enabled'] === 'true' && !$is_rt &&!empty($ppc['idx-register'])) {
            $this->getSkin()->includeFile('tpl/partials/tracking.tpl.php', [
                'trackingScript' => $ppc['idx-register']
            ]);
        }

        // Trigger Save Listing
        if (!empty($_SESSION['bookmarkListing'])) { ?>
            <script>IDX.Favorite({'mls':'<?= $_SESSION['bookmarkListing']; ?>','force':true,'feed':'<?= $_SESSION['bookmarkFeed']; ?>'});</script>
            <?php unset($_SESSION['bookmarkListing'], $_SESSION['bookmarkFeed']);
        }
        // Trigger Save Search
        if (!empty($_SESSION['saveSearch'])) { ?>
            <script>IDX.SaveSearch(<?= $_SESSION['saveSearch']; ?>);</script>
            <?php unset($_SESSION['saveSearch']);
        }
    } ?>
</div>