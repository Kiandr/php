<div class="msg"><p>Don't have an account? <a href="<?=Settings::getInstance()->SETTINGS['URL_IDX_REGISTER']; ?>">Register Now</a></p></div>

<h1>Sign In</h1>

<?=(!empty($success) ? '<div class="msg positive"><p>' . implode('</p><p>', $success) . '</p></div>' : ''); ?>
<?=(!empty($errors) ? '<div class="msg negative"><p>' . implode('</p><p>', $errors) . '</p></div>' : ''); ?>

<?php if (!empty($show_form)) { ?>

    <?=!empty(Settings::getInstance()->SETTINGS['copy_login']) ? '<div class="copy hidden-phone">' . Settings::getInstance()->SETTINGS['copy_login'] . '</div>' : ''; ?>

    <form action="?login" method="post" id="brew_tpl_login">

        <div class="field x12">
            <label>Email</label>
            <input type="email" name="email" value="<?=htmlentities($email); ?>" required>
        </div>

        <?php if (!empty(Settings::getInstance()->SETTINGS['registration_password'])) { ?>
	        <div class="field x12">
	            <label>Password</label>
	            <input type="password" name="password" required>
	        </div>
        <?php } ?>

        <div class="btnset">
            <?php if (!empty(Settings::getInstance()->SETTINGS['registration_password'])) { ?>
                <a href="<?=Settings::getInstance()->SETTINGS['URL_IDX_REMIND']; ?>" class="pright">Forgot your password?</a>
            <?php } ?>
            <button class="strong" type="submit">Sign In</button>
        </div>

    </form>

	<?php if (!empty($networks)) { ?>
		<div class="networks">
			<h3>Or Connect Using&hellip;</h3>
			<?php foreach ($networks as $network) { ?>
				<a href="javascript:var w = window.open('<?=$network['connect']; ?>', 'socialconnect', 'toolbar=0,status=0,scrollbars=1,width=600,height=450,left='+(screen.availWidth/2-225)+',top='+(screen.availHeight/2-250)); w.focus();"><img src="<?=Settings::getInstance()->SETTINGS['URL_IMG']; ?>icons/32/<?=$network['image']; ?>" alt="Login with <?=$network['title']; ?>"></a>
			<?php } ?>
		</div>
	<?php } ?>

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

		// Default to IDX
		$url_redirect = !empty($url_redirect) ? $url_redirect : Settings::getInstance()->SETTINGS['URL_IDX'];

		// Javascript Callback
		$page->writeJS("setTimeout(function () {
			if (window == window.top) {
				window.location = '" . $url_redirect . "';
			} else {
				window.parent.location.reload();
				window.parent.$.Window('close');
			}
		}, 2000);");

	}

}

?>