<a name="quick-inquire"></a>
<div id="inquire-allure">
	<?php

		// Global Resources
		global $page;

		// Current User
		$user = User_Session::get();

		// Anti-Spam Settings
		$anti_spam = array(
			'optin' => Settings::get('anti_spam.optin'),
			'consent_text' => Settings::get('anti_spam.consent_text')
		);

		// Submit Form
		if (isset($_GET['inquire'])) {

			// Require 'contactForm' Function
			require_once Settings::getInstance()->DIRS['BACKEND'] . 'inc/php/functions/funcs.ContactSnippets.php';

			// Require Phone Number? If logged in, no. Otherwise, use IDX Preferences
			$require_phone = $user->isValid() ? false : Settings::getInstance()->SETTINGS['registration_phone'];

			// Quick Inquire
			if (isset($_POST['inquire_type']) && $_POST['inquire_type'] == 'Quick Inquire') {
				$_POST['comments'] = $_POST['inquire']['comments'];

				// Store Comments
				$user->saveInfo('comments', $_POST['comments']);

				unset($_POST['showing']['comments']);
				unset($_POST['inquire']['comments']);
				$sent = contactForm(6, 'Quick Inquire', false, $require_phone);
			}

			// Quick Showing
			if (isset($_POST['inquire_type']) && $_POST['inquire_type'] == 'Quick Showing') {
				$_POST['comments'] = $_POST['showing']['comments'];

				// Store Comments
				$user->saveInfo('comments', $_POST['comments']);

				unset($_POST['inquire']['comments']);
				unset($_POST['showing']['comments']);
				$sent = contactForm(6, 'Quick Showing', false, $require_phone);
			}

			// Display Errors
			if (!empty($sent['errors'])) {
				echo '<div class="msg negative"><strong>Oops! Your Form Contains Errors&hellip;</strong><ul><li>' . implode('</li><li>', $sent['errors']) . '</li></ul></div>';
			}

			// Display Success
			if (!empty($sent['success'])) {
				echo '<div class="msg positive"><p><strong>Thanks for your interest!</strong> We\'ll get back to you as soon as possible.</p></div>';
				// Conversion Tracking
				$ppc = Util_CMS::getPPCSettings();
				if (!empty($ppc) && $ppc['enabled'] === 'true') {
					if ($_POST['inquire_type'] == 'Quick Inquire' && !empty($ppc['idx-inquire'])) echo $ppc['idx-inquire'];
					if ($_POST['inquire_type'] == 'Quick Showing' && !empty($ppc['idx-showing'])) echo $ppc['idx-showing'];
				}
			}

		}

		// Inquiry Type
		$inquire_type = isset($_POST['inquire_type']) ? $_POST['inquire_type'] : '';
		switch ($inquire_type) {
			case 'Quick Showing':
				$inquire_type = 'Quick Showing';
				break;
			default:
				$inquire_type = 'Quick Inquire';
				break;
		}

		// Default Message
		if ($idx->getLink() == 'cms') {
			$inquire_comments = isset($_POST['inquire']['comments']) ? $_POST['inquire']['comments'] : "I was searching for a Property and found the listing ({$listing['ListingTitle']}). Please send me more information regarding {$listing['Address']}, {$listing['AddressCity']}, {$listing['AddressState']}, {$listing['AddressZipCode']}. Thank you!";
			$showing_comments = isset($_POST['showing']['comments']) ? $_POST['showing']['comments'] : "I'd like to request a showing of " . $listing['Address'] . ", " . $listing['AddressCity'] . ", " . $listing['AddressState'] . ", " . $listing['AddressZipCode'] . ". \nThank you!";
		} else {
			$inquire_comments = isset($_POST['inquire']['comments']) ? $_POST['inquire']['comments'] : Lang::write('INQUIRE_ASK_ABOUT', $listing);
			$showing_comments = isset($_POST['showing']['comments']) ? $_POST['showing']['comments'] : Lang::write('INQUIRE_REQUEST_SHOWING', $listing);
		}

	?>

	<div class="tabset">
		<ul>
			<li<?=(isset($inquire_type) && ($inquire_type == 'Quick Inquire')) ? ' class="current"' : ''; ?>><a rel="nofollow" href="#quick-inquire">Ask About this Property</a></li>
			<li<?=(isset($inquire_type) && ($inquire_type == 'Quick Showing')) ? ' class="current"' : ''; ?>><a rel="nofollow" href="#quick-showing">Request a Showing</a></li>
		</ul>
	</div>

	<div class="panel">

		<form action="?inquire#quick-inquire" method="post" class="rewfw">

			<input type="hidden" name="step" value="send">

            <?php require Settings::getInstance()->URLS['HONEYPOT']; ?>

			<input type="hidden" name="mls_number" value="<?=htmlspecialchars($listing['ListingMLS']); ?>">
            <input type="hidden" name="listing_type" value="<?=htmlspecialchars($listing['ListingType']); ?>">
            <input type="hidden" name="listing_feed" value="<?=htmlspecialchars($listing['ListingFeed']); ?>">
			<input type="hidden" name="address" value="<?=htmlspecialchars($listing['Address'] . ', ' . $listing['AddressCity'] . ', ' . $listing['AddressState'] . ', ' . $listing['AddressZipCode']); ?>">
			<input type="hidden" name="price" value="<?=htmlspecialchars($listing['ListingPrice']); ?>">

		   <?php if($user->isValid()) { ?>

				<input type="hidden" name="onc5khko" id="frm_onc5khko" value="<?=$user->info('first_name'); ?>">
				<input type="hidden" name="sk5tyelo" id="frm_sk5tyelo" value="<?=$user->info('last_name'); ?>">
				<input type="hidden" name="mi0moecs" id="frm_mi0moecs" value="<?=$user->info('email'); ?>">
				<input type="hidden" name="telephone" id="frm_telephone" value="<?=$user->info('phone'); ?>">

			<?php } else { ?>

				<div class="field x6">
					<label>First Name</label>
					<input type="text" name="onc5khko" id="frm_onc5khko" size="28" value="<?=htmlspecialchars($_POST['onc5khko']); ?>">
				</div>

			   <div class="field x6 last">
					<label>Last Name</label>
					<input type="text" name="sk5tyelo" id="frm_sk5tyelo" size="28" value="<?=htmlspecialchars($_POST['sk5tyelo']); ?>">
				</div>

				<div class="field x6">
					<label>Email</label>
					<input type="text" name="mi0moecs" id="frm_mi0moecs" size="38" value="<?=htmlspecialchars($_POST['mi0moecs']); ?>">
				   <small>Please provide a valid email address.</small>
				</div>

			   <div class="field x6 last">
				   <label>Phone
					   <?php if (empty(Settings::getInstance()->SETTINGS['registration_phone'])) { ?>
						   <small>(optional)</small>
					   <?php } ?>
				   </label>
				   <input type="text" name="telephone" id="frm_telephone" size="28" value="<?=htmlspecialchars($_POST['telephone']); ?>">
				</div>

			<?php } ?>

			<input type="hidden" name="inquire_type" value="<?=$inquire_type; ?>">

			<div class="field x12">
				<textarea<?=(isset($inquire_type) && ($inquire_type == 'Quick Inquire')) ? '' : ' style="display: none;"'; ?> cols="32" rows="3" name="inquire[comments]" id="frm_comments"><?=$inquire_comments; ?></textarea>
				<textarea<?=(isset($inquire_type) && ($inquire_type == 'Quick Showing')) ? '' : ' style="display: none;"'; ?> cols="32" rows="3" name="showing[comments]" id="frm_comments"><?=$showing_comments; ?></textarea>
			</div>

			<?php if (Settings::getInstance()->LANG == 'en-CA' && $user->info('opt_marketing') != 'in') { ?>
				<div class="field x12">
					<label class="toggle"><input type="checkbox" name="opt_marketing" value="in"<?=$anti_spam['optin'] == 'in' ? ' checked' : ''; ?>> <?=(!empty($anti_spam['consent_text']) ? $anti_spam['consent_text'] : 'Please send me updates concerning this website and the real estate market.'); ?></label>
				</div>
			<?php } ?>

			<div class="btnset">
				<button class="strong" type="submit">Send Inquiry</button>
			</div>

		</form>

	</div>

</div>
<?php

// Output Buffering
ob_start();

?>
/* <script> */
(function () {
	// Toggle Form Tabs
	var $inquire = $('#inquire-allure').on(BREW.events.click, '.tabset li', function () {
		var $this = $(this).addClass('current');
		$this.siblings('li').removeClass('current');
		if ($this.find('a').attr('href') == '#quick-inquire') {
			$inquire.find('textarea[name="inquire[comments]"]').show();
			$inquire.find('textarea[name="showing[comments]"]').hide();
			$inquire.find('input[name="inquire_type"]').val('Quick Inquire');
		}
		if ($this.find('a').attr('href') == '#quick-showing') {
			$inquire.find('textarea[name="showing[comments]"]').show();
			$inquire.find('textarea[name="inquire[comments]"]').hide();
			$inquire.find('input[name="inquire_type"]').val('Quick Showing');
		}
		return false;
	});
})();
/* </script> */
<?php

// Write to Page
$page->writeJS(ob_get_clean());

?>