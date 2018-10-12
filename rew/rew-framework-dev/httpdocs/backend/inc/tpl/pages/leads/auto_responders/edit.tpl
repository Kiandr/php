<form action="?submit" method="post" class="rew_check">
	<input type="hidden" name="id" value="<?=$autoresponder['id']; ?>">

<div class="bar">
    <div class="bar__title"><?=$autoresponder['title']; ?></div>
    <div class="bar__actions">
        <a class="bar__action" href="/backend/leads/auto_responders/"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
    </div>
</div>

<div class="btns btns--stickyB"> <span class="R">
	<button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?></button>
	</span>
</div>

<div class="block">

	<h3 class="panel__hd"><?= __('Sender Information'); ?></h3>
	<div class="field">
        <div class="toggle--stacked">
			<input id="from_admin" type="radio" name="from" value="admin"<?=($autoresponder['from'] == 'admin') ? ' checked' : ''; ?>>
			<label  class="toggle__label" for="from_admin">
				<?=Format::htmlspecialchars($super_admin['first_name']); ?>
				<?=Format::htmlspecialchars($super_admin['last_name']); ?>
			</label>
        </div>
        <div class="toggle--stacked">
			<input id="from_agent" type="radio" name="from" value="agent"<?=($autoresponder['from'] == 'agent') ? ' checked' : ''; ?>>
			<label class="toggle__label" for="from_agent"><?= __('Assigned Agent'); ?></label>
        </div>
        <div class="toggle--stacked">
			<input id="from_custom" type="radio" name="from" value="custom"<?=($autoresponder['from'] == 'custom') ? ' checked' : ''; ?>>
			<label class="toggle__label" for="from_custom"><?= __('Custom'); ?></label>
        </div>
	</div>
    <div id="autoresponder-from"<?=($autoresponder['from'] == 'custom') ? '' : ' class="hidden"'; ?>>
        <div class="field">
            <label class="field__label"><?= __('Sender Name'); ?> <em class="required">*</em></label>
            <input class="w1/1" name="from_name" value="<?=htmlspecialchars($autoresponder['from_name']); ?>">
        </div>
        <div class="field">
            <label class="field__label"><?= __('Sender Email'); ?> <em class="required">*</em></label>
            <input class="w1/1" type="email" name="from_email" value="<?=htmlspecialchars($autoresponder['from_email']); ?>">
        </div>
    </div>
	<div id="emailCC" class="field <?=empty($autoresponder['cc_email']) ? ' hidden' : ''; ?>">
	<label class="field__label"><?= __('CC'); ?></label>
	<input class="w1/1" type="email" name="cc_email" value="<?=htmlspecialchars($autoresponder['cc_email']); ?>">
	</div>
	<div id="emailBCC" class="field <?=empty($autoresponder['bcc_email']) ? ' hidden' : ''; ?>">
	<label class="field__label"><?= __('BCC'); ?></label>
	<input class="w1/1" type="email" name="bcc_email" value="<?=htmlspecialchars($autoresponder['bcc_email']); ?>">
	</div>
	<p class="form_display_control">
		<?php if (empty($autoresponder['cc_email'])) : ?>
		<a id="addCC" href="javascript:void(0);"><?= __('Add CC'); ?></a>
		<?php endif; ?>
		<?php if (empty($autoresponder['bcc_email'])) : ?>
		<a id="addBCC" href="javascript:void(0);"><?= __('Add BCC'); ?></a>
		<?php endif; ?>
	</p>
	<h3 class="panel__hd"><?= __('Email Settings'); ?></h3>
	<div class="field">
		<label class="field__label"><?= __('Template (optional)'); ?></label>
		<select class="w1/1" id="tempid" name="tempid">
			<option value=""><?= __('No Template'); ?></option>
			<?php foreach ($templates as $template) :?>
			<option value="<?=$template['id']; ?>"<?=(($autoresponder['tempid'] == $template['id']) ? ' selected="selected"' : ''); ?>>
			<?=$template['name']; ?>
			</option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="field">
		<label class="field__label"><?= __('HTML Email'); ?></label>
		<div class="toggle">
			<input id="is_html_true" type="radio" name="is_html" class="is_html" value="true"<?=(($autoresponder['is_html'] != 'false') ? ' checked="checked"' : ''); ?>>
			<label class="toggle__label" for="is_html_true"><?= __('Yes'); ?></label>
			<input id="is_html_false" type="radio" name="is_html" class="is_html" value="false"<?=(($autoresponder['is_html'] == 'false') ? ' checked="checked"' : ''); ?>>
			<label class="toggle__label" for="is_html_false"><?= __('No'); ?></label>
		</div>
	</div>
	<h3><?= __('Email Message'); ?></h3>
	<div class="field">
		<label class="field__label"><?= __('Subject'); ?> <em class="required">*</em></label>
		<input class="w1/1" type="text" name="subject" value="<?=htmlspecialchars($autoresponder['subject']); ?>" required>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Message'); ?> <em class="required">*</em></label>
		<?php $tinymce = (($autoresponder['tempid'] > 0) || ($autoresponder['is_html'] != 'false')) ? '' : ' off'; ?>
		<textarea class="w1/1 tinymce email<?=$tinymce; ?>" name="document" rows="15" cols="80"><?=htmlspecialchars($autoresponder['document']); ?></textarea>
		<label class="hint"><?= __('Tags'); ?>: {first_name}, {last_name}, {email}, {signature}, {verify}</label>
	</div>
	<h3 class="panel__hd"><?= __('Settings'); ?></h3>
	<div class="field">
		<label class="field__label"><?= __('Active'); ?></label>
		<div class="toggle">
			<input type="radio" id="active_Y" name="active" value="Y" <?=(($autoresponder['active'] != 'N') ? ' checked="checked"' : ''); ?>>
			<label class="toggle__label"  for="active_Y"><?= __('Yes'); ?></label>
			<input type="radio" id="active_N" name="active" value="N" <?=(($autoresponder['active'] == 'N') ? ' checked="checked"' : ''); ?>>
			<label class="toggle__label" for="active_N"><?= __('No'); ?></label>
		</div>
	</div>

</div>

</form>