<?php if (!empty($show_form)) { ?>

    <?php if (!empty($errors) && $_POST['step'] == 'verify') { ?>
		<div class="msg negative"><p><?=implode('</p><p>', $errors); ?></p></div>
    <?php } ?>

    <?php if ($_POST['step'] == 'email') { ?>
        <?php if (!empty($errors)) { ?>
        	<div class="msg negative"><p><?=implode('</p><p>', $errors); ?></p></div>
        <?php } ?>
        <?php if (!empty($success)) { ?>
        	<div class="msg positive"><p><?=implode('</p><p>', $success); ?></p></div>
        <?php } ?>
    <?php } ?>

    <p>It looks like you have almost completed your registration, but you haven't clicked on the confirmation link we sent to your email address. You simply need to click on that link in the email, or paste the code from that email into this space below:</p>

    <form action="?submit" method="post">

    	<input type="hidden" name="step" value="verify">

    	<div class="field x12">
    		<label>Verification Code</label>
    		<input name="code" value="<?=htmlspecialchars($_POST['code']); ?>" required>
    	</div>

        <div class="btnset">
            <button class="button strong -mar-bottom-sm" type="submit">Verify Email</button>
        </div>

    </form>

    <p>Need us to re-send the email to you? Use the form below to have it sent to you.<br>(The first email may have gone into your junk folder by mistake)</p>

    <form id="resend-code" action="?submit" method="post">

    	<input type="hidden" name="step" value="email">

    	<div class="field x12">
    		<label>Email Address</label>
    		<input type="email" name="email" value="<?=htmlspecialchars($_POST['email']); ?>">
    	</div>

    	<div class="field x12">
    		<label><input type="checkbox" name="resend" value="1" required> Yes, please re-send my confirmation link. <em class="required">*</em></label>
    	</div>

        <div class="btnset">
            <button class="button strong -mar-bottom-sm" type="submit">Send Code</button>
        </div>

    </form>

    <p>Thanks for your understanding as we work to give you the best possible experience!</p>

<?php ob_start(); ?>
/* <script> */
// Disable submit button once submitted
$('#resend-code').on('submit', function () {
	var $form = $(this), $submit = $form.find('button[name="send"]');
	$submit.prop('disabled', true);
});
/* </script> */
<?php $page->writeJS(ob_get_clean()); ?>

<?php } else {

	// Success
    echo '<h1>Thank you!</h1>';
    echo '<p>Your email has successfully been verified! You can now save listings, save searches and more!</p>';
    echo '<p><a href="' . Settings::getInstance()->SETTINGS['URL_IDX'] . '" target="_parent" class="parentlink">Click here to start searching</a></p>';

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
		}, 2500);");

	}

}