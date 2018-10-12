<?php

// Success messages
if (!empty($success)) {
	echo '<div class="msg positive"><p>' . implode('</p><p>', $success) . '</p></div>';
}

// Error messages
if (!empty($errors)) {
	echo '<div class="msg negative"><p>' . implode('</p><p>', $errors) . '</p></div>';
}

// Show register form
if (!empty($show_form)) {

	// Require page specific javascript
	$page->addJavascript($_SERVER['DOCUMENT_ROOT'] . '/inc/js/idx/register.js', 'page', false);

	// Registartion form text
	$copy_register = Settings::getInstance()->SETTINGS['copy_register'];

	// Split form into two columns
	$colset = (!empty($copy_register) || !empty($networks));
	if (!empty($colset)) {
		echo '<div class="colset">';
		echo '<div class="col width-1/2 width-1-sm">';

		// Registration form text
		if (!empty($copy_register)) {
			echo '<div class="copy text-center hidden-phone">';
			echo $copy_register;
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
<form action="?register" method="post" id="lec2015_idx_tpl_register">

	<h2 class="text-center">Create Your Free Account&hellip;</h2>

    <?php require Settings::getInstance()->URLS['HONEYPOT']; ?>

	<div class="field x6">
		<input placeholder="First Name" name="onc5khko" value="<?=htmlspecialchars($first_name); ?>" required>
	</div>

	<div class="field x6 last">
		<input placeholder="Last Name" name="sk5tyelo" value="<?=htmlspecialchars($last_name); ?>" required>
	</div>

	<div class="field x12">
		<input placeholder="Email (This will also be your sign in name)" type="email" name="mi0moecs" value="<?=htmlspecialchars($email); ?>" required>
	</div>

	<div class="field x12">
		<input placeholder="Phone" type="tel" name="phone" value="<?=htmlspecialchars($phone); ?>"<?=!empty(Settings::getInstance()->SETTINGS['registration_phone']) ? ' required' : ''; ?>>
	</div>

	<div class="field x12">
		<label>Preferred Contact Method</label>
		<label class="boolean"><input type="radio" name="contact_method" value="email"<?=($contact_method === 'email' ? ' checked' : ''); ?>> Email</label>
		<?php if (Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO']) { ?>
			<label class="boolean"><input type="radio" name="contact_method" value="text"<?=($contact_method === 'text' ? ' checked' : ''); ?>> Text</label>
		<?php } ?>
		<label class="boolean"><input type="radio" name="contact_method" value="phone"<?=($contact_method === 'phone' ? ' checked' : ''); ?>> Phone</label>
	</div>

	<?php if (!empty(Settings::getInstance()->SETTINGS['registration_password'])) { ?>
		<div class="field x6">
			<input type="password" name="password" placeholder="Password" required>
		</div>
		<div class="field x6 last">
			<input type="password" name="confirm_password" placeholder="Confirm Password" required>
		</div>
	<?php } ?>
	<div class="field x12 checkbox">
		<label class="toggle"><input type="checkbox" name="opt_marketing" value="in"<?=$opt_marketing == 'in' ? ' checked' : ''; ?>>
		<?=(!empty($anti_spam['consent_text']) ? $anti_spam['consent_text'] : 'Please send me updates concerning this website and the real estate market.'); ?>
		</label>
	</div>
	<?php

		// Opt-in to text messages
		if (Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO']) {
			echo '<div class="field x12 checkbox">';
			echo '<label class="toggle">';
			echo '<input type="checkbox" name="opt_texts" value="in"' . ($opt_texts === 'in' ? ' checked' : '') . '>';
			echo $anti_spam_sms['consent_text'] ?: 'I consent to receiving text messages from this site.';
			echo '</label>';
			echo '</div>';
		}

		// Compliance Requires Agreement
		if (!empty($_COMPLIANCE['register']['agree']) && is_array($_COMPLIANCE['register']['agree'])) {
			$agree = $_COMPLIANCE['register']['agree'];
			echo '<div class="field x12 checkbox">';
			echo '<label class="toggle"><input type="checkbox" name="agree" value="true"' . (!empty($_POST['agree']) ? ' checked' : '') . '> I agree to the <a href="' . $agree['link'] . '" target="_blank">' . $agree['title'] . '</a>.</label>';
			echo '</div>';
		}

	?>
	<div class="btnset">
		<button class="strong" type="submit">Create Your Free Account</button>
	</div>

	<p class="text-center">
		<span>Already have an account?</span>
		<a href="<?=Settings::getInstance()->SETTINGS['URL_IDX_LOGIN']; ?>">Sign In</a>
	</p>

</form>
<?php

	// Close colset
	echo '</div>';

} else {

	// Conversion Tracking
	if (!empty($ppc) && $ppc['enabled'] === 'true' && !empty($ppc['idx-register'])) echo $ppc['idx-register'];

	// Require Verification
	if ((!empty(Settings::getInstance()->SETTINGS['registration_verify']) && !Validate::verifyWhitelisted($user->info('email'))) || Validate::verifyRequired($user->info('email'))) {
		echo '<div class="msg positive">';
		echo '<p>Thank you! A verification code has been sent to you at <strong>' . htmlspecialchars($user->info('email')) . '</strong>.</p>';
		echo '<p>Just one more step is needed to finish your registration. Please verify that we have your correct email address by clicking on the verification link we sent to you. If you don\'t receive instructions within a minute or two, check your email\'s spam and junk filters, or <a href="' . sprintf(Settings::getInstance()->SETTINGS['URL_IDX_VERIFY'], '') . '"</a>try resending your code</a>.</p>';
		echo '</div>';
		return;
	}

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