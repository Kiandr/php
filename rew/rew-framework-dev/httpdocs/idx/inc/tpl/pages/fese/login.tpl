<?php

// Success notices
if (!empty($success)) {
    echo sprintf('<div class="msg msg--pos">%s</div>',
        implode('<br>', $success)
    );
}

// Error notices
if (!empty($errors)) {
    echo sprintf('<div class="msg msg--neg">%s</div>',
        implode('<br>', $errors)
    );
}

// Display login form
if (!empty($show_form)) {

?>
<div class="wrp S2">

    <?=!empty(Settings::getInstance()->SETTINGS['copy_login']) ? '<div class="copy hidden-phone">' . Settings::getInstance()->SETTINGS['copy_login'] . '</div>' : ''; ?>

    <?php if (!empty($networks)) { ?>
        <div class="login-with"><span>Log In With</span></div>
        <?php include $this->locateTemplate('idx', 'misc', 'social-connect'); ?>
        <div class="or"><span>OR</span></div>
    <?php } ?>

    <div class="cols marB-lg">
        <form action="?login" method="post" id="fese_tpl_login">
            <div class="fld col w1/1">
                <label>Email Address</label>
                <input type="email" name="email" value="<?=Format::htmlspecialchars($email); ?>" placeholder="Email Address" required>
            </div>
            <?php if (!empty(Settings::getInstance()->SETTINGS['registration_password'])) { ?>
                <div class="fld col w1/1">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Password" required>
                </div>
            <?php } ?>
            <div class="btns padT">
                <button class="btn btn--primary" type="submit">Sign In</button>
                <?php if (!empty(Settings::getInstance()->SETTINGS['registration_password'])) { ?>
                    <a href="<?=Settings::getInstance()->SETTINGS['URL_IDX_REMIND']; ?>" class="forgot-password">Forgot your password?</a>
                <?php } ?>
            </div>
        </form>
    </div>

    <p class="login-register">
        <span>Don&#39;t have an account? </span>
        <a href="<?=Settings::getInstance()->SETTINGS['URL_IDX_REGISTER']; ?>">Register Now</a>
    </p>

</div>
<?php

} else {

    // Trigger Save Listing
    if (!empty($_SESSION['bookmarkListing'])) {
        $page->writeJS("window.parent.IDX.Favorite({'mls':'" . $_SESSION['bookmarkListing'] . "','force':true,'feed':'" . $_SESSION['bookmarkFeed'] . "'});");
        unset($_SESSION['bookmarkListing'], $_SESSION['bookmarkFeed']);
    }

    // Trigger Save Search
    if (!empty($_SESSION['saveSearch'])) {
        $page->writeJS('window.parent.saveSearch(' . $_SESSION['saveSearch'] . ');');
        unset($_SESSION['saveSearch']);
    } else {

        // Get & Reset Redirect URL
        $url_redirect = $user->url_redirect();
        $user->setRedirectUrl('');

        // Require Verification
        if (!empty(Settings::getInstance()->SETTINGS['registration_verify']) && $user->info('verified') != 'yes') {
            $url_redirect = sprintf(Settings::getInstance()->SETTINGS['URL_IDX_VERIFY'], '');
        }

        // Javascript Callback
        $page->writeJS("setTimeout(function () {
            if (window == window.top) {
                window.location = '" . ($url_redirect ?: Settings::getInstance()->SETTINGS['URL_IDX']) . "';
            } else {
                window.parent.location.reload();
                window.parent.$.Window('close');
            }
        }, 2000);");

    }

}
