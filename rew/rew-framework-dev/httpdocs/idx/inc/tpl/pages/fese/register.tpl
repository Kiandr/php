<?php

// Success notices
if (!empty($success)) {
    echo sprintf('<div class="msg msg--pos"><p>%s</p></div>',
        implode('</p><p>', $success)
    );
}

// Error notices
if (!empty($errors)) {
    echo sprintf('<div class="msg msg--neg">%s</div>',
        implode('<br>', $errors)
    );
}

// Show register form
if (!empty($show_form)) {

    // Require page specific javascript
    $page->addJavascript($_SERVER['DOCUMENT_ROOT'] . '/inc/js/idx/register.js', 'page', false);

    // Display registration copy from backend settings
    if ($copy_register = Settings::getInstance()->SETTINGS['copy_register']) {
        echo sprintf('<div class="copy hidden-phone">%s</div>', $copy_register);
    }

?>
<div class="wrp">

    <?php if (!empty($networks)) { ?>
        <div class="login-with"><span>Log In With</span></div>
        <?php include $this->locateTemplate('idx', 'misc', 'social-connect'); ?>
        <div class="or"><span>OR</span></div>
    <?php } ?>

    <div>
        <form action="?register" method="post" id="fese_idx_tpl_register">

            <?php require Settings::getInstance()->URLS['HONEYPOT']; ?>

            <div class="cols">
                <div class="fld col w1/2">
                    <label>First Name</label>
                    <input placeholder="First Name" name="onc5khko" value="<?=htmlspecialchars($first_name); ?>" required>
                </div>
                <div class="fld col w1/2">
                    <label>Last Name</label>
                    <input placeholder="Last Name" name="sk5tyelo" value="<?=htmlspecialchars($last_name); ?>" required>
                </div>
            </div>


            <div class="fld w1/1">
                <label>Email</label>
                <input placeholder="Email (This will also be your sign in name)" type="email" name="mi0moecs" value="<?=htmlspecialchars($email); ?>" required>
            </div>

            <div class="fld w1/1">
                <label>Phone</label>
                <input placeholder="Phone <?php if (empty(Settings::getInstance()->SETTINGS['registration_phone'])) echo '(optional)'; ?>" type="tel" name="phone" value="<?=htmlspecialchars($phone); ?>"<?=!empty(Settings::getInstance()->SETTINGS['registration_phone']) ? ' required' : ''; ?>>
            </div>


            <?php if (!empty(Settings::getInstance()->SETTINGS['registration_password'])) { ?>

            <div class="cols">

                <div class="fld col w1/2">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Password" required>
                </div>

                <div class="fld col w1/2">
                    <label>Password (Confirm)</label>
                    <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                </div>

            </div>

            <?php } ?>
            <div class="fld w1/1 fbox">
                <label>Preferred Contact Method</label>
                <label class="boolean"><input type="radio" name="contact_method" value="email"<?=($contact_method === 'email' ? ' checked' : ''); ?>> Email</label>
                <?php if (Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO']) { ?>
                    <label class="boolean"><input type="radio" name="contact_method" value="text"<?=($contact_method === 'text' ? ' checked' : ''); ?>> Text</label>
                <?php } ?>
                <label class="boolean"><input type="radio" name="contact_method" value="phone"<?=($contact_method === 'phone' ? ' checked' : ''); ?>> Phone</label>
            </div>
            <div class="fld w1/1">
                <label class="toggle"><input type="checkbox" name="opt_marketing" value="in"<?=$opt_marketing == 'in' ? ' checked' : ''; ?>><?=(!empty($anti_spam['consent_text']) ? $anti_spam['consent_text'] : 'Please send me updates concerning this website and the real estate market.'); ?></label>
                <?php

                    // Opt-in to text messages
                    if (Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO']) {
                        echo '<div class="fld w1/1">';
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

            <p class="btns padT" style="overflow: hidden;">
                <button class="btn btn--primary L" type="submit">Register</button>
                <a class="R" href="<?=Settings::getInstance()->SETTINGS['URL_IDX_LOGIN']; ?>">Already have an Account?</a>
            </p>

        </form>
    </div>
</div>
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
