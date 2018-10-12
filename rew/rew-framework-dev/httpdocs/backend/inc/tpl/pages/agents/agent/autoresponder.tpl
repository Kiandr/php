<?php
// Render agent summary header (menu/title/preview)
echo $this->view->render('inc/tpl/partials/agent/summary.tpl.php', [
    'title' => __('Auto-Responder'),
    'agent' => $agent,
    'agentAuth' => $agentAuth
]);
?>
<form action="?submit" method="post" class="rew_check">
	<input type="hidden" name="id" value="<?=Format::htmlspecialchars($agent['id']); ?>">

        <div class="block">

		<div class="field">
			<label class="field__label"><?= __('Subject'); ?> <em class="required">*</em></label>
			<input class="w1/1" type="text" name="ar_subject" value="<?=Format::htmlspecialchars($agent['ar_subject']); ?>" required>
		</div>

		<div class="field">
			<label class="field__label"><?= __('Message'); ?> <em class="required">*</em></label>
			<?php $tinymce = (!empty($agent['ar_tempid']) || ($agent['ar_is_html'] != 'false')) ? '' : ' off'; ?>
			<textarea class="w1/1 tinymce email<?=$tinymce; ?>" id="document" name="ar_document" rows="15" cols="80"><?=Format::htmlspecialchars($agent['ar_document']); ?></textarea>
			<label class="hint"><?= __('Tags:'); ?> {first_name}, {last_name}, {email}, {signature}</label>
		</div>

		<div class="field">
			<label class="field__label"><?= __('CC'); ?></label>
			<input class="w1/1" type="email" name="ar_cc_email" value="<?=Format::htmlspecialchars($agent['ar_cc_email']); ?>">
		</div>

		<div class="field">
			<label class="field__label"><?= __('BCC'); ?></label>
			<input class="w1/1" type="email" name="ar_bcc_email" value="<?=Format::htmlspecialchars($agent['ar_bcc_email']); ?>">
		</div>

		<div class="field">
			<label class="field__label"><?= __('Active'); ?></label>
			<div>
				<label><input type="radio" name="ar_active" value="Y" <?=(($agent['ar_active'] != 'N') ? ' checked="checked"' : ''); ?>> <?= __('Yes'); ?></label>
				<label><input type="radio" name="ar_active" value="N" <?=(($agent['ar_active'] == 'N') ? ' checked="checked"' : ''); ?>> <?= __('No'); ?></label>
			</div>
		</div>

		<div class="field">
			<label class="field__label"><?= __('HTML Email'); ?></label>
			<div>
				<label><input type="radio" name="ar_is_html" value="true"<?=($agent['ar_is_html'] != 'false' ? ' checked="checked"' : ''); ?>> <?= __('Yes'); ?></label>
				<label><input type="radio" name="ar_is_html" value="false"<?=($agent['ar_is_html'] == 'false' ? ' checked="checked"' : ''); ?>> <?= __('No'); ?></label>
			</div>
		</div>

		<?php if (!empty($templates)) : ?>
		<div class="field">
			<label class="field__label"><?= __('Template'); ?></label>
			<select name="ar_tempid">
				<option value=""><?= __('No Template'); ?></option>
				<?php foreach ($templates as $template) :?>
				<option value="<?=$template['id']; ?>"<?=(($agent['ar_tempid'] == $template['id']) ? ' selected="selected"' : ''); ?>>
				<?=$template['name']; ?>
				</option>
				<?php endforeach; ?>
			</select>
		</div>
		<?php endif; ?>

		<div class="btns btns--stickyB">
			<span class="R">
				<button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?></button>
			</span>
		</div>
    </div>
</form>
