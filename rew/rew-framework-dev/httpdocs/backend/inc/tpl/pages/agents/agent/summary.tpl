<?php
// Render agent summary header (menu/title/preview)
echo $this->view->render('inc/tpl/partials/agent/summary.tpl.php', [
    'title' => __('Agent Summary'),
    'agent' => $agent,
    'agentAuth' => $agentAuth
]);
?>

<div class="block">

    <div class="keyvals keyvals--bordered -marB">
        <?php if(!empty($lead['phone'])) { ?><div class="keyvals__row keyvals__row--rows@sm"><span class="keyvals__key text text--strong -padB0@sm">Phone</span><span class="keyvals__val text text--mute"><?=Format::htmlspecialchars($agent['phone']);?></span></div><?php } ?>
        <div class="keyvals__row keyvals__row--rows@sm">
            <span class="keyvals__key text text--strong -padB0@sm"><?= __('Email'); ?></span>
            <span class="keyvals__val text text--mute">
                <a href="<?=URL_BACKEND; ?>email/?id=<?=$agent['id'] ;?>&type=agents"><?=Format::htmlspecialchars($agent['email']); ?></a>
            </span>
        </div>
    </div>

    <?php if (!empty($leads)) { ?>

    <div class="divider -marB"><span class="divider__label divider__label--left text text--mute text--small"><?= __('Leads'); ?></span></div>

    <div class="keyvals keyvals--columns text--center -marB">
        <div class="keyvals__row keyvals__row--rows">
            <span class="keyvals__key text text--small text--mute"><?= __('Total'); ?></span>
            <a href="<?=URL_BACKEND; ?>leads/?submit=true&amp;agents[]=<?=$agent['id']; ?>" class="keyvals__val text text--strong"><?=number_format($leads['total']);?></a>
        </div>
        <div class="keyvals__row keyvals__row--rows">
            <span class="keyvals__key text text--small text--mute"><?= __('Accepted'); ?></span>
            <a href="<?=URL_BACKEND; ?>leads/?submit=true&amp;agents[]=<?=$agent['id']; ?>&amp;status=accepted" class="keyvals__val text text--strong"><?=number_format($leads['accepted']); ?></a>
        </div>
        <div class="keyvals__row keyvals__row--rows">
            <span class="keyvals__key text text--small text--mute"><?= __('Pending'); ?></span>
            <a href="<?=URL_BACKEND; ?>leads/?submit=true&amp;agents[]=<?=$agent['id']; ?>&amp;status=pending" class="keyvals__val text text--strong"><?=number_format($leads['pending']); ?></a>
        </div>
        <div class="keyvals__row keyvals__row--rows">
            <span class="keyvals__key text text--small text--mute"><?= __('Rejected'); ?></span>
            <a href="<?=URL_BACKEND; ?>leads/?submit=true&amp;agents[]=<?=$agent['id']; ?>&amp;status=rejected" class="keyvals__val text text--strong"><?=number_format($leads['rejected']); ?></a>
        </div>
    </div>

    <?php } ?>

    <div class="divider -marB"><span class="divider__label divider__label--left text text--mute text--small"><?= __('Basics'); ?></span></div>


<div class="keyvals keyvals--bordered marB">

<div class="keyvals__row keyvals__row--rows@sm">
<span class="keyvals__key text text--strong -padB0@sm"><?= __('Timezone:'); ?></span> <span class="keyvals__val text -padT0@sm">
	<?=$timezone; ?>
	</span> </div>
<?php if (!empty(Settings::getInstance()->MODULES['REW_AGENT_MANAGER']) || !empty($_COMPLIANCE['backend']['always_show_idx_agent'])) : ?>
<?php if (!empty(Settings::getInstance()->IDX_FEEDS)) { ?>
<?php foreach (Settings::getInstance()->IDX_FEEDS as $feed => $settings) { ?>
<div class="keyvals__row keyvals__row--rows@sm"> <span class="keyvals__key text text--strong -padB0@sm">
	<?=Format::htmlspecialchars($settings['title']); ?>
	<?= __('Agent ID'); ?></span> <span class="keyvals__val text -padT0@sm">
	<?=!empty($agent['agent_id'][$feed]) ? $agent['agent_id'][$feed] : '-'; ?>
	</span> </div>
<?php } ?>
<?php } else { ?>
<div class="keyvals__row keyvals__row--rows@sm"> <span class="keyvals__key text text--strong -padB0@sm"><?= __('Agent ID:'); ?></span> <span class="keyvals__val text -padT0@sm">
	<?=!empty($agent['agent_id'][Settings::getInstance()->IDX_FEED]) ? $agent['agent_id'][Settings::getInstance()->IDX_FEED] : '-'; ?>
	</span> </div>
<?php } ?>
<?php if (!empty(Settings::getInstance()->MODULES['REW_AGENT_MANAGER'])) { ?>
<?php if (!empty($agent['website'])) { ?>
<?php $agent['website'] = preg_match('/^https?:\/\//', $agent['website']) ? $agent['website'] : 'http://' . $agent['website']; ?>
<div class="keyvals__row keyvals__row--rows@sm">
<span class="keyvals__key text text--strong -padB0@sm"><?= __('Website:'); ?></span>
<span class="keyvals__val text -padT0@sm"><a href="<?=$agent['website']; ?>" target="_blank">
	<?=$agent['website']; ?>
	</a></span>
</div>
<?php } ?>
<?php } ?>
<?php endif; ?>
<?php if (!empty($office)) { ?>
<div class="keyvals__row keyvals__row--rows@sm"> <span class="keyvals__key text text--strong -padB0@sm"><?= __('Office'); ?></span> <span class="keyvals__val text -padT0@sm">
	<?=Format::htmlspecialchars($office['title']); ?>
	</span> </div>
<?php } ?>

</div>

    <div class="divider -marB"><span class="divider__label divider__label--left text text--mute text--small"><?= __('Contact Details'); ?></span></div>

<div class="keyvals keyvals--bordered marB">


<?php if (!empty($can_email)) { ?>
    <div class="keyvals__row keyvals__row--rows@sm">
        <span class="keyvals__key text text--strong -padB0@sm"><?= __('Email Address:'); ?></span>
        <span class="keyvals__val text -padT0@sm">
            <?=$agent['email'] ? sprintf('<a href="%semail?id=%s&type=agents">%s</a>', URL_BACKEND, $agent['id'], $agent['email']) : '-'; ?>
        </span>
    </div>
<?php } ?>

<?php if (!empty($agent['sms_email']) && !empty($can_email)) { ?>
<div class="keyvals__row keyvals__row--rows@sm"> <span class="keyvals__key text text--strong -padB0@sm"><?= __('SMS Email Address:'); ?></span> <span class="keyvals__val text -padT0@sm"><a href="mailto:<?=Format::htmlspecialchars($agent['sms_email']); ?>">
	<?=!empty($agent['sms_email']) ? Format::htmlspecialchars($agent['sms_email']) : '-'; ?>
	</a></span> </div>
<?php } ?>
<div class="keyvals__row keyvals__row--rows@sm"> <span class="keyvals__key text text--strong -padB0@sm"><?= __('Office Phone:'); ?></span> <span class="keyvals__val text -padT0@sm">
	<?=!empty($agent['office_phone']) ? Format::htmlspecialchars($agent['office_phone']) : '-'; ?>
	</span> </div>
<div class="keyvals__row keyvals__row--rows@sm"> <span class="keyvals__key text text--strong -padB0@sm"><?= __('Home Phone:'); ?></span> <span class="keyvals__val text -padT0@sm">
	<?=!empty($agent['home_phone']) ? Format::htmlspecialchars($agent['home_phone']) : '-'; ?>
	</span> </div>
<div class="keyvals__row keyvals__row--rows@sm"> <span class="keyvals__key text text--strong -padB0@sm"><?= __('Cell Phone:'); ?></span> <span class="keyvals__val text -padT0@sm">
	<?=!empty($agent['cell_phone']) ? Format::htmlspecialchars($agent['cell_phone']) : '-'; ?>
	</span> </div>
<div class="keyvals__row keyvals__row--rows@sm"> <span class="keyvals__key text text--strong -padB0@sm"><?= __('Fax:'); ?></span> <span class="keyvals__val text -padT0@sm">
	<?=!empty($agent['fax']) ? Format::htmlspecialchars($agent['fax']) : '-'; ?>
	</span>
</div>

</div>

<?php if ($agentAuth->canManageAgent()) { ?>

<div class="divider -marB"><span class="divider__label divider__label--left text text--mute text--small"><?= __('Advanced'); ?></span></div>

<div class="keyvals keyvals--bordered marB">
<?php if ($settings['auto_assign'] == 'true') { ?>
<div class="keyvals__row keyvals__row--rows@sm"> <span class="keyvals__key text text--strong -padB0@sm"><?= __('Auto-Assign'); ?></span> <span class="keyvals__val text -padT0@sm">
	<?=($agent['auto_assign_admin'] == 'true') ? 'Yes' : 'No'; ?>
	</span> </div>
<div class="keyvals__row keyvals__row--rows@sm"> <span class="keyvals__key text text--strong -padB0@sm"><?= __('Auto-Assign Opt-In'); ?></span> <span class="keyvals__val text -padT0@sm">
	<?=($agent['auto_assign_agent'] == 'true') ? 'Yes' : 'No'; ?>
	</span> </div>
<?php } ?>
<?php if ($settings['auto_rotate'] == 'true') { ?>
<div class="keyvals__row keyvals__row--rows@sm"> <span class="keyvals__key text text--strong -padB0@sm"><?= __('Auto-Rotate'); ?></span> <span class="keyvals__val text -padT0@sm">
	<?=($agent['auto_rotate'] == 'true') ? 'Yes' : 'No'; ?>
	</span> </div>
<?php } ?>
<?php if (!empty(Settings::getInstance()->MODULES['REW_AGENT_MANAGER'])) { ?>
<div class="keyvals__row keyvals__row--rows@sm"> <span class="keyvals__key text text--strong -padB0@sm"><?= __('Show on Agents Page'); ?></span> <span class="keyvals__val text -padT0@sm">
	<?=(($agent['display'] == 'Y') ? 'Yes' : 'No'); ?>
	</span> </div>
<?php if (!empty(Settings::getInstance()->MODULES['REW_AGENT_SPOTLIGHT'])) { ?>
<div class="keyvals__row keyvals__row--rows@sm"> <span class="keyvals__key text text--strong -padB0@sm"><?= __('Display on Agents Feature'); ?></span> <span class="keyvals__val text -padT0@sm">
	<?=($agent['display_feature'] == 'Y') ? 'Yes' : 'No'; ?>
	</span> </div>
<?php } ?>
<?php } ?>
</div>

<?php if (!empty(Settings::getInstance()->MODULES['REW_AGENT_CMS']) && ($agent['id'] != 1)) { ?>


<div class="divider -marB"><span class="divider__label divider__label--left text text--mute text--small"><?= __('Agent CMS'); ?></span></div>

<div class="keyvals keyvals--bordered marB">
<div class="keyvals__row keyvals__row--rows@sm"> <span class="keyvals__key text text--strong -padB0@sm"><?= __('Enabled:'); ?></span> <span class="keyvals__val text -padT0@sm">
	<?=($agent['cms'] == 'true') ? 'Yes' : 'No'; ?>
	</span> </div>
<?php if ($agent['cms'] == 'true') { ?>
<div class="keyvals__row keyvals__row--rows@sm"> <span class="keyvals__key text text--strong -padB0@sm"><?= __('Website:'); ?></span> <span class="keyvals__val text -padT0@sm"><a href="<?=sprintf(URL_AGENT_SITE, $agent['cms_link']); ?>" target="_blank">
	<?=sprintf(URL_AGENT_SITE, $agent['cms_link']); ?>
	</a></span> </div>
<div class="keyvals__row keyvals__row--rows@sm">
    <span class="keyvals__key text text--strong -padB0@sm"><?=__('Enabled Addons:'); ?></span>
    <span class="keyvals__val text -padT0@sm"><?=ucwords(implode(', ', $agent['cms_addons'])); ?></span>
</div>
<?php } ?>
<?php } ?>
<?php } ?>

</div>
</div>

