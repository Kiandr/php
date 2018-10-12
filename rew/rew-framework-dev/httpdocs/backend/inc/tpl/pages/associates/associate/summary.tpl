<?php

include('inc/tpl/app/menu-associates.tpl.php');

// Render associate summary header (menu/title/preview)
echo $this->view->render('inc/tpl/partials/associate/summary.tpl.php', [
    'title' => __('Associate Summary'),
    'associate' => $associate,
    'associateAuth' => $associateAuth
]);
?>

<div class="block">
	<div class="keyvals keyvals--bordered -marB">
		<?php if (!empty($can_email)) { ?>
		<div class="keyvals__row keyvals__row--rows@sm">
			<strong class="keyvals__key text text--strong -padB0@sm"><?= __('Email'); ?></strong> <span class="keyvals__val text text--mute -padT0@sm"> <a href="<?=URL_BACKEND; ?>email/?id=<?=$associate['id']; ?>&type=associates">
			<?=$associate['email']; ?>
			</a> </span> </div>
		<?php } ?>
	</div>
</div>

<div class="block">

    <div class="divider -marB"><span class="divider__label divider__label--left text text--mute text--small"><?= __('Basics'); ?></span></div>

    <div class="keyvals keyvals--bordered -marB">

    	<div class="keyvals__row keyvals__row--rows@sm">
    		<span class="keyvals__key text text--strong -padB0@sm"><?= __('Timezone:'); ?></span>
    		<span class="keyvals__val text text--mute -padT0@sm">
    			<?=$timezone; ?>
    		</span>
    	</div>

    </div>

    <div class="divider -marB"><span class="divider__label divider__label--left text text--mute text--small"><?= __('Contact Details'); ?></span></div>

    <div class="keyvals keyvals--bordered -marB">

    	<?php if (!empty($can_email)) { ?>
    	<div class="keyvals__row keyvals__row--rows@sm">
    		<span class="keyvals__key text text--strong -padB0@sm"><?= __('Email Address:'); ?></span>
    		<strong class="keyvals__val text text--mute -padT0@sm"><a href="<?=URL_BACKEND; ?>email/?id=<?=$associate['id'] ;?>&type=associates">
    			<?=Format::htmlspecialchars($associate['email']); ?>
    			</a>
    		</strong>
    	</div>
    		<?php } elseif ($authuser->isAssociate() && $authuser->info('id') == $associate['id']) { ?>
    	<div class="keyvals__row keyvals__row--rows@sm">
    		<span class="keyvals__key text text--strong -padB0@sm"><?= __('Email Address:'); ?></div>
    		<strong class="keyvals__val text text--mute -padT0@sm">
    			<?=!empty($associate['email']) ? '<a href="mailto:' . Format::htmlspecialchars($associate['email']) . '">' . Format::htmlspecialchars($associate['email']) . '</a>' : '-'; ?>
    		</strong>
    	</div>
    		<?php } ?>
    	<div class="keyvals__row keyvals__row--rows@sm">
    		<span class="keyvals__key text text--strong -padB0@sm"><?= __('Office Phone:'); ?></span>
    		<strong class="keyvals__val text text--mute -padT0@sm">
    			<?=!empty($associate['office_phone']) ? Format::htmlspecialchars($associate['office_phone']) : '-'; ?>
    		</strong>
    	</div>
    	<div class="keyvals__row keyvals__row--rows@sm">
    		<span class="keyvals__key text text--strong -padB0@sm"><?= __('Home Phone:'); ?></span>
    		<strong class="keyvals__val text text--mute -padT0@sm">
    			<?=!empty($associate['home_phone']) ? Format::htmlspecialchars($associate['home_phone']) : '-'; ?>
    		</strong>
    	</div>
    	<div class="keyvals__row keyvals__row--rows@sm">
    		<span class="keyvals__key text text--strong -padB0@sm"><?= __('Cell Phone:'); ?></span>
    		<strong class="keyvals__val text text--mute -padT0@sm">
    			<?=!empty($associate['cell_phone']) ? Format::htmlspecialchars($associate['cell_phone']) : '-'; ?>
    		</strong>
    	</div>
    	<div class="keyvals__row keyvals__row--rows@sm">
    		<span class="keyvals__key text text--strong -padB0@sm"><?= __('Fax:'); ?></span>
    		<strong class="keyvals__val text text--mute -padT0@sm">
    			<?=!empty($associate['fax']) ? Format::htmlspecialchars($associate['fax']) : '-'; ?>
    		</strong>
    	</div>
    </div>

</div>