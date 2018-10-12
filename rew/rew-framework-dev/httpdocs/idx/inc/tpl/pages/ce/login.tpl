<?php

// Success notices
if (!empty($success)) {
    echo sprintf('<div class="notice notice--positive -mar-bottom"><div class="notice__message">%s</div></div>',
        implode('<br>', $success)
    );
}

// Error notices
if (!empty($errors)) {
    echo sprintf('<div class="notice notice--negative -mar-bottom"><div class="notice__message">%s</div></div>',
        implode('<br>', $errors)
    );
}

// Display login form
if (!empty($show_form)) {

    // Show password field
    $requirePassword = !empty(Settings::getInstance()->SETTINGS['registration_password']);

?>
<div class="login-page container -sm">
	
	<h1>Sign In</h1>

    <?=!empty(Settings::getInstance()->SETTINGS['copy_login']) ? '<div class="copy hidden-phone">' . Settings::getInstance()->SETTINGS['copy_login'] . '</div>' : ''; ?>

    <form action="?login" method="post" id='idx_ce_tpl_login'>
        <div class="field login__field<?=$requirePassword ? ' field--stacked' : ''; ?>">
            <label id="login_email_label" for="login_email_input">Email Address</label>
            <input id="login_email_input" aria-labelledby="login_email_label" type="email" name="email" value="<?=Format::htmlspecialchars($email); ?>" required>
        </div>
        <?php if ($requirePassword) { ?>
            <div class="field login__field field--stacked">
                <label id="login_password_label" for="login_password_input">Password</label>
                <input id="login_password_input" aria-labelledby="login_password_label" type="password" name="password" required>
            </div>
        <?php } ?>
        <?php if ($_GET['opt_marketing'] === 'in') { ?>
            <input id="login_newsletter" type="hidden" name="login_newsletter" value="newsletter">
        <?php } ?>
        <div class="buttons columns">
            <button class="button button--pill button--strong column -width-1/2 -width-1/1@sm" type="submit">Sign In</button>
			<a class="button button--ghost column -width-1/2 -width-1/1@sm" href="<?=Settings::getInstance()->SETTINGS['URL_IDX_REGISTER']; ?>">Create An Account</a>
        </div>
    </form>
	
	<?php if (!empty($networks)) { ?>
		<div class="or"><span>OR</span></div>
        <?php include $this->locateTemplate('idx', 'misc', 'social-connect'); ?>
    <?php } ?>
	
	<?php if ($requirePassword) { ?>
		<a href="<?=Settings::getInstance()->SETTINGS['URL_IDX_REMIND']; ?>" class="forgot-password">Forgot your password?</a>
	<?php } ?>

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

        $login_newsletter = '?' . $_POST['login_newsletter'] ?: '';

        // Javascript Callback
        $page->writeJS("setTimeout(function () {
            if (window == window.top) {
                window.location = '" . ($url_redirect ?: '/') . $login_newsletter . "';
            } else {
                window.parent.location.reload();
                window.parent.$.Window('close');
            }
        }, 2000);");

    }

}
