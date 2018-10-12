<?php

// Listing Not Found
if (empty($listing)) {
    include $page->locateTemplate('idx', 'misc', 'not-found');
    return;
}

?>

<h1>Your <?=Locale::spell('favorite'); ?> homes at your fingertips.</h1>
<p> It has never been easier to save and share listings on the go. Send this listing to yourself via text message. <small>(Carrier fees may apply)</small></p>

<?php if (empty($success)) { ?>

    <?php if (!empty($error)) { ?>
        <div class="uk-alert uk-alert-negative"><p><?=Format::htmlspecialchars($error); ?></p></div>
    <?php } ?>

    <form action="?submit" method="post">

        <div class="field x12">
            <label>Your Phone #</label>
            <input type="tel" name="to" value="<?=Format::htmlspecialchars($to); ?>" required>
            <?=!empty($errors['to']) ? '<small class="negative">' . $errors['to'] . '</small>' : ''; ?>
        </div>

        <div class="field x12">
            <label style="float: left;">Include Short Message</label>
            <small class="remaining" style="float: right; display: inline;"></small>
            <textarea cols="32" rows="2" name="message" data-maxlength="<?=$maxlength; ?>" placeholder="<?=Format::htmlspecialchars($placeholder); ?>"><?=Format::htmlspecialchars($_POST['message']); ?></textarea>
            <?=!empty($errors['message']) ? '<small class="negative">' . $errors['message'] . '</small>' : ''; ?>
        </div>

        <div class="btnset">
            <button type="submit" class="strong">Send to your Device</button>
        </div>

    </form>

<?php } else { ?>

    <div class="uk-alert uk-alert-success"><p><?=$success; ?></p></div>
    <?php

        // Conversion tracking script
        $ppc = Util_CMS::getPPCSettings();
        if (!empty($ppc) && $ppc['enabled'] === 'true' && !empty($ppc['idx-phone'])) {
            $this->getSkin()->includeFile('tpl/partials/tracking.tpl.php', [
                'trackingScript' => $ppc['idx-phone']
            ]);
        }

    ?>
<?php } ?>