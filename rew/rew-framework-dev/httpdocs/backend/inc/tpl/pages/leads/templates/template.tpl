<?php

// Un-Authorized
if (!empty($preview)) {

?>

<div class="bar">
    <div class="bar__title"><?=Format::htmlspecialchars($template['name']); ?></div>
    <div class="bar__actions">
		<button class="bar__action btn btn--ghost" onclick="window.close()"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-close"></use></button>
    </div>

</div>

<div class="block -padH16"><textarea id="preview-template" cols="24" rows="12" readonly disabled><?=Format::htmlspecialchars($template['template']); ?></textarea></div>
<?php

	return;
}

?>
<form id="template-form" action="?submit" method="post" class="rew_check" enctype="multipart/form-data">

<div class="bar">
	<?php if (!empty($template['id'])) { ?>
	<div class="bar__title"><?=Format::htmlspecialchars($template['name']); ?></div>
	<input type="hidden" name="id" value="<?=$template['id']; ?>">
	<?php } else { ?>
	<div class="bar__title">Add Template</div>
	<?php } ?>
    <div class="bar__actions">
		<a class="bar__action" href="<?=URL_BACKEND; ?>leads/templates/"><svg class="icon icon-left-a mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></a>
    </div>
</div>

<div class="block">

	<div class="field">
		<label class="field__label">Template Name <em class="required">*</em></label>
		<input class="w1/1" type="text" name="name" value="<?=htmlspecialchars($template['name']); ?>" required>
	</div>
	<div class="field">
		<label class="field__label">Message</label>
		<textarea class="w1/1 tinymce email" id="template" name="template" rows="15" cols="80"><?=(!empty($template['template']) ? Format::htmlspecialchars($template['template']) : "#body#"); ?></textarea>
		<label class="hint">Tags: {first_name}, {last_name}, {email}, {signature}, {unsubscribe}, {verify}</label>
	</div>
	<?php if (!empty($can_share)) { ?>
	<div class="field">
		<label class="field__label">Share with Agents</label>
		<div class="toggle">
			<input id="share_true" type="radio" name="share" value="true"<?=($template['share'] == 'true') ? ' checked': ''; ?>>
			<label class="toggle__label" for="share_true">Yes</label>
			<input id="share_false" type="radio" name="share" value="false"<?=($template['share'] != 'true') ? ' checked': ''; ?>>
			<label class="toggle__label" for="share_false">No</label>
		</div>
	</div>
	<?php } ?>


	<div class="btns btns--stickyB"> <span class="R">
		<button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> Save</button>
		</span>
    </div>

</div>

</form>