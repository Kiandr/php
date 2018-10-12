<form action="?submit" method="post" class="rew_check">
	<input type="hidden" name="id" value="<?=$edit_entry['id']; ?>">

	<div class="bar">
		<div class="bar__title"><?=Format::htmlspecialchars($edit_entry['title']); ?></div>
		<div class="bar__actions">
			<a class="bar__action" href="<?=sprintf(URL_BLOG_ENTRY, $edit_entry['link']); ?>" target="_blank"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-view"></use></svg></a>
			<a class="bar__action timeline__back" href="<?='/backend/blog/entries/'; ?>"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
		</div>
	</div>

	<div class="block">

    	<?php if ($blogAuth->canManageEntries($authuser)) { ?>
    	<div class="field">
    		<label class="field__label" style="font-size: 16px;">
                <?= __('Blog Author:'); ?>

                <?php if (!empty($author)) { ?>
                <a href="<?=URL_BACKEND; ?>agents/agent/summary/?id=<?=$author['id']; ?>">
                <?=Format::htmlspecialchars($author['first_name']); ?>
                <?=Format::htmlspecialchars($author['last_name']); ?>
                </a>
                <?php } else { ?>
                <span><?= __('(Unknown)'); ?></span>
                <?php } ?>
            </label>
    	</div>
    	<?php } ?>
    	<div class="field">
    		<label class="field__label"><?= __('Entry Title'); ?> <em class="required">*</em></label>
    		<input class="w1/1" name="title" value="<?=Format::htmlspecialchars($edit_entry['title']); ?>" required>
    	</div>
    	<div class="field">
    		<label class="field__label"><?= __('Entry Body'); ?></label>
    		<textarea class="w1/1 tinymce" id="body" name="body" cols="24" rows="25"><?=Format::htmlspecialchars($edit_entry['body']); ?></textarea>
    	</div>
    	<div class="field">
    		<label class="field__label"><?= __('Blog Tags'); ?></label>
    		<textarea class="w1/1" name="tags" cols="24" rows="4"><?=Format::htmlspecialchars($edit_entry['tags']); ?></textarea>
    		<p class="text--mute">
    			<?=tpl_lang('DESC_FORM_BLOG_TAGS'); ?>
    		</p>
    	</div>
    	<h3><?= __('Related Links'); ?></h3>
        <div class="cols">
        	<div class="field col w1/2">
        		<label class="field__label"><?= __('Link Title'); ?></label>
        		<input class="w1/1" name="link_title1" value="<?=Format::htmlspecialchars($edit_entry['link_title1']); ?>">
        	</div>
        	<div class="field col w1/2">
        		<label class="field__label"><?= __('Link URL'); ?></label>
        		<input class="w1/1" type="url" name="link_url1" value="<?=Format::htmlspecialchars($edit_entry['link_url1']); ?>" placeholder="http://" pattern="https?://.+">
        	</div>
        	<div class="field col w1/2">
        		<label class="field__label"><?= __('Link Title'); ?></label>
        		<input class="w1/1" name="link_title2" value="<?=Format::htmlspecialchars($edit_entry['link_title2']); ?>">
        	</div>
        	<div class="field col w1/2">
        		<label class="field__label"><?= __('Link URL'); ?></label>
        		<input class="w1/1" type="url" name="link_url2" value="<?=Format::htmlspecialchars($edit_entry['link_url2']); ?>" placeholder="http://" pattern="https?://.+">
        	</div>
        	<div class="field col w1/2">
        		<label class="field__label"><?= __('Link Title'); ?></label>
        		<input class="w1/1" name="link_title3" value="<?=Format::htmlspecialchars($edit_entry['link_title3']); ?>">
        	</div>
        	<div class="field col w1/2">
        		<label class="field__label"><?= __('Link URL'); ?></label>
        		<input class="w1/1" type="url" name="link_url3" value="<?=Format::htmlspecialchars($edit_entry['link_url3']); ?>" placeholder="http://" pattern="https?://.+">
        	</div>
        </div>
        <p class="text--mute">
            <?=tpl_lang('DESC_FORM_BLOG_RELATED_LINKS'); ?>
        </p>
    	<?php if (!empty($edit_entry['snippets'])) { ?>
    	<h3><?= __('Snippets Used'); ?></h3>
    	<div class="field">
    		<ul class="checklist">
    			<?php foreach ($edit_entry['snippets'] as $snippet) { ?>
    			<?php if (in_array($snippet['type'], array('idx', 'form', 'cms'))) { ?>
    			<li><span class="doc-icon"><img alt="" src="<?=URL_BACKEND_IMAGES; ?>ico-snippet.gif"></span> <a href="<?=URL_BACKEND; ?>cms/snippets/edit/?id=<?=$snippet['name']; ?>">#
    				<?=$snippet['name']; ?>
    				#</a></li>
    			<?php } elseif ($snippet['type'] == 'Featured Community') { ?>
    			<li><span class="doc-icon"><img alt="" src="<?=URL_BACKEND_IMAGES; ?>ico-snippet.gif"></span> <a href="<?=URL_BACKEND; ?>cms/tools/communities/edit/?id=<?=$snippet['id']; ?>">#
    				<?=$snippet['name']; ?>
    				#</a></li>
    			<?php } else { ?>
    			<li><span class="doc-icon"><img alt="" src="<?=URL_BACKEND_IMAGES; ?>ico-snippet.gif"></span> #
    				<?=$snippet['name']; ?>
    				#</li>
    			<?php } ?>
    			<?php } ?>
    		</ul>
    	</div>
    	<?php } ?>
    	<h3><?= __('Published'); ?></h3>
    	<div class="field">
    		<input type="radio" name="published" id="published_true" value="true"<?=($edit_entry['published'] == 'true') ? ' checked' : ''; ?>>
    		<label class="toggle__label" for="published_true"><?= __('Yes'); ?></label>
    		<input type="radio" name="published" id="published_false" value="false"<?=($edit_entry['published'] == 'false') ? ' checked' : ''; ?>>
    		<label class="toggle__label" for="published_false"><?= __('No'); ?></label>
    	</div>
    	<div class="ui-helper-clearfix"></div>
    	<div id="publish-date">
    		<h3><?= __('Date Published'); ?></h3>
    		<div class="field">
    			<input class="w1/1" name="timestamp_published" value="<?=date('l, F j, Y g:ia', $edit_entry['timestamp_published']); ?>" placeholder="<?= __('Publish Date'); ?>" <?=($edit_entry['published'] == 'true') ? readonly : '' ?>>
    		</div>
    	</div>
    	<?php if (!empty($categories) && is_array($categories)) { ?>
    	<div>
    		<h3><?= __('Categories'); ?></h3>

			<?php foreach ($categories as $category) { ?>

				<label class="toggle toggle--stacked">
					<input<?=in_array($category['link'], $edit_entry['categories']) ? ' checked' : ''; ?> type="checkbox" name="categories[]" value="<?=$category['link']; ?>">
					<span class="toggle__label"><?=Format::htmlspecialchars($category['title']); ?></span>
				</label>
				<?php if (!empty($category['subcategories'])) { ?>
				<ul class="toggle toggle--stacked" style="list-style: none; padding-left: 20px; margin-top: 0;">
					<?php foreach ($category['subcategories'] as $subcategory) { ?>
					<li>
						<label>
							<input<?=in_array($subcategory['link'], $edit_entry['categories']) ? ' checked' : ''; ?> type="checkbox" name="categories[]" value="<?=$subcategory['link']; ?>">
							<span class="toggle__label"><?=Format::htmlspecialchars($subcategory['title']); ?></span>
						</label>
					</li>
					<?php } ?>
				</ul>
				<?php } ?>

			<?php } ?>

    	</div>
    	<?php } ?>
    	<h3><?= __('Meta Information'); ?></h3>
    	<div class="field">
    		<label class="field__label"><?= __('Description'); ?></label>
    		<textarea class="w1/1" id="meta_tag_desc" name="meta_tag_desc" cols="24" rows="6"><?=Format::htmlspecialchars($edit_entry['meta_tag_desc']); ?></textarea>
    		<p class="text--mute">
    			<?=tpl_lang('DESC_FORM_BLOG_DESCRIPTION'); ?>
    		</p>
    	</div>
    	<div class="field">
    		<label class="field__label"><?=tpl_lang('LBL_FORM_OG_IMAGE'); ?></label>
    		<div data-uploader='<?=json_encode([
                'inputName' => 'og_image[]',
                'placeholder' => __('Upload photo'),
                'extraParams' => ['type' => 'blog:og:image', 'row' => (int) $edit_entry['id']]
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
    	<div class="btns btns--stickyB"> <span class="R">
    		<button class="btn btn--positive"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?> </button>
    		</span>
        </div>
    </div>
</form>