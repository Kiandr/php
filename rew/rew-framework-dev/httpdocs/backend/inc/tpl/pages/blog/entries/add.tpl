<form action="?submit" method="post" class="rew_check">

	<div class="bar">
		<div class="bar__title"><?= __('New Blog Entry'); ?></div>
		<div class="bar__actions">
			<a href="/backend/blog/entries/" class="bar__action"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
		</div>
	</div>

	<div class="block">

		<div class="btns btns--stickyB"> <span class="R">
			<button class="btn btn--positive"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?> </button>
			</span> </div>
		<div class="field">
			<label class="field__label"><?= __('Title'); ?> <em class="required">*</em></label>
			<input class="w1/1" type="text" name="title" value="<?=Format::htmlspecialchars($_POST['title']); ?>" required>
		</div>
		<div class="field">
			<label class="field__label"><?= __('Body'); ?></label>
			<textarea class="w1/1 tinymce" id="body" name="body" cols="24" rows="25"><?=Format::htmlspecialchars($_POST['body']); ?></textarea>
		</div>
		<div class="field">
			<label class="field__label"><?= __('Tags'); ?></label>
			<textarea class="w1/1" name="tags" cols="24" rows="4"><?=Format::htmlspecialchars($_POST['tags']); ?></textarea>
			<p class="text--mute">
				<?=tpl_lang('DESC_FORM_BLOG_TAGS'); ?>
			</p>
		</div>
		<h3><?= __('Related Links'); ?></h3>
		<div class="cols">
			<div class="field col w1/2">
				<label class="field__label"><?= __('Link Title'); ?></label>
				<input class="w1/1" type="text" name="link_title1" value="<?=Format::htmlspecialchars($_POST['link_title1']); ?>">
			</div>
			<div class="field col w1/2">
				<label class="field__label"><?= __('Link URL'); ?></label>
				<input class="w1/1" type="url" name="link_url1" value="<?=Format::htmlspecialchars($_POST['link_url1']); ?>" placeholder="http://" pattern="https?://.+">
			</div>
			<div class="field col w1/2">
				<label class="field__label"><?= __('Link Title'); ?></label>
				<input class="w1/1" type="text" name="link_title2" value="<?=Format::htmlspecialchars($_POST['link_title2']); ?>">
			</div>
			<div class="field col w1/2">
				<label class="field__label"><?= __('Link URL'); ?></label>
				<input class="w1/1" type="url" name="link_url2" value="<?=Format::htmlspecialchars($_POST['link_url2']); ?>" placeholder="http://" pattern="https?://.+">
			</div>
			<div class="field col w1/2">
				<label class="field__label"><?= __('Link Title'); ?></label>
				<input class="w1/1" type="text" name="link_title3" value="<?=Format::htmlspecialchars($_POST['link_title3']); ?>">
			</div>
			<div class="field col w1/2">
				<label class="field__label"><?= __('Link URL'); ?></label>
				<input class="w1/1" type="url" name="link_url3" value="<?=Format::htmlspecialchars($_POST['link_url3']); ?>" placeholder="http://" pattern="https?://.+">
			</div>
		</div>
		<p class="text--mute">
			<?=tpl_lang('DESC_FORM_BLOG_RELATED_LINKS'); ?>
		</p>
		<h3><?= __('Published'); ?></h3>
		<div class="field toggle">
			<input type="radio" name="published" id="published_true" value="true"<?=($_POST['published'] == 'true') ? ' checked' : ''; ?>>
			<label class="toggle__label" for="published_true"><?= __('Yes'); ?></label>
			<input type="radio" name="published" id="published_false" value="false"<?=($_POST['published'] == 'false') ? ' checked' : ''; ?>>
			<label class="toggle__label" for="published_false"><?= __('No'); ?></label>
		</div>
		<div id="publish-date"<?=($_POST['published'] == 'true') ? '' : ' class="hidden"'; ?>>
		<h3><?= __('Date Published'); ?></h3>
		<div class="field">
			<?php $published_timestamp = (empty($_POST['timestamp_published']) || $_POST['timestamp_published'] == '0000-00-00 00:00:00') ? '' : Format::htmlspecialchars($_POST['timestamp_published']); ?>
			<?php $published_timestamp = is_numeric($published_timestamp) ? date('l, F j, Y g:ia', $_POST['timestamp_published']) : $published_timestamp; ?>
			<input class="w1/1" type="text" name="timestamp_published" value="<?=$published_timestamp; ?>" placeholder="<?= __('Publish Date'); ?>">
		</div>
		<?php if (!empty($categories) && is_array($categories)) { ?>
		<h3><?= __('Categories'); ?></h3>

		<?php foreach ($categories as $category) { ?>
			<label class="toggle toggle--stacked">
				<input<?=in_array($category['link'], $_POST['categories']) ? ' checked' : ''; ?> type="checkbox" name="categories[]" value="<?=$category['link']; ?>">
				<span class="toggle__label"><?=Format::htmlspecialchars($category['title']); ?></span>
			</label>
			<?php if (!empty($category['subcategories'])) { ?>
			<ul class="toggle toggle--stacked" style="list-style: none; margin-top: 0; padding-left: 20px;">
				<?php foreach ($category['subcategories'] as $subcategory) { ?>
				<li>
					<label>
						<input<?=in_array($subcategory['link'], $_POST['categories']) ? ' checked' : ''; ?> type="checkbox" name="categories[]" value="<?=$subcategory['link']; ?>">
						<span class="toggle__label"><?=Format::htmlspecialchars($subcategory['title']); ?></span>
					</label>
				</li>
				<?php } ?>
			</ul>
			<?php } ?>
		<?php } ?>

		<?php } ?>
		<h3><?= __('Meta Information'); ?></h3>
		<div class="field">
			<label class="field__label"><?= __('Description'); ?></label>
			<textarea class="w1/1" id="meta_tag_desc" name="meta_tag_desc" cols="24" rows="6"><?=Format::htmlspecialchars($_POST['meta_tag_desc']); ?></textarea>
			<p class="text--mute">
				<?=tpl_lang('DESC_FORM_BLOG_DESCRIPTION'); ?>
			</p>
		</div>
		<div class="field">
			<label class="field__label"><?=tpl_lang('LBL_FORM_OG_IMAGE'); ?></label>
			<div data-uploader='<?=json_encode([
				'inputName' => 'og_image[]',
				'placeholder' => __('Upload photo'),
				'extraParams' => ['type' => 'blog:og:image']
			]); ?>'>
				<?php if (!empty($og_image)) { ?>
				<div class="file-manager">
					<ul>
						<?php foreach ($og_image as $image) { ?>
						<li>
							<div class="wrap"> <img src="/thumbs/95x95/uploads/<?=urlencode($image['file']); ?>" border="0">
								<input type="hidden" name="og_image[]" value="<?=$image['id']; ?>" class="skip-check">
							</div>
						</li>
						<?php } ?>
					</ul>
				</div>
				<?php } ?>
			</div>
			<p class="text--mute">
				<?=tpl_lang('DESC_FORM_OG_IMAGE'); ?>
			</p>
		</div>
		</div>
	</div>
</form>
