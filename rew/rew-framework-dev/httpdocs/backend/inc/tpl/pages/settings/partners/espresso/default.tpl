<div class="bar">
	<div class="bar__title"><?= __('REW Dialer'); ?></div>
	<div class="bar__actions">
		<a class="bar__action" href="/backend/settings/partners/"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-left-a"/></svg></a>
	</div>
</div>

<div class="block">
<div class="img-wrapper"><img src="/backend/img/dialer/dialer-settings.png" alt="Connect to your leads" class="dialerGraphic"></div>
<h1><?= __('Manage your Lines'); ?></h1>
<h3><?= __('Select a line and launch the manager to access account settings.'); ?></h3>
<div class="partner">
	<form>
		<div class="field">
			<select class="w1/1" name="tpid">
				<option value="">----</option>
				<?php for ($i = 1; $i <= $accounts; $i++) { ?>
				<?php $tpid = $api->generateTPID($_SERVER['HTTP_HOST'], $i); ?>
				<?php if ($api->validateAccount($tpid)) { ?>
				<option value="<?=$tpid; ?>"><?= __('Line'); ?>
				<?=$i; ?>
				</option>
				<?php } ?>
				<?php } ?>
			</select>
		</div>
		<button type="button" id="login-espresso-submit" class="btn btn--positive"><?= __('Launch Line Manager'); ?></button>
	</form>
</div>
<?php if (!empty($check_dev)) { ?>
<div class="text--mute linesMsg">[ <?= __('Dialer lines will become accessible when this site goes live.'); ?> ]</div>
<?php } ?>

</div>