<?php

/**
 * @var string $feed
 * @var array $system
 * @var array $cities
 * @var \REW\Core\Interfaces\SkinInterface $skin
 * @var \REW\Core\Interfaces\Page\BackendInterface $page
 * @var \REW\Core\Interfaces\SettingsInterface $settings
 */

?>
<form action="?submit" method="post" class="rew_check">
    <input type="hidden" name="feed" value="<?=$feed; ?>">

    <div class="bar">
        <div class="bar__title"><?= __('IDX Settings'); ?></div>
    </div>

    <div class="block">

        <ul class="tabs">
            <li class="current"><a href="<?=URL_BACKEND; ?>settings/idx/?feed=<?=$feed; ?>"><?= __('General'); ?></a></li>
            <li><a href="<?=URL_BACKEND; ?>settings/idx/meta/?feed=<?=$feed; ?>"><?= __('Meta'); ?></a></li>
            <?php if ($saved_search_email_responsive_template_exists) { ?>
            <li><a href="<?=URL_BACKEND; ?>settings/idx/savedsearches/?feed=<?=$feed; ?>"><?= __('Saved Searches Email'); ?></a></li>
            <?php } ?>
        </ul>

        <?php if (!$skin->hasFeature(Skin::DISABLE_SEARCH_CITIES)) { ?>
        <div class="field">
            <label for="fld-city" class="field__label"><?= __('Search Cities'); ?></label>
            <select id="fld-city" class="w1/1" name="search_cities[]" data-selectize multiple>
                <?php foreach ($cities as $option) { ?>
                    <option value="<?=$option['value']; ?>"<?=(is_array($system['search_cities']) && in_array($option['value'], $system['search_cities'])) ? ' selected' : ''; ?>>
                        <?=Format::htmlspecialchars($option['title']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <?php } ?>

        <div class="field">
            <label for="fld-register" class="field__label"><?= __('Registration Form Message'); ?></label>
            <textarea id="fld-register" class="tinymce simple" id="copy_register" name="copy_register" cols="24" rows="8"><?=htmlspecialchars($system['copy_register']); ?></textarea>
        </div>

        <div class="field">
            <label for="fld-login" class="field__label"><?= __('Login Form Message'); ?></label>
            <textarea id="fld-login" class="tinymce simple" id="copy_login" name="copy_login" cols="24" rows="8"><?=htmlspecialchars($system['copy_login']); ?></textarea>
        </div>

        <div class="field">
            <label for="fld-connect" class="field__label"><?= __('Social Connect Form Message'); ?></label>
            <textarea id="fld-connect" class="tinymce simple" id="copy_connect" name="copy_connect" cols="24" rows="8"><?=htmlspecialchars($system['copy_connect']); ?></textarea>
            <p class="text--mute"><?= __('This message is displayed on the Social Connect Form. You must first setup your settings from the %s', '<a href="' . URL_BACKEND . 'leads/tools/social/">' . __('IDX Social Connect Tool') . '</a>'); ?> .</p>
        </div>

        <div class="field<?=($system['savedsearches_responsive'] === 'true' || $system['force_savedsearches_responsive'] === 'true') ? ' hidden' : ''; ?>">
            <label for="fld-searches" class="field__label"><?= __('Saved Searches Email Message'); ?></label>
            <textarea id="fld-searches" class="tinymce simple email" id="savedsearches_message" name="savedsearches_message" rows="10" cols="80"><?=htmlspecialchars($system['savedsearches_message']); ?></textarea>
            <p class="text--mute"><?= __('Tags'); ?>: {first_name}, {last_name}, {email}, {search_url}, {search_title}, {results}, {signature}, {unsubscribe}, {url}</p>
            <div class="text--mute">
                <ul style="list-style: none; padding-left: 0; line-height: 2;">
                    <li><strong>{first_name}, {last_name}, {email} - </strong> <?= __('These tags will be replaced with the recipient\'s information.'); ?></li>
                    <li><strong>{search_url} - </strong> <?= __('This tag will contain the web address for the the lead to access their saved search.'); ?></li>
                    <li><strong>{search_title} - </strong> <?= __('This will be replaced with the title that the lead saved their search under.'); ?></li>
                    <li><strong>{results} - </strong> <?= __('This tag contains the markup for the list of results. Be sure to include this tag.'); ?></li>
                    <li><strong>{signature} - </strong> <?= __('This will be replaced with the lead\'s agent\'s signature'); ?>.</li>
                    <li><strong>{unsubscribe} - </strong> <?= __('This tag contains the web address that allows the lead to unsubscribe from receiving updates.'); ?></li>
                    <li><strong>{url} - </strong> <?= __('This tag will be replaced with the web address of your homepage.'); ?></li>
                </ul>
            </div>
        </div>

        <h3 class="divider text">
            <span class="divider__label divider__label--left"><?= __('Registration Settings'); ?></span>
        </h3>

        <div class="field">
            <label for="fld-registration" class="field__label"><?= __('Require Registration'); ?></label>
            <select class="w1/1" id="fld-registration" name="registration">
                <option value="false"<?=($system['registration'] === 'false') ? ' selected' : ''; ?>><?= __('No, Never'); ?></option>
                <option value="true"<?=($system['registration'] === 'true')   ? ' selected' : ''; ?>><?= __('Yes, Always'); ?></option>
                <option value="optional"<?=($system['registration'] === 'optional') ? ' selected' : ''; ?>><?= __('Yes, But Make it Optional'); ?></option>
                <option value=""<?=(intval($system['registration']) !== 0)    ? ' selected' : ''; ?>><?= __('After # of Properties Have Been Viewed'); ?></option>
            </select>
            <p class="text--mute"><?= __('Change the access privileges of your IDX Listing Detail pages for unregistered visitors'); ?>.</p>
        </div>

        <div id="registration-extras"<?=(intval($system['registration']) === 0) ? ' class="hidden"' : ''; ?>>
            <div class="field">
                <label class="field__label"><?= __('Number of Views'); ?></label>
                <label>
                    <input type="number" name="registration_views" value="<?=intval($system['registration']) !== 0 ? $system['registration'] : 3; ?>" min=0>
                </label>
            </div>
            <div class="field">
                <label class="field__label"><?= __('Force Registration'); ?></label>
                <input type="radio" name="registration_required" id="registration_required_true" value="true"<?=($system['registration_required'] === 'true') ? ' checked' : ''; ?>>
                <label for="registration_required_true"><?= __('Yes'); ?></label>
                <input type="radio" name="registration_required" id="registration_required_false" value="false"<?=($system['registration_required'] === 'false') ? ' checked' : ''; ?>>
                <label for="registration_required_false"><?= __('No'); ?></label>
            </div>
        </div>

        <?php if ($skin::hasFeature($skin::REGISTRATION_ON_MORE_PICS)) { ?>
            <div class="field">
                <label class="field__label"><?= __('Require Registration To View All Listing Photos'); ?></label>
                <div class="buttonset radios compact toggle">
                    <input type="radio" name="registration_on_more_pics" id="registration_on_more_pics_true" value="true"<?=($system['registration_on_more_pics'] === 'true') ? ' checked' : ''; ?>>
                    <label class="boolean toggle__label" for="registration_on_more_pics_true"><?= __('Yes'); ?></label>
                    <input type="radio" name="registration_on_more_pics" id="registration_on_more_pics_false" value="false"<?=($system['registration_on_more_pics'] === 'false') ? ' checked' : ''; ?>>
                    <label class="boolean toggle__label" for="registration_on_more_pics_false"><?= __('No'); ?></label>
                </div>
                <p class="text--mute">
                    <strong><?= __('If Yes'); ?>:</strong> <?= __('Visitors will be required to register in order to view all of listing photos on the details page'); ?>.<br>
                    <strong><?= __('If No'); ?>:</strong> <?= __('Visitors can view listing photos as normal'); ?>.
                </p>
            </div>
        <?php } ?>

        <div class="field">
            <label class="field__label"><?= __('Require Registrant Password'); ?></label>
            <input type="radio" name="registration_password" id="registration_password_true" value="true"<?=($system['registration_password'] === 'true') ? ' checked' : ''; ?>>
            <label class="toggle__label" for="registration_password_true"><?= __('Yes'); ?></label>
            <input type="radio" name="registration_password" id="registration_password_false" value="false"<?=($system['registration_password'] === 'false') ? ' checked' : ''; ?>>
            <label class="toggle__label" for="registration_password_false"><?= __('No'); ?></label>
            <p class="text--mute"><strong><?= __('Yes'); ?>:</strong> V<?= __('isitors will be required to provide a password'); ?>, <br/><strong><?= __('No'); ?>:</strong> <?= __('Visitors will be logged in once an email is provided'); ?>. </p>
        </div>

        <div class="field">
            <label class="field__label"><?= __('Require Phone Number'); ?></label>
            <input type="radio" name="registration_phone" id="registration_phone_true" value="true"<?=($system['registration_phone'] === 'true') ? ' checked' : ''; ?>>
            <label class="toggle__label" for="registration_phone_true"><?= __('Yes'); ?></label>
            <input type="radio" name="registration_phone" id="registration_phone_false" value="false"<?=($system['registration_phone'] === 'false') ? ' checked' : ''; ?>>
            <label class="toggle__label" for="registration_phone_false"><?= __('No'); ?></label>
            <p class="text--mute"><?= __('Require new registrants will be to provide a phone number'); ?>.</p>
        </div>

        <div class="field">
            <label class="field__label"><?= __('Require Email Verification'); ?></label>
            <input type="radio" name="registration_verify" id="registration_verify_true" value="true"<?=($system['registration_verify'] === 'true') ? ' checked' : ''; ?>>
            <label class="toggle__label" for="registration_verify_true"><?= __('Yes'); ?></label>
            <input type="radio" name="registration_verify" id="registration_verify_false" value="false"<?=($system['registration_verify'] === 'false') ? ' checked' : ''; ?>>
            <label class="toggle__label" for="registration_verify_false"><?= __('No'); ?></label>
            <p class="text--mute">
                <span><?= __('Require registrants to verify their email address before they can continue to view listings on your website'); ?>.</span>
                <span><?= __('An email will be sent to the registrant containing a link that they will need to click to verify their account'); ?>.</span>
            </p>
        </div>

        <div class="field">
            <label class="field__label"><?= __('Default Contact Method'); ?></label>
            <input id="default_contact_method_email" type="radio" name="default_contact_method" value="email"<?=($system['default_contact_method'] === 'email') ? ' checked' : ''; ?>>
            <label class="toggle__label" for="default_contact_method_email"><?= __('Email'); ?></label>
            <input id="default_contact_method_phone" type="radio" name="default_contact_method" value="phone"<?=($system['default_contact_method'] === 'phone') ? ' checked' : ''; ?>>
            <label class="toggle__label" for="default_contact_method_phone"><?= __('Phone'); ?></label>
            <?php if (!empty($settings->MODULES['REW_PARTNERS_TWILIO'])) { ?>
                <input id="default_contact_method_text" type="radio" name="default_contact_method" value="text"<?=($system['default_contact_method'] === 'text') ? ' checked' : ''; ?>>
                <label class="toggle__label" for="default_contact_method_text"><?= __('Text'); ?></label>
            <?php } ?>
            <p class="text--mute"><?= __('Choose the contact method to be selected by default on the registration form for new leads'); ?>.</p>
        </div>

        <?php $page->container('idx-setting-manager-after-registration')->loadModules(); ?>

    </div>

    <div class="btns btns--stickyB">
        <div class="R">
            <button class="btn btn--positive" type="submit">
                <svg class="icon icon-check mar0">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use>
                </svg>
                <?= __('Save'); ?>
            </button>
        </div>
    </div>

</form>
