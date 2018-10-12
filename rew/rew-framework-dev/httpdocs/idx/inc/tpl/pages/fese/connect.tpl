<?php

// Success notices
if (!empty($success)) {
    echo sprintf('<div class="msg msg--pos marB-md">%s</div>',
        implode('<br>', $success)
    );
}

// Error notices
if (!empty($errors)) {
    echo sprintf('<div class="msg msg--neg marB-md">%s</div>',
        implode('<br>', $errors)
    );
}

if (!empty($show_form)) {

?>
<div class="cols">
    <div class="col w1/3 w1/1-sm">
        <?php

            // Profile Display
            echo '<div class="module articleset">';
            echo '<article>';
            echo '<header>';
            if (!empty($profile['link'])) {
                echo '<h4><a href="' . $profile['link'] . '" target="_blank">' . $profile['first_name'] . ' ' . $profile['last_name'] . '</a></h4>';
            } else {
                echo '<h4>' . $profile['first_name'] . ' ' . $profile['last_name'] . '</h4>';
            }
            echo '</header>';
            if (!empty($profile['image'])) {
                echo '<div class="body">';
                echo '<div class="photo">';
                if (!empty($profile['link'])) {
                    echo '<a href="' . $profile['link'] . '" target="_blank"><img src="' . $profile['image'] . '" alt=""></a>';
                } else {
                    echo '<span><img src="' . $profile['image'] . '" alt=""></span>';
                }
                echo '</div>';
                echo '</div>';
            }
            echo '</article>';
            echo '</div>';

            // Social Connect Message
            if (!empty(Settings::getInstance()->SETTINGS['copy_connect'])) {
                echo '<div class="copy">' . Settings::getInstance()->SETTINGS['copy_connect'] . '</div>';
            }

        ?>
    </div>
    <div class="col w2/3 w1/1-sm">
        <form action="?submit" method="post">
            <h3>Complete Registration&hellip;</h3>
            <div class="fld col w1/2">
                <label>First Name</label>
                <input name="first_name" value="<?=htmlspecialchars($first_name); ?>" required>
            </div>
            <div class="fld col w1/2">
                <label>Last Name</label>
                <input name="last_name" value="<?=htmlspecialchars($last_name); ?>" required>
            </div>
            <div class="fld col w1/1">
                <label>Email Address</label>
                <input type="email" name="email" value="<?=htmlspecialchars($email); ?>" required>
            </div>
            <div class="fld col w1/1">
                <label>Phone <?php if (empty(Settings::getInstance()->SETTINGS['registration_phone'])) echo '<small>(optional)</small>'; ?></label>
                <input type="tel" name="phone" value="<?=htmlspecialchars($phone); ?>"<?=!empty(Settings::getInstance()->SETTINGS['registration_phone']) ? ' required' : ''; ?>>
            </div>
            <div class="fld col w1/1">
                <label class="toggle"><input type="checkbox" name="opt_marketing" value="in"<?=$opt_marketing == 'in' ? ' checked' : ''; ?>>
                    <?=(!empty($anti_spam['consent_text']) ? $anti_spam['consent_text'] : 'Please send me updates concerning this website and the real estate market.'); ?>
                </label>
                <?php if(!empty(Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO'])){ ?>
                    <label class="toggle"><input type="checkbox" name="opt_texts" value="in"<?=$opt_texts == 'in' ? ' checked' : ''; ?>>
                        <?=(!empty($anti_spam_sms['consent_text']) ? $anti_spam_sms['consent_text'] : 'Please send me updates via text message concerning this website and the real estate market.'); ?>
                    </label>
                <?php } ?>
                <?php

                    // Compliance Requires Agreement
                    if (!empty($_COMPLIANCE['register']['agree']) && is_array($_COMPLIANCE['register']['agree'])) {
                        $agree = $_COMPLIANCE['register']['agree'];
                        echo '<label class="toggle"><input type="checkbox" name="agree" value="true"' . (!empty($_POST['agree']) ? ' checked' : '') . '> I agree to the <a href="' . $agree['link'] . '" target="_blank">' . $agree['title'] . '</a>.</label>';
                    }

                ?>
            </div>
            <div class="btnset">
                <button class="strong" type="submit">Continue</button>
            </div>
        </form>
    </div>
</div>
<?php

} else {

    // Conversion Tracking
    $ppc = Util_CMS::getPPCSettings();
    if (!empty($ppc) && $ppc['enabled'] === 'true' && $is_rt && !empty($ppc['rt-register'])) echo $ppc['rt-register'];
    if (!empty($ppc) && $ppc['enabled'] === 'true' && !$is_rt &&!empty($ppc['idx-register'])) echo $ppc['idx-register'];

    // Trigger Save Listing
    if (!empty($_SESSION['bookmarkListing'])) {
        $page->writeJS("window.parent.IDX.Favorite({'mls':'" . $_SESSION['bookmarkListing'] . "','force':true, 'feed':'" . $_SESSION['bookmarkFeed'] . "'});");
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
        }, 0);");

    }

}