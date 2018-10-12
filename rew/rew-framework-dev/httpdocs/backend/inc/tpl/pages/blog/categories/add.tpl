<div class="block">
<form action="?submit" method="post" class="rew_check">

	<div class="main-header">
		<h2><?= __('New Blog Category'); ?></h2>
		<div class="btns R">
			<a href="/backend/blog/categories/" class="btn btn--ghost"><svg class="icon icon-left-a mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
		</div>
	</div>

	<div class="field">
		<label class="field__label"><?= __('Title'); ?> <em class="required">*</em></label>
		<input class="w1/1" type="text" name="title" value="<?=Format::htmlspecialchars($_POST['title']); ?>" required>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Description'); ?></label>
		<textarea class="w1/1 tinymce simple" id="description" name="description" cols="24" rows="10"><?=Format::htmlspecialchars($_POST['description']); ?>
</textarea>
	</div>
	<?php if (!empty($categories)) { ?>
	<div class="field">
		<label class="field__label"><?= __('Category Level'); ?></label>
		<select class="w1/1" name="parent" id="parent">
			<option value=""><?= __('Main Category'); ?></option>
			<?php foreach ($categories as $category) { ?>
			<?php $selected = ($_POST['parent'] == $category['link']) ? ' selected' : ''; ?>
			<option value="<?=$category['link']; ?>"<?=$selected; ?>>
			<?=Format::htmlspecialchars($category['title']); ?>
			</option>
			<?php } ?>
		</select>
	</div>
	<?php } ?>
	<h3><?= __('Meta Information'); ?></h3>
	<div class="field">
		<label class="field__label"><?= __('Page Title'); ?></label>
		<input class="w1/1" type="text" name="page_title" value="<?=Format::htmlspecialchars($_POST['page_title']); ?>">
		<p class="text--mute">
			<?=tpl_lang('DESC_FORM_CMS_PAGE_TITLE'); ?>
		</p>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Meta Description'); ?> </label>
		<textarea class="w1/1" id="meta_tag_desc" name="meta_tag_desc" cols="24" rows="6"><?=Format::htmlspecialchars($_POST['meta_tag_desc']); ?></textarea>
		<p class="text--mute">
			<?=tpl_lang('DESC_FORM_CMS_PAGE_DESCRIPTION'); ?>
		</p>
	</div>
	<div class="btns btns--stickyB"> <span class="R">
		<button class="btn btn--positive"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?> </button>
		</span> </div>
</form>
</div>