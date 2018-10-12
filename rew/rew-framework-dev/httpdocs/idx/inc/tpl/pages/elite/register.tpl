<?php if (!empty($show_form)) { ?>
    <div class="modal-header">
        <h1>Register for your Free Account</h1>
    </div>
    <?php if (!empty(Settings::getInstance()->SETTINGS['copy_register'])) { ?>
        <p class="uk-hidden-small">
            <?= Settings::getInstance()->SETTINGS['copy_register']; ?>
        </p>
    <?php } ?>

    <div class="modal-body">
        <?php include $page->locateTemplate('idx', 'misc', 'messages'); ?>

        <?php require $page->locateTemplate('idx', 'misc', 'register_form'); ?>

        <h2>Do you already have an account?</h2>
        <a class="uk-button" href="<?= Format::htmlspecialchars(Settings::getInstance()->SETTINGS['URL_IDX_LOGIN']); ?>" data-modal="login">Login Now</a>

        <?php if (!empty($networks)) { ?>
            <h3>Or login using one of the following...</h3>

            <div class="social-media-login-buttons">
                <?php foreach ($networks as $id => $network) { ?>
                    <a class="network-login <?= $id; ?> oauth uk-icon-button uk-icon-<?= $id; ?>" href="<?= Format::htmlspecialchars($network['connect']); ?>"></a>
                <?php } ?>
            </div>
        <?php } ?>
    </div>

<?php } else { ?>
    <div class="modal-body">
        <?php include $page->locateTemplate('idx', 'misc', 'messages');

        // Conversion tracking script
        if (!empty($ppc) && $ppc['enabled'] === 'true' && !empty($ppc['idx-register'])) {
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

        // Require Verification
        if ((!empty(Settings::getInstance()->SETTINGS['registration_verify']) && !Validate::verifyWhitelisted($user->info('email'))) || Validate::verifyRequired($user->info('email'))) { ?>
            <div class="modal-header">
                <h1>Thank You!</h1>
            </div>
            <div class="modal-body">
                <p>A verification code has been sent to you at <span class="uk-text-bold"><?= Format::htmlspecialchars($user->info('email')); ?></span>.</p>
                <p>
                    Just one more step is needed to finish your registration. Please verify that we have your correct email address by clicking on the verification link we sent to you. If you don't receive instructions within a minute or two, check your email's spam and junk filters, or
                    <a data-modal="verify" href="<?= Format::htmlspecialchars(sprintf(Settings::getInstance()->SETTINGS['URL_IDX_VERIFY'], '')); ?>">try resending your code</a>.
                </p>
            </div>
        <?php } ?>
    </div>
<?php } ?>
