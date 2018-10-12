<form action="?submit" method="post" class="rew_check">
    <div class="bar">
        <div class="bar__title"><?= __('Add Page'); ?></div>
        <div class="bar__actions">
            <a class="bar__action" href="/backend/cms/<?=$subdomain->getPostLink();?>"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
        </div>
    </div>

    <div class="block">

    <?php
        echo $this->view->render('::partials/subdomain/selector', [
            'subdomain' => $subdomain,
        ]);
    ?>

	<div class="cols padB">
		<div class="col w3/4">
			<div class="field">
				<label class="field__label"><?= __('Title'); ?><em class="required">*</em></label>
				<input class="w1/1" placeholder="<?=tpl_lang('LBL_FORM_CMS_PAGE_TITLE'); ?>" name="page_title" value="<?=Format::htmlspecialchars($_POST['page_title']); ?>" required>
			</div>
			<div class="field">
				<label class="field__label"><?= __('Main Content'); ?></label>
				<textarea class="w1/1 tinymce" id="category_html" name="category_html" cols="24" rows="25"><?=Format::htmlspecialchars($_POST['category_html']); ?></textarea>
			</div>
			<div class="field">
				<label class="field__label">
					<?=tpl_lang('LBL_FORM_CMS_PAGE_FOOTER'); ?>
				</label>
				<textarea class="w1/1" name="footer" cols="24" rows="2"><?=Format::htmlspecialchars($_POST['footer']); ?></textarea>
			</div>
		</div>
		<div class="col w1/4">
			<div class="field">
				<label class="field__label"><?= __('Page Level'); ?></label>
				<select class="w1/1" name="category">
					<option value="">----- <?= __('Set as Main Page'); ?> -----</option>
					<?php
						// Main Categories
						if (!empty($pages)&& is_array($pages)) {
						   	foreach ($pages as $category) {
						       	$selected = ($category['file_name'] == $_POST['category']) ? ' selected' : '';
						       	echo '<option value="' . $category['file_name'] . '"' . $selected . '>' . Format::htmlspecialchars($category['link_name']) . '</option>';
						   	}
						}

						?>
				</select>
			</div>
			<div class="field">
				<label class="field__label">
					<?=tpl_lang('LBL_FORM_CMS_PAGE_ALIAS'); ?>
          <em class="required">*</em>
				</label>
				<input class="w1/1" class="search_input" name="file_name" id="file_name" value="<?=$_POST['file_name']; ?>" data-slugify required>
			</div>
			<div class="field">
				<label class="field__label">
					<?=tpl_lang('LBL_FORM_CMS_LINK_LABEL'); ?>
          <em class="required">*</em>
				</label>
				<input class="w1/1" name="link_name" value="<?=Format::htmlspecialchars($_POST['link_name']); ?>" required>
			</div>
			<ul class="nodes">
				<li class="node">
					<label class="article">
						<input class="vC" type="checkbox" name="hide" value="t"<?=($_POST['hide'] == 't') ? ' checked' : ''; ?>>
						<?= __('Hide from Navigation'); ?></label>
				</li>
				<li class="node">
					<label class="article">
						<input class="vC" type="checkbox" name="hide_sitemap" value="t"<?=($_POST['hide_sitemap'] == 't') ? ' checked' : ''; ?>>
						<?= __('Hide from Sitemap'); ?></label>
				</li>
			</ul>

			<div class="field">
				<label class="field__label">
					<?=tpl_lang('LBL_FORM_CMS_PAGE_DESCRIPTION'); ?>
				</label>
				<textarea class="w1/1" name="meta_tag_desc" cols="24" rows="6"><?=htmlspecialchars($_POST['meta_tag_desc']); ?></textarea>
			</div>
			<div class="field">
				<label class="field__label"><?=tpl_lang('LBL_FORM_OG_IMAGE'); ?></label>
				<div data-uploader='<?=json_encode([
                    'inputName' => 'og_image[]',
                    'placeholder' => __('Upload photo'),
                    'extraParams' => ['type' => 'page:og:image']
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
			</div>
		</div>
	</div>
    <?php

        // Display Template Picker Form
        Container::getInstance()->make(REW\Core\Interfaces\Page\Template\EditorInterface::class)
            ->displayForm($_POST['template'], $_POST['variables'][$_POST['template']]);

    ?>
	<div class="btns btns--stickyB"> <span class="R">
		<button class="btn btn--positive"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save '); ?></button>
		</span>
    </div>

</div>

</form>
