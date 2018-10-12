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

?>
	<?=!empty(Settings::getInstance()->SETTINGS['copy_register']) ? '<div class="copy hidden-phone">' . Settings::getInstance()->SETTINGS['copy_register'] . '</div>' : ''; ?>

	<div class="grid_12">
		<div<?=!empty($networks) ? ' class="x8"' : ' class="x12"'; ?>>
			<form action="?register" method="post" id="bcse_tpl_register">
				<h3>Get Started&hellip;</h3>

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
					<input placeholder="Phone <?php if (empty(Settings::getInstance()->SETTINGS['registration_phone'])) echo '(optional)'; ?>" type="tel" name="phone" value="<?=htmlspecialchars($phone); ?>"<?=!empty(Settings::getInstance()->SETTINGS['registration_phone']) ? ' required' : ''; ?>>
				</div>
				<?php if (!empty(Settings::getInstance()->SETTINGS['registration_password'])) { ?>
					<div class="field x6">
						<input type="password" name="password" placeholder="Password" required>
					</div>
					<div class="field x6 last">
						<input type="password" name="confirm_password" placeholder="Confirm Password" required>
					</div>
				<?php } ?>
				<div class="field x12">
					<label>Preferred Contact Method</label>
					<label class="boolean"><input type="radio" name="contact_method" value="email"<?=($contact_method === 'email' ? ' checked' : ''); ?>> Email</label>
					<?php if (Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO']) { ?>
						<label class="boolean"><input type="radio" name="contact_method" value="text"<?=($contact_method === 'text' ? ' checked' : ''); ?>> Text</label>
					<?php } ?>
					<label class="boolean"><input type="radio" name="contact_method" value="phone"<?=($contact_method === 'phone' ? ' checked' : ''); ?>> Phone</label>
				</div>
				<div class="field x12">
					<label class="toggle"><input type="checkbox" name="opt_marketing" value="in"<?=$opt_marketing == 'in' ? ' checked' : ''; ?>><?=(!empty($anti_spam['consent_text']) ? $anti_spam['consent_text'] : 'Please send me updates concerning this website and the real estate market.'); ?></label>
					<?php

						// Opt-in to text messages
						if (Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO']) {
							echo '<div class="field x12">';
							echo '<label class="toggle">';
							echo '<input type="checkbox" name="opt_texts" value="in"' . ($opt_texts === 'in' ? ' checked' : '') . '>';
							echo $anti_spam_sms['consent_text'] ?: 'I consent to receiving text messages from this site.';
							echo '</label>';
							echo '</div>';
						}

						// Compliance Requires Agreement
						if (!empty($_COMPLIANCE['register']['agree']) && is_array($_COMPLIANCE['register']['agree'])) {
							$agree = $_COMPLIANCE['register']['agree'];
							echo '<label class="toggle"><input type="checkbox" name="agree" value="true"' . (!empty($_POST['agree']) ? ' checked' : '') . '> I agree to the <a href="' . $agree['link'] . '" target="_blank">' . $agree['title'] . '</a>.</label>';
						}

					?>
				</div>
				<div class="btnset">
					<button class="strong" type="submit">Register</button>
				</div>
			</form>
		</div>
		<?php if (!empty($networks)) { ?>
			<div class="x4 last">
				<div class="networks">
					<h3>Or &hellip;</h3>
					<ul>
					<?php foreach ($networks as $id => $network) { ?>
						<li>
							<a class="network-login <?=$id; ?>" href="javascript:var w = window.open('<?=$network['connect']; ?>', 'socialconnect', 'toolbar=0,status=0,scrollbars=1,width=600,height=450,left='+(screen.availWidth/2-225)+',top='+(screen.availHeight/2-250)); w.focus();">Login with <?=$network['title']; ?></a>
						</li>
					<?php } ?>
					</ul>
				</div>
			</div>
		<?php } ?>
	</div>

	<h3 class="login-register">
		<span>Already have an account? </span>
		<a class="buttonstyle mini" href="<?=Settings::getInstance()->SETTINGS['URL_IDX_LOGIN']; ?>">Sign In Here</a>
	</h3>
<?php

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