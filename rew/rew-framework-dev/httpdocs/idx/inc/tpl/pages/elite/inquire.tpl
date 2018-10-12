<?php

// Listing Not Found
if (empty($listing)) {
    require $page->locateTemplate('idx', 'misc', 'not-found');
    return;
}

?>

<div class="modal-header">
    <h1 class="title"><?= ($_REQUEST['inquire_type'] == 'Property Showing') ? 'Request a Showing' : 'Inquire About this Property'; ?></h1>
</div>

<?php
// Check Showing Suite Module Output
if (!empty($showing_suite_display)) {
    echo $showing_suite_display;

    // Otherwise Default to REW Form
} else if (!empty($show_form)) { ?>

    <div class="modal-body">
        <?php require $page->locateTemplate('idx', 'misc', 'messages'); ?>
        <form action="?submit" method="post" class="uk-form">

            <input type="text" name="email" style="display:none;" value="" autocomplete="off">
            <input type="text" name="first_name" style="display:none;" value="" autocomplete="off">
            <input type="text" name="last_name" style="display:none;" value="" autocomplete="off">
            <?php if (!empty($agent)) { ?>
                <input type="hidden" name="agent" value="<?= $agent->getId(); ?>">
            <?php } ?>
            <?php if (!empty($lender)) { ?>
                <input type="hidden" name="lender" value="<?= $lender->getId(); ?>">
            <?php } ?>

            <div class="uk-grid">
                <div
                    class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 uk-margin-bottom">
                    <input class="uk-width-1-1 uk-form-large" placeholder="First Name" name="onc5khko" value="<?= Format::htmlspecialchars($_POST['onc5khko']); ?>" required>
                </div>
                <div
                    class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 uk-margin-bottom">
                    <input class="uk-width-1-1 uk-form-large" placeholder="Last Name" name="sk5tyelo" value="<?= Format::htmlspecialchars($_POST['sk5tyelo']); ?>" required>
                </div>
                <div
                    class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 uk-margin-bottom">
                    <input class="uk-width-1-1 uk-form-large" placeholder="Email Address" name="mi0moecs" value="<?= Format::htmlspecialchars($_POST['mi0moecs']); ?>" required>
                </div>
                <div
                    class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 uk-margin-bottom">
                    <input type="tel" class="uk-width-1-1 uk-form-large" placeholder="Phone<?= empty(Settings::getInstance()->SETTINGS['registration_phone']) ? ' (optional)' : ''; ?>" name="phone" value="<?= Format::htmlspecialchars($_POST['phone']); ?>" <?= !empty(Settings::getInstance()->SETTINGS['registration_phone']) ? ' required' : ''; ?>>
                </div>

                <?php if (!empty($inquiry_types)) { ?>
                    <div class="uk-width-1-1 uk-margin-bottom">
                        <label>Subject</label>
                        <select name="inquire_type" class="uk-form-large">
                            <?php foreach ($inquiry_types as $value => $inquiry_type) { ?>
                                <option value="<?= Format::htmlspecialchars($value); ?>" data-message=<?= $inquiry_type['message'] === false ? 'false' : '"' . Format::htmlspecialchars($inquiry_type['message']) . '"'; ?><?= ($_REQUEST['inquire_type'] === $value ? ' selected' : ''); ?>>
                                    <?= Format::htmlspecialchars($inquiry_type['subject']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                <?php } ?>
            </div>

            <div class="uk-width-1-1 uk-margin-bottom">
                <textarea class="uk-form-large uk-width-1-1" placeholder="Message" cols="32" rows="4" name="comments" required><?= html_entity_decode(Format::htmlspecialchars($_REQUEST['comments']), ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>

            <?php if (Settings::getInstance()->LANG == 'en-CA' && $user->info('opt_marketing') != 'in') { ?>
                <div class="uk-width-1-1 uk-margin-bottom">
                    <label class="toggle">
                        <input type="checkbox" name="opt_marketing" value="in"<?= $anti_spam['optin'] == 'in' ? ' checked' : ''; ?>>
                        <?= (!empty($anti_spam['consent_text']) ? $anti_spam['consent_text'] : 'Please send me updates concerning this website and the real estate market.'); ?>
                    </label>
                </div>
            <?php } ?>

            <button type="submit" class="uk-button"><?= ($_REQUEST['inquire_type'] == 'Property Showing') ? 'Send Request' : 'Send Inquiry'; ?></button>
        </form>
    </div>
<?php } else { ?>

    <div class="modal-body">
        <div class="uk-alert uk-alert-success">
            Your listing inquiry has been successfully sent.
        </div>
        <?php

        // Conversion tracking script
        $ppc = Util_CMS::getPPCSettings();
        if (!empty($ppc) && $ppc['enabled'] === 'true') {
            if ($_POST['inquire_type'] == 'Property Showing' && !empty($ppc['idx-showing'])) {
                $this->getSkin()->includeFile('tpl/partials/tracking.tpl.php', [
                    'trackingScript' => $ppc['idx-showing']
                ]);
            } else if (!empty($ppc['idx-inquire'])) {
                $this->getSkin()->includeFile('tpl/partials/tracking.tpl.php', [
                    'trackingScript' => $ppc['idx-inquire']
                ]);
            }
        }

        ?>
    </div>
<?php } ?>

