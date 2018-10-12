<?php if (empty($_POST['unsubscribe']) && empty($_GET['d'])) { ?>

    <h1>Subscription Opt-Out Request</h1>

    <p>You have decided to unsubscribe from receiving our email messages.</p>
    <p>To complete the opt-out process, choose the option(s) that apply and click the unsubscribe button below.</p>

    <form method="post" class="uk-form uk-form-stacked">
        <input type="hidden" name="uid" value="<?=htmlspecialchars($_GET['uid']); ?>">
        <fieldset>
            <div class="field x6">
                <label>Unsubscribe E-mail</label>
                <label><input type="input" name="unsubemail" />
            </div>
        </fieldset>
        <div class="uk-grid">
            <div class="uk-width-1-1 uk-margin-bottom">
                <?php foreach ($options as $value => $option) { ?>
                    <div class="uk-width-1-1 uk-margin-bottom">
                        <label>
                            <input type="checkbox" name="unsubscribe[]" value="<?=$value; ?>"<?=(!empty($option['selected']) ? ' checked' : ''); ?>>
                            <?=$option['title']; ?>
                        </label>
                    </div>
                <?php } ?>
            </div>
            <div class="uk-form-row">
                <button value="Unsubscribe" name="send" type="submit" class="uk-button uk-button-medium">Unsubscribe
                </button>
            </div>
        </div>
    </form>

<?php } else if (!empty($_POST['unsubscribe']) && !empty($_POST['unsubemail'])) { ?>
    <h1>We are sorry to see you go.</h1>
    <p>Your request has been received and processed, and you have been removed from our mailing list.</p>
<?php } else if (!empty($_POST['unsubscribe']) && !empty($_POST['unsubemail'])) { ?>

    <h1>Confirm Account</h1>

    <p>In order for you to remain registered for our property search (so that you can save your searches and listings,
        get notifications of new properties, etc.), you will need to confirm your account by clicking on this
        button.</p>

    <form method="post" class="uk-form uk-form-stacked">
        <input type="hidden" name="uid" value="<?=htmlspecialchars($_GET['uid']); ?>">
        <input type="hidden" name="subscribe" value="subscribe">
        <input type="hidden" name="d" value="<?=$_REQUEST['d']; ?>">
        <?php if (isset($data['om'])) { ?>
            <input type="hidden" name="opt_marketing" value="in">
        <?php } ?>
        <?php if (isset($data['os'])) { ?>
            <input type="hidden" name="opt_searches" value="in">
        <?php } ?>
        <?php if (isset($data['s'])) { ?>
            <?php foreach ($data['s'] as $search) { ?>
                <input type="hidden" name="search[]" value="<?=$search['id']; ?>">
            <?php } ?>
        <?php } ?>
        <div class="uk-form-row">
            <button value="Confirm Account" name="send" type="submit" class="uk-button uk-button-medium">Confirm
                Account
            </button>
        </div>
    </form>

<?php } else if (!empty($_POST['subscribe'])) { ?>

    <h1>Thank you for confirming your account!</h1>
    <p>Your request has been received and processed, and you will continue to receive mail from us.</p>

<?php } else { ?>

    <h1>Error</h1>
    <p>Something has gone wrong.  Please check the e-mail address entered.</p>

<?php } ?>