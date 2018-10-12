<section class="section-preferences">
    <h3>My Preferences</h3>
    <form class="uk-form uk-form-stacked" id="form-preferences" method="post" <?= $current_form !== 'preferences' ? ' class="uk-hidden"' : ''; ?>" action="<?= Format::htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
    <input type="hidden" name="form" value="preferences">

    <fieldset class="uk-margin-bottom">
        <legend>Contact Information</legend>
        <div class="uk-form-row">
            <label class="uk-form-label" for="first_name">First Name</label>
            <div class="uk-form-controls">
                <input name="first_name" id="first_name" class="uk-width-1-1 uk-form-large" value="<?= Format::htmlspecialchars($preferences['first_name']); ?>">
            </div>
        </div>
        <div class="uk-form-row">
            <label class="uk-form-label" for="last_name">Last Name</label>
            <div class="uk-form-controls">
                <input name="last_name" id="last_name" class="uk-width-1-1 uk-form-large" value="<?= Format::htmlspecialchars($preferences['last_name']); ?>">
            </div>
        </div>
        <div class="uk-form-row">
            <label class="uk-form-label" for="email">Email Address *</label>
            <div class="uk-form-controls">
                <input type="email" name="email" id="email" class="uk-width-1-1 uk-form-large" value="<?= Format::htmlspecialchars($preferences['email']); ?>" required>
            </div>
        </div>
        <div class="uk-form-row">
            <label class="uk-form-label" for="email_alt">Alternate Email Address</label>
            <input class="uk-width-1-1 uk-form-large"  type="email" name="email_alt" value="<?=Format::htmlspecialchars($preferences['email_alt']); ?>">
            <label class="toggle toggle--stacked">
                <input type="checkbox" name="email_alt_cc_searches" value="saved_searches"<?=($preferences['email_alt_cc_searches'] === 'true' ? ' checked' : ''); ?>>
                <span class="toggle__label">Send CC of saved search updates to this email address.</span>
            </label>
        </div>
    </fieldset>

    <fieldset class="uk-margin-bottom">
        <legend>Phone Numbers</legend>
        <div class="uk-form-row">
            <?php $phone_required = !empty(Settings::getInstance()->SETTINGS['registration_phone']); ?>
            <label class="uk-form-label" for="phone">Primary Phone <?= $phone_required ? '*' : ''; ?></label>
            <div class="uk-form-controls">
                <input type="tel" name="phone" id="phone" class="uk-width-1-1 uk-form-large" value="<?= Format::htmlspecialchars($preferences['phone']); ?>" <?= $phone_required ? 'required' : ''; ?>>
            </div>
        </div>
        <div class="uk-form-row">
            <label class="uk-form-label" for="phone_cell">Secondary Phone</label>
            <div class="uk-form-controls">
                <input type="tel" name="phone_cell" id="phone_cell" class="uk-width-1-1 uk-form-large" value="<?= Format::htmlspecialchars($preferences['phone_cell']); ?>">
            </div>
        </div>
        <div class="uk-form-row">
            <label class="uk-form-label" for="phone_work">Work Phone</label>
            <div class="uk-form-controls">
                <input type="tel" name="phone_work" id="phone_work" class="uk-width-1-1 uk-form-large" value="<?= Format::htmlspecialchars($preferences['phone_work']); ?>">
            </div>
        </div>
        <div class="uk-form-row">
            <label class="uk-form-label" for="phone_fax">Fax Number</label>
            <div class="uk-form-controls">
                <input type="tel" name="phone_fax" id="phone_fax" class="uk-width-1-1 uk-form-large" value="<?= Format::htmlspecialchars($preferences['phone_fax']); ?>">
            </div>
        </div>
    </fieldset>

    <fieldset class="uk-margin-bottom">
        <legend>Mailing Address</legend>
        <div class="uk-form-row">
            <label class="uk-form-label" for="address1">Street Address</label>
            <div class="uk-form-controls">
                <input name="address1" id="address1" class="uk-width-1-1 uk-form-large" value="<?= Format::htmlspecialchars($preferences['address1']); ?>">
            </div>
        </div>
        <div class="uk-form-row">
            <label class="uk-form-label" for="city">City</label>
            <div class="uk-form-controls">
                <input name="city" id="city" class="uk-width-1-1 uk-form-large" value="<?= Format::htmlspecialchars($preferences['city']); ?>">
            </div>
        </div>
        <div class="uk-form-row">
            <label class="uk-form-label" for="city"><?= Locale::spell('State'); ?></label>
            <div class="uk-form-controls">
                <input name="state" id="state" class="uk-width-1-1 uk-form-large" value="<?= Format::htmlspecialchars($preferences['state']); ?>">
            </div>
        </div>
        <div class="uk-form-row">
            <label class="uk-form-label" for="country">Country</label>
            <div class="uk-form-controls">
                <input name="country" id="country" class="uk-width-1-1 uk-form-large" value="<?= Format::htmlspecialchars($preferences['country']); ?>">
            </div>
        </div>
    </fieldset>

    <fieldset class="uk-margin-bottom">
        <legend>Subscription Settings</legend>
        <div class="uk-width-1-1 uk-margin-bottom">
            <div class="uk-form-controls uk-form-controls-text uk-margin">
                <p class="uk-form-controls-condensed">
                    <label><input type="checkbox" value="in" name="opt_searches" <?= ($preferences['opt_searches'] != 'out' ? ' checked' : ''); ?>>
                        Yes, I would like to receive listing updates matching my saved search criteria.
                    </label>
                </p>
            </div>
        </div>
        <div class="uk-width-1-1 uk-margin-bottom">
            <div class="uk-form-controls uk-form-controls-text uk-margin">
                <p class="uk-form-controls-condensed">
                    <label><input type="checkbox" value="in" name="opt_marketing" <?= ($preferences['opt_marketing'] != 'out' ? ' checked' : ''); ?>>
                        <?= $opt_text['opt_marketing'] ?: 'Please send me updates concerning this website and the real estate market.'; ?>
                    </label>
                </p>
            </div>
        </div>
        <?php if (Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO']) { ?>
            <div class="uk-width-1-1 uk-margin-bottom">
                <div class="uk-form-controls uk-form-controls-text uk-margin">
                    <p class="uk-form-controls-condensed">
                        <label><input type="checkbox" value="in" name="opt_texts" <?= ($preferences['opt_texts'] == 'in' ? ' checked' : ''); ?>>
                            <?= $opt_text['opt_texts'] ?: 'I consent to receiving text messages from this site.'; ?>
                        </label>
                    </p>
                </div>
            </div>
        <?php } ?>
    </fieldset>

    <div class="uk-form-row">
        <fieldset>
            <button type="submit" class="uk-button uk-button-medium">Update Preferences</button>
            <?php if (!empty(Settings::getInstance()->SETTINGS['registration_password'])) { ?>
                <button type="button" class="uk-button uk-button-medium" data-uk-toggle="{target:'.form-password'}">
                    Change My Password
                </button>
            <?php } ?>
        </fieldset>
    </div>
    </form>

    <?php if (!empty(Settings::getInstance()->SETTINGS['registration_password'])) { ?>
        <form class="uk-form uk-hidden form-password" method="post" <?= $current_form !== 'password' ? ' class="uk-hidden"' : ''; ?>" action="<?= Format::htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
            <input type="hidden" name="form" value="password">
            <fieldset class="uk-margin-bottom">
                <legend>Change My Password</legend>
                <?php if (!empty($preferences['password'])) { ?>
                <div class="uk-form-row">
                    <label class="uk-form-label" for="current_password">Your Current
                        Password *</label>
                    <div class="uk-form-controls">
                        <input type="password" name="current_password" id="current_password" class="uk-width-1-1 uk-form-large" value="" required>
                    </div>
                </div>
                <?php } ?>
                <div class="uk-form-row">
                    <label class="uk-form-label" for="new_password">Your New Password *</label>
                    <div class="uk-form-controls">
                        <input type="password" name="new_password" id="new_password" class="uk-width-1-1 uk-form-large" value="" required>
                    </div>
                </div>
                <div class="uk-form-row">
                    <label class="uk-form-label" for="confirm_password">Repeat New Password
                        *</label>
                    <div class="uk-form-controls">
                        <input type="password" name="confirm_password" id="confirm_password" class="uk-width-1-1 uk-form-large" value="" required>
                    </div>
                </div>
            </fieldset>
            <div class="uk-form-row">
                <button type="submit" class="uk-button uk-button-medium uk-margin-top">
                    Update Password
                </button>
                <button type="button" class="uk-button uk-button-medium uk-margin-top" data-uk-toggle="{target:'.form-password'}">
                    Back to Preferences
                </button>
            </div>
        </form>
    <?php } ?>
</section>
