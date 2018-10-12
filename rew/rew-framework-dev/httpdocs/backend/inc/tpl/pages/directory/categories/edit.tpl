<form action="?submit" method="post" class="rew_check">
	<input type="hidden" name="id" value="<?=$edit_category['id']; ?>">


	<div class="main-header">
		<h2>Edit Directory Category</h2>
		<div class="btns R">
			<a href="/backend/directory/categories/" class="btn btn--ghost"><svg class="icon icon-left-a mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
		</div>
	</div>

	<div class="btns btns--stickyB"> <span class="R">
		<button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> Save</button>
		</span> </div>
	<div class="field">
		<label class="field__label">Category Title <em class="required">*</em></label>
		<input class="w1/1" type="text" name="title" value="<?=htmlspecialchars($edit_category['title']); ?>" required>
	</div>
	<div class="field">
		<label class="field__label">Category Level</label>
		<select class="w1/1" name="parent">
			<option value="">----- Top Level -----</option>
			<?php foreach ($categories as $category) : ?>
			<?php $selected  = ($category['link'] == $edit_category['parent']) ? ' selected="selected"' : ''; ?>
			<?php $selected .= ($category['parent'] == '') ? ' style="background-color: #D1DFEF;"' : ' style="background-color: #DFF1FF;"'; ?>
			<option value="<?=$category['link']; ?>"<?=$selected; ?>>
			<?=$category['title']; ?>
			</option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="field">
		<label class="field__label">Category Content</label>
		<textarea class="w1/1" class="tinymce simple" id="category_content" name="category_content" cols="24" rows="15">
		<?=htmlspecialchars($edit_category['category_content']); ?>
		</textarea>
	</div>
	<h3>Meta Information</h3>
	<div class="field">
		<label class="field__label">Page Title</label>
		<input class="w1/1" type="text" name="page_title" value="<?=htmlspecialchars($edit_category['page_title']); ?>">
	</div>
	<div class="field">
		<label class="field__label">Meta Keywords</label>
		<textarea class="w1/1" id="meta_tag_keywords" name="meta_tag_keywords" cols="24" rows="2"><?=htmlspecialchars($edit_category['meta_tag_keywords']); ?>
</textarea>
		<p class="tip">
			<?=tpl_lang('DESC_FORM_DIRECTORY_KEYWORDS'); ?>
		</p>
	</div>
	<div class="field">
		<label class="field__label">Meta Description</label>
		<textarea class="w1/1" id="meta_tag_desc" name="meta_tag_desc" cols="24" rows="2"><?=htmlspecialchars($edit_category['meta_tag_desc']); ?>
</textarea>
		<p class="tip">
			<?=tpl_lang('DESC_FORM_DIRECTORY_DESCRIPTION'); ?>
		</p>
	</div>
	<?php if (!empty($related_categories)) : ?>
	<h3>Related Categories</h3>
	<?php foreach ($related_categories as $category) : ?>
	<?php $checked = (is_array($edit_category['related_categories']) && in_array($category['link'], $edit_category['related_categories'])) ? ' checked' : ''; ?>
	<label class="field__label">
		<input type="checkbox" name="related_categories[]" value="<?=$category['link']; ?>"<?=$checked; ?>>
		<?=$category['title']; ?>
	</label>
	<?php endforeach; ?>
	<?php endif; ?>
</form>
