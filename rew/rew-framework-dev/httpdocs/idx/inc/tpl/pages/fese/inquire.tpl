<?php

// Listing must exist
if (!empty($listing)) {

    // Require page specific javascript
    $page->addJavascript($_SERVER['DOCUMENT_ROOT'] . '/inc/js/idx/inquire.js', 'page', false);

    // Check Showing Suite Module Output
    if (!empty($showing_suite_display)) {
        echo $showing_suite_display;

    // Otherwise Default to REW Form
    } else if (!empty($show_form)) { ?>

        <?php if (!empty($errors)) { ?>
            <div class="msg negative"><p>One of more errors occurred while processing your request.</p></div>
        <?php } ?>

        <form action="?submit" method="post" id="fese_tpl_inquire">

            <?php require Settings::getInstance()->URLS['HONEYPOT']; ?>

            <?php
                // Assign to Agent
                if (!empty($agent)) echo '<input type="hidden" name="agent" value="' . $agent->getId() . '">';

                // Assign to Lender
                if (!empty($lender)) echo '<input type="hidden" name="lender" value="' . $lender->getId() . '">';
            ?>

            <?php if (!empty($inquiry_types)) { ?>
                <div class="fld col w1/1">
                    <label>Subject</label>
                    <select name="inquire_type">
                        <?php

                            // Inquiry types
                            foreach ($inquiry_types as $value => $inquiry_type) {
                                echo '<option '
                                    . ' value="' . Format::htmlspecialchars($value) . '"'
                                    . ' data-message=' . ($inquiry_type['message'] === false ? 'false' : '"' . Format::htmlspecialchars($inquiry_type['message']) . '"')
                                    . ($_REQUEST['inquire_type'] === $value ? ' selected' : '')
                                . '>' . Format::htmlspecialchars($inquiry_type['subject']) . '</option>' . PHP_EOL;
                            }

                        ?>
                    </select>
                </div>
            <?php } ?>

            <div class="fld col w1/1">
                <label>Message</label>
                <textarea cols="32" rows="4" name="comments" required><?=html_entity_decode(htmlspecialchars($_REQUEST['comments']), ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>

            <?php if (Settings::getInstance()->LANG == 'en-CA' && $user->info('opt_marketing') != 'in') { ?>
                <div class="fld col w1/1">
                    <label class="toggle"><input type="checkbox" name="opt_marketing" value="in"<?=$anti_spam['optin'] == 'in' ? ' checked' : ''; ?>> <?=(!empty($anti_spam['consent_text']) ? $anti_spam['consent_text'] : 'Please send me updates concerning this website and the real estate market.'); ?></label>
                </div>
            <?php } ?>

            <hr>

            <div class="cols">

                <div class="fld col w1/2">
                    <input placeholder="First Name" name="onc5khko" value="<?=htmlspecialchars($_POST['onc5khko']); ?>" required>
                    <?=!empty($errors['first_name']) ? '<small class="negative">' . $errors['first_name'] . '</small>' : ''; ?>
                </div>

                <div class="fld col w1/2">
                    <input placeholder="Last Name" name="sk5tyelo" value="<?=htmlspecialchars($_POST['sk5tyelo']); ?>" required>
                    <?=!empty($errors['last_name']) ? '<small class="negative">' . $errors['last_name'] . '</small>' : ''; ?>
                </div>

                <div class="fld col w1/2">
                    <input placeholder="Email" type="email" name="mi0moecs" value="<?=htmlspecialchars($_POST['mi0moecs']); ?>" required>
                    <?=!empty($errors['email']) ? '<small class="negative">' . $errors['email'] . '</small>' : '<small>Please provide a valid email address.</small>'; ?>
                </div>

                <div class="fld col w1/2">
                    <input placeholder="Phone <?=empty(Settings::getInstance()->SETTINGS['registration_phone']) ? '(optional)' : ''; ?>" type="tel" name="phone" value="<?=htmlspecialchars($_POST['phone']); ?>"<?=!empty(Settings::getInstance()->SETTINGS['registration_phone']) ? ' required' : ''; ?>>
                    <?=!empty($errors['phone']) ? '<small class="negative">' . $errors['phone'] . '</small>' : ''; ?>
                </div>

            </div>

            <div class="btns">
                <button type="submit" class="btn btn--primary"><?=($_REQUEST['inquire_type'] == 'Property Showing') ? 'Send Request' : 'Send Inquiry'; ?></button>
            </div>

        </form>

    <?php } else { ?>

        <div class="msg positive"><p>Your listing inquiry has been successfully sent.</p></div>

        <?php

            // Conversion Tracking
            $ppc = Util_CMS::getPPCSettings();
            if (!empty($ppc) && $ppc['enabled'] === 'true') {
                // Showing Request
                if ($_POST['inquire_type'] == 'Property Showing' && !empty($ppc['idx-showing'])) {
                    echo $ppc['idx-showing'];

                // Property Inquiry
                } else if (!empty($ppc['idx-inquire'])) {
                    echo $ppc['idx-inquire'];

                }
            }

        ?>

    <?php } ?>

<?php } else { ?>

    <div class="msg negative">
        <h1 class="title">Listing Not Found</h1>
        <p>The selected listing could not be found. This is probably because the listing has been sold, removed from the market, or simply moved.</p>
    </div>

<?php } ?>