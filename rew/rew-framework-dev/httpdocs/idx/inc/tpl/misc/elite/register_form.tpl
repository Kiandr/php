<form class="uk-form" action="?<?= $_GET['load_page'] == 'connect' ? 'submit' : 'register'; ?>" method="post">

    <?php require Settings::getInstance()->URLS['HONEYPOT']; ?>

    <fieldset>
        <legend>Enter your details</legend>
        <div class="uk-grid">
            <div class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 uk-margin-bottom">
                <input class="uk-width-1-1 uk-form-large" placeholder="First Name" name="onc5khko" value="<?= Format::htmlspecialchars($first_name); ?>" required>
            </div>
            <div class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 uk-margin-bottom">
                <input class="uk-width-1-1 uk-form-large" placeholder="Last Name" name="sk5tyelo" value="<?= Format::htmlspecialchars($last_name); ?>" required>
            </div>
            <div class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 uk-margin-bottom">
                <input class="uk-width-1-1 uk-form-large" placeholder="Email (This will also be your sign in name)" type="email" name="mi0moecs" value="<?= Format::htmlspecialchars($email); ?>" required>
            </div>
            <div class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 uk-margin-bottom">
                <?php $phone_required = !empty(Settings::getInstance()->SETTINGS['registration_phone']); ?>
                <input class="uk-width-1-1 uk-form-large" placeholder="Phone <?= $phone_required ? '(required)' : '(optional)'; ?>" type="tel" name="phone" value="<?= Format::htmlspecialchars($phone); ?>"<?= $phone_required ? ' required' : ''; ?>>
            </div>
        </div>
    </fieldset>

    <?php if (!empty(Settings::getInstance()->SETTINGS['registration_password'])) { ?>
    <fieldset>
        <legend>Set your Password</legend>
        <div class="uk-grid">
            <div class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 uk-margin-bottom">
                <input class="uk-width-1-1 uk-form-large" type="password" name="password" placeholder="Password" required>
            </div>
            <div class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 uk-margin-bottom">
                <input class="uk-width-1-1 uk-form-large" type="password" name="confirm_password" placeholder="Confirm Password" required>
            </div>
        </div>
    </fieldset>
    <?php } ?>

    <fieldset>
        <legend>Preferred Contact Method</legend>
        <div class="uk-form-controls uk-form-controls-text">
            <p class="uk-form-controls-condensed">
                <input type="radio" name="contact_method" id="contact_method-email" value="email"<?=($contact_method === 'email' ? ' checked' : ''); ?>>
                <label for="contact_method-email" class="uk-margin-right">Email</label>
                <?php if (Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO']) { ?>
                <input type="radio" name="contact_method" id="contact_method-text" value="text"<?=($contact_method === 'text' ? ' checked' : ''); ?>> <label for="contact_method-text" class="uk-margin-right">Text</label>
                <?php } ?>
                <input type="radio" name="contact_method" id="contact_method-phone" value="phone"<?=($contact_method === 'phone' ? ' checked' : ''); ?>>
                <label for="contact_method-phone">Phone</label>
            </p>
        </div>
    </fieldset>

    <div class="uk-form-controls uk-form-controls-text uk-margin">
        <p class="uk-form-controls-condensed">
            <input type="checkbox" name="opt_marketing" id="opt_marketing" value="in" <?=$opt_marketing == 'in' ? 'checked' : ''; ?>>
            <label for="opt_marketing"><?=(!empty($anti_spam['consent_text']) ? $anti_spam['consent_text'] : 'Please send me updates concerning this website and the real estate market.'); ?></label>
        </p>
    </div>

    <?php if (Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO']) { ?>
        <div class="uk-form-controls uk-form-controls-text uk-margin">
            <p class="uk-form-controls-condensed">
                <input type="checkbox" name="opt_texts" id="opt_texts" value="in" <?=($opt_texts === 'in' ? ' checked' : ''); ?>>
                <label for="opt_texts"><?= $anti_spam_sms['consent_text'] ?: 'I consent to receiving text messages from this site.' ?></label>
            </p>
        </div>
        <?php if (!empty($_COMPLIANCE['register']['agree']) && is_array($_COMPLIANCE['register']['agree'])) { ?>
            <div class="uk-form-controls uk-form-controls-text uk-margin">
                <p class="uk-form-controls-condensed">
                    <input type="checkbox" name="agree" id="agree" value="true" <?=(!empty($_POST['agree']) ? ' checked' : ''); ?>>
                    <label for="agree">I agree to the <a href="<?= Format::htmlspecialchars($agree['link']); ?>" target="_blank"><?= Format::htmlspecialchars($_COMPLIANCE['register']['agree']['title']); ?></a>.</label>
                </p>
            </div>
        <?php } ?>
    <?php } ?>

    <div class="uk-form-row">
        <button type="submit" class="uk-button uk-button-medium"><?= $_GET['load_page'] == 'connect' ? 'Continue' : 'Register'; ?></button>
    </div>
</form>
