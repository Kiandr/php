<?php

// Success notices
if (!empty($success)) {
    echo sprintf('<div class="notice notice--positive -mar-bottom"><div class="notice__message">%s</div></div>',
        implode('</p><p>', $success)
    );
}

// Error notices
if (!empty($errors)) {
    echo sprintf('<div class="notice notice--negative -mar-bottom"><div class="notice__message">%s</div></div>',
        implode('<br>', $errors)
    );
}

// Show register form
if (!empty($show_form)) {

    // Require page specific javascript
    $page->addJavascript($_SERVER['DOCUMENT_ROOT'] . '/inc/js/idx/register.js', 'page', false);

?>
<div class="login-page container -sm">

	<h1>Create An Account</h1>

    <?php
        // Display registration copy from backend settings
        if ($copy_register = Settings::getInstance()->SETTINGS['copy_register']) {
            echo sprintf('<div class="copy hidden-phone">%s</div>', $copy_register);
        }
    ?>

    <div>
        <form action="?register" method="post" id='vision_idx_register_tpl'>
            <?php require Settings::getInstance()->URLS['HONEYPOT']; ?>

			<div class="field column -width-1/2 -width-1/1@sm -width-1/1@xs">
				<label id="register_fname_label" for="register_fname_input" class="field__label">First Name</label>
				<input id="register_fname_input" aria-labelledby="register_fname_label" name="onc5khko" value="<?=htmlspecialchars($first_name); ?>" required>
			</div>
			<div class="field column -width-1/2 -width-1/1@sm -width-1/1@xs">
				<label id="register_lname_label" for="register_lname_input" class="field__label">Last Name</label>
				<input id="register_lname_input" aria-labelledby="register_lname_label" name="sk5tyelo" value="<?=htmlspecialchars($last_name); ?>" required>
			</div>
			
            <div class="field column -width-1/1 -mar-bottom-0 -pad-bottom-0">
                <label id="register_email_label" for="register_email_input" class="field__label">Email <small>(This will also be your sign in name)</small></label>
                <input id="register_email_input" aria-labelledby="register_email_label" aria-describedby="register_email_format" type="email" name="mi0moecs" value="<?=htmlspecialchars($email); ?>" required>
                <small id="register_email_format" class="field__tip -pad-bottom-0">Please enter a valid email address.</small>
            </div>

            <div class="field column -width-1/1 -mar-top-0">
                <label id="register_phone_label" for="register_phone_input" class="field__label">
                    Phone
                    <?php if (empty(Settings::getInstance()->SETTINGS['registration_phone'])) { ?>
                        <small>(optional)</small>
                    <?php } ?>
                </label>
                <input id="register_phone_input" aria-labelledby="register_phone_label" type="tel" name="phone" value="<?=htmlspecialchars($phone); ?>"<?=!empty(Settings::getInstance()->SETTINGS['registration_phone']) ? ' required' : ''; ?>>
            </div>

            <?php if (!empty(Settings::getInstance()->SETTINGS['registration_password'])) { ?>

                <div class="field column -width-1/2 -width-1/1@sm -width-1/1@xs">
                    <label id="register_password_label" for="register_password_input" class="field__label">Password</label>
                    <input id="register_password_input" aria-labelledby="register_password_label" type="password" name="password" required>
                </div>

                <div class="field column -width-1/2 -width-1/1@sm -width-1/1@xs">
                    <label id="register_confirm_label" for="register_confirm_input" class="field__label">Password (Confirm)</label>
                    <input id="register_confirm_input" aria-labelledby="register_confirm_label"  type="password" name="confirm_password" required>
                </div>

            <?php } ?>

            <div class="field column -width-1/1 fbox">
                <label class="pref-contact">Preferred Contact Method</label>
                <label class="boolean"><input type="radio" name="contact_method" value="email"<?=($contact_method === 'email' ? ' checked' : ''); ?>> Email</label>
                <?php if (Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO']) { ?>
                    <label class="boolean"><input type="radio" name="contact_method" value="text"<?=($contact_method === 'text' ? ' checked' : ''); ?>> Text</label>
                <?php } ?>
                <label class="boolean"><input type="radio" name="contact_method" value="phone"<?=($contact_method === 'phone' ? ' checked' : ''); ?>> Phone</label>
            </div>

            <div class="field column -width-1/1">
                <label class="toggle"><input type="checkbox" name="opt_marketing" value="in"<?=$opt_marketing == 'in' ? ' checked' : ''; ?>><?=(!empty($anti_spam['consent_text']) ? $anti_spam['consent_text'] : 'Please send me updates concerning this website and the real estate market.'); ?></label>
                <?php

                    // Opt-in to text messages
                    if (Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO']) {
                        echo '<label class="toggle -mar-top-sm">';
                        echo '<input type="checkbox" name="opt_texts" value="in"' . ($opt_texts === 'in' ? ' checked' : '') . '>';
                        echo $anti_spam_sms['consent_text'] ?: 'I consent to receiving text messages from this site.';
                        echo '</label>';
                    }

                    // Compliance Requires Agreement
                    if (!empty($_COMPLIANCE['register']['agree']) && is_array($_COMPLIANCE['register']['agree'])) {
                        $agree = $_COMPLIANCE['register']['agree'];
                        echo '<label class="toggle -mar-top-sm"><input type="checkbox" name="agree" value="true"' . (!empty($_POST['agree']) ? ' checked' : '') . '> I agree to the <a href="' . $agree['link'] . '" target="_blank">' . $agree['title'] . '</a>.</label>';
                    }

                ?>
            </div>

            <div class="buttons columns -pad-vertical-md">
            	<button class="button button--pill button--strong column -width-1/2 -width-1/1@sm" type="submit">Register</button>
                <a class="button button--ghost column -width-1/2 -width-1/1@sm" href="<?=Settings::getInstance()->SETTINGS['URL_IDX_LOGIN']; ?>">Already have an Account?</a>
            </div>

        </form>
    </div>

	<?php if (!empty($networks)) { ?>
        <div class="or"><span>OR</span></div>
		<span class="social-login--text">Create an account with</span>
        <?php include $this->locateTemplate('idx', 'misc', 'social-connect'); ?>
    <?php } ?>

</div>
<?php

} else {

    // Conversion Tracking
    if (!empty($ppc) && $ppc['enabled'] === 'true' && !empty($ppc['idx-register'])) echo $ppc['idx-register'];

    // Require Verification
    if ((!empty(Settings::getInstance()->SETTINGS['registration_verify']) && !Validate::verifyWhitelisted($user->info('email'))) || Validate::verifyRequired($user->info('email'))) {
        echo '<div class="notice notice--positive">';
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
        $url_redirect = !empty($url_redirect) ? $url_redirect : '/';

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
