<?php if (!empty($show_form)) { ?>
    <div class="modal-header">
        <h1>Login to Access your Dashboard</h1>
    </div>
    <div class="modal-body">
        <?php include $page->locateTemplate('idx', 'misc', 'messages'); ?>
        <form class="uk-form" method="post" action="?login" id='elite_idx_tpl_login'>
            <fieldset>

                <?php if (!empty(Settings::getInstance()->SETTINGS['copy_login'])) { ?>
                    <p><?= Settings::getInstance()->SETTINGS['copy_login']; ?></p>
                <?php } ?>

                <div class="uk-form-row">
                    <input type="email" name="email" value="<?= Format::htmlspecialchars($email); ?>" placeholder="Email Address" class="uk-form-width-large uk-form-large" required>
                </div>
                <?php if (!empty(Settings::getInstance()->SETTINGS['registration_password'])) { ?>
                    <div class="uk-form-row">
                        <input type="password" name="password" placeholder="Password" class="uk-form-width-large uk-form-large" required>
                    </div>
                <?php } ?>
                <div class="uk-form-row">
                    <button type="submit" class="uk-button uk-button-medium">Sign In</button>
                </div>
                <?php if (!empty(Settings::getInstance()->SETTINGS['registration_password'])) { ?>
                    <p><a class="forgot-password" href="<?=Settings::getInstance()->SETTINGS['URL_IDX_REMIND']; ?>">Forgot your password?</a></p>
                <?php } ?>
            </fieldset>
        </form>
        <?php if (!empty($networks)) { ?>
            <h2>Or login using one of the following...</h2>
            <div class="social-media-login-buttons">
                <?php foreach ($networks as $id => $network) { ?>
                    <a class="network-login <?=$id; ?> oauth uk-icon-button uk-icon-<?=$id; ?>" href="<?= Format::htmlspecialchars($network['connect']); ?>"></a>
                <?php } ?>
            </div>
        <?php } ?>

        <h3>Need an Account?</h3>
        <a class="uk-button" href="<?= Format::htmlspecialchars(Settings::getInstance()->SETTINGS['URL_IDX_REGISTER']); ?>" data-modal="register">Register Now</a>
    </div>

<?php }else{ ?>
    <div class="modal-header">
        <h1>Success</h1>
    </div>
    <div class="modal-body">
        <?php include $page->locateTemplate('idx', 'misc', 'messages');
        // Trigger Save Listing
        if (!empty($_SESSION['bookmarkListing'])) { ?>
            <script>IDX.Favorite({'mls':'<?= $_SESSION['bookmarkListing']; ?>','force':true,'feed':'<?= $_SESSION['bookmarkFeed']; ?>'});</script>
            <?php unset($_SESSION['bookmarkListing'], $_SESSION['bookmarkFeed']);
        }
        // Trigger Save Search
        if (!empty($_SESSION['saveSearch'])) { ?>
            <script>IDX.SaveSearch(<?= $_SESSION['saveSearch']; ?>);</script>
            <?php unset($_SESSION['saveSearch']);
        } ?>
    </div>
<?php } ?>

