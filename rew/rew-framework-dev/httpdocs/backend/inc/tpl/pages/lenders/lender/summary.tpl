<?php include('inc/tpl/app/menu-lenders.tpl.php'); ?>
<div class="bar">
	<a class="bar__title" data-drop="#menu--filters" href="javascript:void(0);"><?= __('Lender Summary'); ?> <svg class="icon icon-drop"><use xlink:href="/backend/img/icos.svg#icon-drop" xmlns:xlink="http://www.w3.org/1999/xlink"/></svg></a>
	<div class="bar__actions">
		<a class="bar__action" href="<?=URL_BACKEND; ?>lenders/"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
	</div>
</div>
<?php include('inc/tpl/app/summary-lender.tpl.php'); ?>

<div class="block">

    <div class="keyvals keyvals--bordered -marB">
        <?php if(!empty($lead['phone'])) { ?><div class="keyvals__row keyvals__row--rows@sm"><span class="keyvals__key text text--strong -padB0@sm"><?= __('Phone'); ?></span><span class="keyvals__val text text--mute -padT0@sm"><?=$lender['phone'];?></span></div><?php } ?>
        <div class="keyvals__row keyvals__row--rows@sm"><span class="keyvals__key text text--strong -padB0@sm"><?= __('Email'); ?></span><span class="keyvals__val text text--mute -padT0@sm"><?=$lender['email']?></span></div>
    </div>


    <div class="divider -marB"><span class="divider__label divider__label--left text text--mute text--small"><?= __('Basics'); ?></span></div>

    <div class="keyvals keyvals--bordered marB">
        <div class="keyvals__row keyvals__row--rows@sm">
            <span class="keyvals__key text text--strong -padB0@sm"><?= __('Timezone'); ?>:</span>
            <span class="keyvals__val text text--mute -padT0@sm"><?=$lender['timezone']; ?></span>
        </div>
    </div>

    <div class="divider -marB"><span class="divider__label divider__label--left text text--mute text--small"><?= __('Contact Details'); ?></span></div>

    <div class="keyvals keyvals--bordered marB">
        <?php if (!empty($can_email)) { ?>
        <div class="keyvals__row keyvals__row--rows@sm">
            <span class="keyvals__key text text--strong -padB0@sm"><?= __('Email Address'); ?>:</span>
            <span class="keyvals__val text text--mute -padT0@sm"><a href="<?=URL_BACKEND; ?>email/?id=<?=$lender['id'] ;?>&type=lenders"><?=Format::htmlspecialchars($lender['email']); ?></a></span>
        </div>
        <?php } elseif ($authuser->isLender() && $authuser->info('id') == $lender['id']) { ?>
        <div class="keyvals__row keyvals__row--rows@sm">
            <strong class="keyvals__key text text--strong -padB0@sm"><?= __('Email Address'); ?>:</strong>
            <span class="keyvals__val text text--mute -padT0@sm"></span>
        </div>
        <?php } elseif ($authuser->isLender() && $authuser->info('id') == $lender['id']) { ?>
        <div class="keyvals__row keyvals__row--rows@sm">
            <strong class="keyvals__key text text--strong -padB0@sm"><?= __('Email Address'); ?>:</strong>
            <span class="keyvals__val text text--mute -padT0@sm">
            <?=!empty($lender['email']) ? '<a href="mailto:' . Format::htmlspecialchars($lender['email']) . '">' . Format::htmlspecialchars($lender['email']) . '</a>' : '-'; ?>
        </span>
        </div>
        <?php } ?>

    	<div class="keyvals__row keyvals__row--rows@sm">
    		<strong class="keyvals__key text text--strong -padB0@sm"><?= __('Office Phone'); ?>:</strong>
    		<span class="keyvals__val text text--mute -padT0@sm">
    			<?=!empty($lender['office_phone']) ? Format::htmlspecialchars($lender['office_phone']) : '-'; ?>
    		</span>
    	</div>

    	<div class="keyvals__row keyvals__row--rows@sm">
    		<strong class="keyvals__key text text--strong -padB0@sm"><?= __('Home Phone'); ?>:</strong>
    		<span class="keyvals__val text text--mute -padT0@sm">
    			<?=!empty($lender['home_phone']) ? Format::htmlspecialchars($lender['home_phone']) : '-'; ?>
    		</span>
    	</div>

    	<div class="keyvals__row keyvals__row--rows@sm">
    		<strong class="keyvals__key text text--strong -padB0@sm"><?= __('Cell Phone'); ?>:</strong>
    		<span class="keyvals__val text text--mute -padT0@sm">
    			<?=!empty($lender['cell_phone']) ? Format::htmlspecialchars($lender['cell_phone']) : '-'; ?>
    		</span>
    	</div>

    	<div class="keyvals__row keyvals__row--rows@sm">
    		<strong class="keyvals__key text text--strong -padB0@sm"><?= __('Fax'); ?>:</strong>
    		<span class="keyvals__val text text--mute -padT0@sm">
    			<?=!empty($lender['fax']) ? Format::htmlspecialchars($lender['fax']) : '-'; ?>
    		</span>
    	</div>

    </div>

</div>