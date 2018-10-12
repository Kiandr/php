<?php // Open Dialer Window ?>
<?php if (!empty($contacts)) { ?>

<div id="dialLead">
	<?php if ($limit > 0) { ?>
	<img src="/backend/img/dialer/dialer-graphic.png" alt="Connect to your leads" class="dialerGraphic">
	<h1><?= __('Start Calling'); ?></h1>
	<h3><?= __('Choose a line to start calling your selected leads.'); ?></h3>
	<div>
		<form id="dialForm" action="http://<?=$api->getDialerURL(); ?>/pb-init.php" method="post" data-contacts='<?=json_encode($contacts); ?>'>
			<input type="hidden" name="a" value="login">
			<input type="hidden" name="f_username" value="">
			<input type="hidden" name="f_password" value="">
			<input type="hidden" name="api_baseurl" value="<?=(!empty($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']; ?>">
			<input type="hidden" name="apiGroup" value="<?=$api->getContactAPIKey(); ?>" />
			<input type="hidden" name="tpsesskey" value="user_info">
				<input type="hidden" name="tpsessvalue" value="<?php
					echo $authuser->info('id')
						. '-' . ($authuser->isAssociate() ? 'associate' : 'agent')
						. '-' . (!empty($_SESSION['task_shortcut']['rew_dialer']) ? 'true' : 'false')
						. '-' . ((!empty($_SESSION['task_shortcut']['rew_dialer']) && $_SESSION['show_automated'] != 'N') ? 'true' : 'false')
						. '-' . ((!empty($_SESSION['task_shortcut']['rew_dialer']) && !empty($_SESSION['task_plan'])) ? $_SESSION['task_plan'] : 'false');
				?>">
			<select name="tpid">
				<option value="">-----</option>
				<?php for ($i = 1; $i <= $limit; $i++) { ?>
				<?php $tpid = $api->generateTPID($_SERVER['HTTP_HOST'], $i); ?>
				<?php if ($api->validateAccount($tpid)) { ?>
				<option id="<?=$api->generatePassword($tpid); ?>" value="<?=$tpid; ?>"><?= __('Line'); ?>
				<?=$i; ?>
				</option>
				<?php } ?>
				<?php } ?>
			</select>
			<span id="contact-ids">
                <?php foreach ($contacts as $contact) { ?>
                    <input type="hidden" name="contactid[]" value="<?=$contact; ?>-' + tpid + '">
                <?php } ?>
            </span>
			<input type="submit" value="Begin Dial-Session" class="beginDial">
		</form>
	</div>
	<?php if (!empty($check_dev)) { ?>
	<div class="linesMsg">[ <?= __('Dialer lines will become accessible when this site goes live.'); ?> ]</div>
	<?php } ?>
	<?php } else { ?>
	<p class="error"><?= __('Error: No dialers available. Please contact our support department at %s.', '<a href="mailto:support@realestatewebmasters.com">support@realestatewebmasters.com</a>'); ?></p>
	<?php } ?>
	<?php // Open Account Management Window ?>
	<?php } else if (isset($_GET['account_manager']) && isset($_GET['tpid'])) { ?>
	<form id="login-espresso-form" class="hidden" action="http://<?=$api->getDialerURL(); ?>/login.php" method="post">
		<input type="hidden" name="f_username" value="<?=$_GET['tpid']; ?>">
		<input type="hidden" name="f_password" value="<?=$api->generatePassword($_GET['tpid']); ?>">
		<button type="submit" value="submit"><?= __('Submit'); ?></button>
	</form>
	<p class="loaderMsg"><img src="/backend/img/dialer/loader.gif" class="loader" alt="loading"> <?= __('Loading REW Dialer Account Manager'); ?>&hellip;</p>
	<?php } else { ?>
	<p class="error"><?= __('Error: Action Not Recognized.'); ?></p>
	<?php } ?>
</div>
