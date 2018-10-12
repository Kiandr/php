<form action="<?=$form_action;?>" method="post" class="rew_check">

	<div class="bar">
		<div class="bar__title"><?= __('Happy Grasshopper&reg;'); ?></div>
		<div class="bar__actions">
    		<?php if (isset($_GET['setup'])) { ?>
    		<a class="bar__action" href="/backend/settings/partners/grasshopper/"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-left-a"/></svg></a>
    		<?php } else { ?>
			<a class="bar__action" href="/backend/settings/partners/"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-left-a"/></svg></a>
            <?php } ?>
		</div>
	</div>

    <div class="block">

    	<div class="btns btns--stickyB">
    		<span class="R">
    			<?php if (isset($_GET['setup'])) { ?>
    			<button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xlink:href="/backend/img/icos.svg#icon-check"/></svg> <?= __('Save'); ?></button>
    			<?php if (!empty($user_key) || !empty($user_code)) { ?>
    			<a class="btn delete" href="../?disconnect=grasshopper" onclick="javascript:return confirm('<?= __('Are you sure you want to disable the integration with this partner?'); ?>');"><?= __('Disable'); ?></a>
    			<?php } ?>
            <?php } else if (empty($user_key) || empty($user_code)) { ?>
    		<?php } else { ?>
        		<a href="?setup" class="btn settings -marR8"><?= __('Integration Settings'); ?></a>
        		<button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xlink:href="/backend/img/icos.svg#icon-check"/></svg> <?= __('Save'); ?></button>
    		<?php } ?>
    		</span>
    	</div>

        <?php if (!isset($_GET['setup']) && (empty($user_key) || empty($user_code))) { ?>
		<div class="help">
		    <img src="/backend/img/hlp/setup.png"/>
			<h1><?= __('Set Up Happy Grasshopper&reg; Integration'); ?></h1>
			<p><?= __('Happy Grasshopper&reg; integration is currently %s. To use this feature, provide your Happy Grasshopper&reg; login credentials.', '<strong>' . __('inactive') . '</strong>'); ?></p>
			<p><a href="?setup" class="btn btn--positive"><svg class="icon icon-add mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use></svg> <?= __('Set Up Happy Grasshopper&reg; Integration'); ?></a></p>
		</div>
		<?php } ?>


	<?php if (isset($_GET['setup'])) { ?>
	<div class="field">
		<label class="field__label"><?= __('Username'); ?></label>
		<input class="w1/1" type="text" name="username" value="<?=htmlspecialchars($_POST['username']); ?>">
		<label class="hint"><?= __('Example: yourname@example.com'); ?></label>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Password'); ?></label>
		<input class="w1/1" type="password" name="password" value="<?=htmlspecialchars($_POST['password']); ?>">
	</div>
	<?php } else if (empty($user_key) || empty($user_code)) { ?>
	<?php  } else { ?>
	<h3><?= __('Email Signature'); ?></h3>
    <div class="cols">
    	<div class="field col w1/2">
    		<label class="field__label" for="signature_signed"><?= __('Signed'); ?> <em class="required">*</em></label>
    		<input class="w1/1" type="text" name="signature_signed" id="signature_signed" value="<?=htmlspecialchars($_POST['signature_signed']);?>">
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label" for="signature_name"><?= __('Full Name'); ?> <em class="required">*</em></label>
    		<input class="w1/1" type="text" name="signature_name" id="signature_name" value="<?=htmlspecialchars($_POST['signature_name']);?>">
    	</div>
    </div>
	<div class="field">
		<label class="field__label" for="signature_business_name"><?= __('Business Name'); ?></label>
		<input class="w1/1" type="text" name="signature_business_name" id="signature_business_name" value="<?=htmlspecialchars($_POST['signature_business_name']);?>">
	</div>
	<div class="field">
		<label class="field__label" for="signature_tag_line"><?= __('Tag Line'); ?></label>
		<textarea class="w1/1" name="signature_tag_line" id="signature_tag_line"><?=htmlspecialchars($_POST['signature_tag_line']);?></textarea>
	</div>
    <div class="cols">
    	<div class="field col w1/2">
    		<label class="field__label" for="signature_street_address"><?= __('Address'); ?> <em class="required">*</em></label>
    		<input class="w1/1" type="text" name="signature_street_address" id="signature_street_address" value="<?=htmlspecialchars($_POST['signature_street_address']);?>">
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label" for="signature_city"><?= __('City'); ?> <em class="required">*</em></label>
    		<input class="w1/1" type="text" name="signature_city" id="signature_city" value="<?=htmlspecialchars($_POST['signature_city']);?>">
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label" for="signature_state"><?= __('State'); ?> <em class="required">*</em></label>
    		<input class="w1/1" type="text" name="signature_state" id="signature_state" value="<?=htmlspecialchars($_POST['signature_state']);?>">
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label" for="signature_zip"><?= __('ZIP'); ?> <em class="required">*</em></label>
    		<input class="w1/1" type="text" name="signature_zip" id="signature_zip" value="<?=htmlspecialchars($_POST['signature_zip']);?>">
    	</div>
    </div>
	<div class="field">
		<label class="field__label" for="signature_email"><?= __('Email'); ?> <em class="required">*</em></label>
		<input class="w1/1" type="text" name="signature_email" id="signature_email" value="<?=htmlspecialchars($_POST['signature_email']);?>">
		<p class="text--mute"><?= __('Your emails will be delivered from the email address you enter here'); ?></p>
	</div>
    <div class="cols">
    	<div class="field col w1/3">
    		<label class="field__label" for="signature_phone"><?= __('Phone Number'); ?></label>
    		<input class="w1/1" type="text" name="signature_phone" id="signature_phone" value="<?=htmlspecialchars($_POST['signature_phone']);?>">
    	</div>
    	<div class="field col w1/3">
    		<label class="field__label" for="signature_phone_2"><?= __('Secondary Phone Number'); ?></label>
    		<input class="w1/1" type="text" name="signature_phone_2" id="signature_phone_2" value="<?=htmlspecialchars($_POST['signature_phone_2']);?>">
    	</div>
    	<div class="field col w1/3">
    		<label class="field__label" for="signature_fax"><?= __('Fax'); ?></label>
    		<input class="w1/1" type="text" name="signature_fax" id="signature_fax" value="<?=htmlspecialchars($_POST['signature_fax']);?>">
    	</div>
    </div>
    <div class="cols">
    	<div class="field col w1/2">
    		<label class="field__label" for="signature_website"><?= __('Website'); ?></label>
    		<input class="w1/1" type="text" name="signature_website" id="signature_website" value="<?=htmlspecialchars($_POST['signature_website']);?>">
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label" for="signature_website_2"><?= __('Second Website'); ?></label>
    		<input class="w1/1" type="text" name="signature_website_2" id="signature_website_2" value="<?=htmlspecialchars($_POST['signature_website_2']);?>">
    	</div>
    </div>
    <div class="cols">
    	<div class="field col w1/2">
    		<label class="field__label" for="signature_facebook"><?= __('Facebook Profile'); ?></label>
    		<input class="w1/1" type="text" name="signature_facebook_profile" id="signature_facebook_profile" value="<?=htmlspecialchars($_POST['signature_facebook_profile']);?>">
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label" for="signature_facebook_page"><?= __('Facebook Page'); ?></label>
    		<input class="w1/1" type="text" name="signature_facebook_page" id="signature_facebook_page" value="<?=htmlspecialchars($_POST['signature_facebook_page']);?>">
    	</div>
    </div>
	<div class="field">
		<label class="field__label" for="signature_google_plus"><?= __('Google Plus'); ?></label>
		<input class="w1/1" type="text" name="signature_google_plus" id="signature_google_plus" value="<?=htmlspecialchars($_POST['signature_google_plus']);?>">
	</div>
	<div class="field">
		<label class="field__label" for="signature_youtube"><?= __('Youtube'); ?></label>
		<input class="w1/1" type="text" name="signature_youtube" id="signature_youtube" value="<?=htmlspecialchars($_POST['signature_youtube']);?>">
	</div>
	<div class="field">
		<label class="field__label" for="signature_pinterest"><?= __('Pinterest'); ?></label>
		<input class="w1/1" type="text" name="signature_pinterest" id="signature_pinterest" value="<?=htmlspecialchars($_POST['signature_pinterest']);?>">
	</div>
	<div class="field">
		<label class="field__label" for="signature_linkedin"><?= __('LinkedIn'); ?></label>
		<input class="w1/1" type="text" name="signature_linkedin" id="signature_linkedin" value="<?=htmlspecialchars($_POST['signature_linkedin']);?>">
	</div>
	<div class="field">
		<label class="field__label" for="signature_twitter"><?= __('Twitter'); ?></label>
		<input class="w1/1" type="text" name="signature_twitter" id="signature_twitter" value="<?=htmlspecialchars($_POST['signature_twitter']);?>">
	</div>
	<?php } ?>

    </div>

</form>