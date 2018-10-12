<?php if (empty($_POST['unsubscribe']) && empty($_GET['d'])) { ?>

	<h1>Subscription Opt-Out Request</h1>

    <p>You have decided to unsubscribe from receiving our email messages.</p>
    <p>To complete the opt-out process, choose the option(s) that apply and click the unsubscribe button below.</p>

    <form method="post">
        <input type="hidden" name="uid" value="<?=htmlspecialchars($_GET['uid']); ?>">

        <fieldset>
            <div class="field x6">
                <label>Unsubscribe E-mail</label>
                <label><input type="input" name="unsubemail" />
            </div>
        </fieldset>

        <?php foreach ($options as $value => $option) { ?>
        <div class="field">
            <label class="toggle"><input type="checkbox" name="unsubscribe[]" value="<?=$value; ?>"<?=(!empty($option['selected']) ? ' checked' : ''); ?>> <span class="toggle__label"><?=$option['title']; ?></span></label>
        </div>
        <?php } ?>


        <div class="buttons -mar-top">
            <button class="button button--strong button--pill" value="Unsubscribe" name="send" type="submit">Unsubscribe</button>
        </div>

    </form>

<?php } else if (!empty($_POST['unsubscribe']) && !empty($_POST['unsubemail'])) { ?>
    <h1>We are sorry to see you go.</h1>
    <p>Your request has been received and processed, and you have been removed from our mailing list.</p>
<?php } else if (!empty($data) && empty($_POST['subscribe'])) { ?>

	<h1>Confirm Account</h1>

	<p>In order for you to remain registered for our property search (so that you can save your searches and listings, get notifications of new properties, etc.), you will need to confirm your account by clicking on this button.</p>

    <form method="post">
        <input type="hidden" name="uid" value="<?=htmlspecialchars($_GET['uid']); ?>">
        <input type="hidden" name="subscribe" value="subscribe">
        <input type="hidden" name="d" value="<?=$_REQUEST['d'];?>">
        <? if (isset($data['om'])) { ?><input type="hidden" name="opt_marketing" value="in"><? } ?>
        <? if (isset($data['os'])) { ?><input type="hidden" name="opt_searches" value="in"><? } ?>
        <? if (isset($data['s'])) { ?>
        	<? foreach ($data['s'] as $search) {?>
        		<input type="hidden" name="search[]" value="<?=$search['id']; ?>">
        	<? } ?>
        <? } ?>

        <div class="buttons">
            <button class="button button--strong button--pill" value="Confirm Account" name="send" type="submit">Confirm Account</button>
        </div>

    </form>

<?php } else if (!empty($_POST['subscribe'])) { ?>

	<h1>Thank you for confirming your account!</h1>
    <p>Your request has been received and processed, and you will continue to receive mail from us.</p>

<?php } else { ?>

    <h1>Error</h1>
    <p>Something has gone wrong.  Please check the e-mail address entered.</p>

<?php } ?>