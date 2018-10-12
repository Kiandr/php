<?php

// Success messages
if (!empty($success)) {
	echo '<div class="msg positive"><p>' . implode('</p><p>', $success) . '</p></div>';
}

// Error messages
if (!empty($errors)) {
	echo '<div class="msg negative"><p>' . implode('</p><p>', $errors) . '</p></div>';
}

// Show login form
if (!empty($show_form)) {

	// Login form text
	$copy_login = Settings::getInstance()->SETTINGS['copy_login'];

	// Split form into two columns
	$colset = (!empty($copy_login) || !empty($networks));
	if (!empty($colset)) {
		echo '<div class="colset">';
		echo '<div class="col width-1/2 width-1-sm">';

		// Login form text
		$copy_login = Settings::getInstance()->SETTINGS['copy_login'];
		if (!empty($copy_login)) {
			echo '<div class="copy text-center">';
			echo $copy_login;
			echo '</div>';
		}

		// Social network logins
		if (!empty($networks)) {
			echo '<div class="networks">';
			echo '<h2>Sign In Instantly with Social Media</h2>';
			echo '<ul class="social-connect">';
			foreach ($networks as $id => $network) {
				echo '<li class="network-' . $id . '">';
				echo '<a href="javascript:var w = window.open(\'' . $network['connect'] . '\', \'socialconnect\', \'toolbar=0,status=0,scrollbars=1,width=600,height=450,left=\'+(screen.availWidth/2-225)+\',top=\'+(screen.availHeight/2-250)); w.focus();">';
				echo 'Login with ' . Format::htmlspecialchars($network['title']);
				echo '</a>';
				echo '</li>';
			}
			echo '</ul>';
			echo '</div>';
		}

		echo '</div>';
		echo '<div class="col width-1/2 width-1-sm">';

	} else {

		// Full page form
		echo '<div class="colset">';
		echo '<div class="col width-1">';

	}

?>
<form action="?login" method="post" id="lec2015_idx_tpl_login">

	<h2>Sign In to Your Account</h2>

	<div class="field x12">
		<label>Email Address</label>
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

	<p class="text-center">
		<span>Don't have an account?</span>
		<a href="<?=Settings::getInstance()->SETTINGS['URL_IDX_REGISTER']; ?>">Sign Up</a>
	</p>

</form>
<?php

	// Close colset
	echo '</div>';

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