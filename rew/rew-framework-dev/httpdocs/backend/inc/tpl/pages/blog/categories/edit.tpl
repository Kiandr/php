<div class="block">
<form action="?submit" method="post" class="rew_check">
	<input type="hidden" name="id" value="<?=$edit_category['id']; ?>">

	<div class="main-header">
		<h2><?=Format::htmlspecialchars($edit_category['title']); ?></h2>
		<div class="btns R">
			<a href="/backend/blog/categories/" class="btn btn--ico btn--ghost"><svg class="icon icon-left-a mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
		</div>
	</div>

	<div class="btns btns--stickyB"> <span class="R">
		<button class="btn btn--positive"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?> </button>
		</span> </div>
	<div class="field">
		<label class="field__label"><?= __('Title'); ?> <em class="required">*</em></label>
		<input class="w1/1" type="text" name="title" value="<?=Format::htmlspecialchars($edit_category['title']); ?>" required>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Description'); ?></label>
		<textarea class="w1/1 tinymce simple" id="description" name="description" cols="24" rows="10"><?=Format::htmlspecialchars($edit_category['description']); ?></textarea>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Category Level'); ?></label>
		<select class="w1/1" name="parent" id="parent">
			<option value=""><?= __('Main Category'); ?></option>
			<?php if (!empty($categories)) { ?>
			<?php foreach ($categories as $category) { ?>
			<?php $selected = ($edit_category['parent'] == $category['link']) ? ' selected' : ''; ?>
			<option value="<?=$category['link']; ?>"<?=$selected; ?>>
			<?=Format::htmlspecialchars($category['title']); ?>
			</option>
			<?php } ?>
			<?php } ?>
		</select>
	</div>
	<h3 class="divider">
        <span class="divider__label divider__label--left"><?= __('Meta Information'); ?></span>
    </h3>
	<div class="field">
		<label class="field__label"><?= __('Page Title'); ?> </label>
		<input class="w1/1" type="text" name="page_title" value="<?=Format::htmlspecialchars($edit_category['page_title']); ?>">
		<p class="text--mute">
			<?=tpl_lang('DESC_FORM_CMS_PAGE_TITLE'); ?>
		</p>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Meta Description'); ?></label>
		<textarea class="w1/1" id="meta_tag_desc" name="meta_tag_desc" cols="24" rows="6"><?=Format::htmlspecialchars($edit_category['meta_tag_desc']); ?></textarea>
		<p class="text--mute">
			<?=tpl_lang('DESC_FORM_CMS_PAGE_DESCRIPTION'); ?>
		</p>
	</div>
	</div>
</form>
</div>