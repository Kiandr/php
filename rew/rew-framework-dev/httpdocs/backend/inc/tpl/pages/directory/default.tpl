<form action="?submit" method="post" class="rew_check">
	<h2>Directory Settings</h2>
	<div class="field">
		<label class="field__label">Directory Name <em class="required">*</em></label>
		<input class="w1/1" type="text" name="directory_name" value="<?=htmlspecialchars($directory_settings['directory_name']); ?>" required>
		<p class="tip">This will be used as the heading (H1) on the main directory page.</p>
	</div>
	<div class="field">
		<label class="field__label">Page Title</label>
		<input class="w1/1" type="text" name="page_title" value="<?=htmlspecialchars($directory_settings['page_title']); ?>">
		<p class="tip">
			<?=tpl_lang('DESC_FORM_DIRECTORY_TITLE'); ?>
		</p>
	</div>
	<div class="field">
		<label class="field__label">Meta Keywords</label>
		<textarea class="w1/1" id="meta_tag_keywords" name="meta_tag_keywords" cols="24" rows="2"><?=htmlspecialchars($directory_settings['meta_tag_keywords']); ?></textarea>
		<p class="tip">
			<?=tpl_lang('DESC_FORM_DIRECTORY_KEYWORDS'); ?>
		</p>
	</div>
	<div class="field">
		<label class="field__label">Meta Description</label>
		<textarea class="w1/1" id="meta_tag_desc" name="meta_tag_desc" cols="24" rows="2"><?=htmlspecialchars($directory_settings['meta_tag_desc']); ?></textarea>
		<p class="tip">
			<?=tpl_lang('DESC_FORM_DIRECTORY_DESCRIPTION'); ?>
		</p>
	</div>
	<?php if (!empty($features)) : ?>
	<div class="field">
		<label class="field__label">Featured Content</label>
		<div class="toggleset gridded">
			<?php foreach ($features as $feature) : ?>
			<label class="boolean">
				<input type="checkbox" name="features[]" value="<?=$feature['value']; ?>"<?=(is_array($directory_settings['features']) && in_array($feature['value'], $directory_settings['features'])) ? ' checked="checked"' : ''; ?>>
				<?=$feature['title']; ?>
			</label>
			<?php endforeach; ?>
		</div>
	</div>
	<?php endif; ?>
	<div class="field">
		<label class="field__label">Sitemap Display</label>
		<div class="buttonset radios">
			<input type="radio" name="sitemap" id="sitemap_true" value="cat"<?=($directory_settings['sitemap'] == 'cat') ? ' checked="checked"' : ''; ?>>
			<label for="sitemap_true">Category</label>
			<input type="radio" name="sitemap" id="sitemap_false" value="list"<?=($directory_settings['sitemap'] == 'list') ? ' checked="checked"' : ''; ?>>
			<label for="sitemap_false">Listings</label>
		</div>
		<p class="tip">"Category" display will show links to all the directory's categories (and a random set of listings) and is only recommended if you are using a three-level category structure.<br />
			"Listings" display will show links to a larger selection of listings, and is recommended for everyone using only two levels of category.</p>
	</div>
	<?php if (isset(Settings::getInstance()->MODULES['REW_HIDE_SLIDESHOW']) && !empty(Settings::getInstance()->MODULES['REW_HIDE_SLIDESHOW'])) : ?>
	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_HIDE_SLIDESHOW'); ?>
		</label>
		<label class="boolean">
			<input type="checkbox" name="hide_slideshow" value="t"<?=($directory_settings['hide_slideshow'] == 't') ? ' checked="checked"' : ''; ?>>
		</label>
	</div>
	<?php endif; ?>
</form>