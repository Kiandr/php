<div class="bar">
    <a class="bar__title" target="_blank" href="<?=$site_link; ?>"><?= __('Homepage'); ?></a>
    <div class="bar__actions">
        <a class="bar__action" href="/backend/cms/<?=$subdomain->getPostLink(); ?>"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
    </div>
</div>

<div class="block">

<form action="?submit" method="post" enctype="multipart/form-data" class="rew_check">

    <?php
        echo $this->view->render('::partials/subdomain/selector', [
            'subdomain' => $subdomain,
            'subdomains' => $subdomains,
        ]);
    ?>

	<div class="cols">
		<div class="col w3/4">
			<div class="field">
				<label class="field__label"><?= __('Title'); ?></label>
				<input class="w1/1" placeholder="<?=tpl_lang('LBL_FORM_CMS_PAGE_TITLE'); ?>" name="page_title" value="<?=htmlspecialchars($defaults['page_title']); ?>" required>
			</div>
			<?php if (!$subdomain->isPrimary()) { ?>
			<div class="field">
				<label class="field__label"><?=$team ? __('Your Team Website') : __('Your Website'); ?></label>
				<a class="w1/1" href="<?=$subdomain->getLink(); ?>" target="_blank">
				<?=$subdomain->getLink(); ?>
				</a> </div>
			<?php } ?>
			<?php if (!$subdomain->isPrimary() && Skin::hasFeature(Skin::SUBDOMAIN_FEATURE_IMAGE)) { ?>
				<div class="field">
					<label class="field__label"><?= __('Feature Photo'); ?></label>
					<?php if (!empty($defaults['feature_image'])) { ?>
						<a href="javascript:void(0)" class="manage_image"> <img src="<?=URL_FEATURED_IMAGES .  $defaults['feature_image']; ?>" border="0" width="600" alt="<?= __('Feature Photo'); ?>">
						<input type="hidden" name="feature_image" value="<?=$defaults['feature_image']; ?>" style="display: none; border: 0; width: 0;">
						</a>
						</br>
					<?php } ?>
					<input type="file" name="feature_image" value="<?=htmlspecialchars($defaults['feature_image']); ?>">
					<?php if (!empty($defaults['feature_image'])) { ?>
						<a class="btn btn--ico btn--ghost btn--left delete" href="?deleteFeaturedImage<?=$subdomain->getPostLink(true); ?>" onclick="return confirm('<?= __('Are you sure you want to remove this featured image?'); ?>');"><svg class="icon icon-trash"><use xlink:href="/backend/img/icos.svg#icon-trash"></use></svg></a>
					<?php } ?>
					<p class="tip"> <?= __('Upload the image that you\'d like used as your website\'s feature photo'); ?>.</p>
				</div>
			<?php } ?>
			<div class="field">
				<label class="field__label"><?= __('Main Content'); ?></label>
				<textarea class="w1/1 tinymce" id="category_html" name="category_html" cols="24" rows="20"><?=htmlspecialchars($defaults['category_html']); ?></textarea>
			</div>
			<div class="field">
				<label class="field__label">
					<?=tpl_lang('LBL_FORM_CMS_PAGE_FOOTER'); ?>
				</label>
				<textarea class="w1/1" name="footer" cols="24" rows="2"><?=htmlspecialchars($defaults['footer']); ?></textarea>
			</div>
		</div>
		<div class="col w1/4">
			<?php if (!empty($defaults['snippets'])) { ?>
			<div class="field">
				<label class="field__label"><?= __('Snippets Used'); ?></label>
				<?php foreach ($defaults['snippets'] as $snippet) { ?>
				<?php if (in_array($snippet['type'], array('idx', 'form', 'cms'))) { ?>
				<span class="doc-icon"><img alt="" src="<?=URL_BACKEND_IMAGES; ?>ico-snippet.gif"></span> <a href="<?=URL_BACKEND; ?>cms/snippets/edit/?id=<?=$snippet['name']; ?><?=$subdomain->getPostLink(true); ?>">#
				<?=$snippet['name']; ?>
				#</a>
				<?php } elseif ($snippet['type'] == 'Featured Community') { ?>
				<span class="doc-icon"><img alt="" src="<?=URL_BACKEND_IMAGES; ?>ico-snippet.gif"></span> <a href="<?=URL_BACKEND; ?>cms/tools/communities/edit/?id=<?=$snippet['id']; ?><?=$subdomain->getPostLink(true); ?>">#
				<?=$snippet['name']; ?>
				#</a>
				<?php } elseif ($snippet['name'] == 'radio-landing-page') { ?>
				<span class="doc-icon"><img alt="" src="<?=URL_BACKEND_IMAGES; ?>ico-snippet.gif"></span> <a href="<?=URL_BACKEND; ?>cms/tools/radio-landing-page/<?=$subdomain->getPostLink(); ?>">#
				<?=$snippet['name']; ?>
				#</a>
				<?php } else { ?>
				<span class="doc-icon"><img alt="" src="<?=URL_BACKEND_IMAGES; ?>ico-snippet.gif"></span> #
				<?=$snippet['name']; ?>
				#
				<?php } ?>
				<?php } ?>
			</div>
			<?php } ?>
			<div class="field">
				<label class="field__label">
					<?=tpl_lang('LBL_FORM_CMS_PAGE_DESCRIPTION'); ?>
				</label>
				<textarea class="w1/1" a name="meta_tag_desc" cols="24" rows="6"><?=htmlspecialchars($defaults['meta_tag_desc']); ?></textarea>
			</div>
			<div class="field">
				<label class="field__label"><?= __('Preview Image'); ?></label>
				<?php
				    $extraParams = ['type'=> $subdomain->getOgType()];
				    if (!$subdomain->isPrimary()) {
				        $extraParams['row'] = (int) $subdomain->getId();
				    }
				?>
				<div data-uploader='<?=json_encode([
                    'inputName' => 'og_image[]',
                    'extraParams' => $extraParams
                ]); ?>'>
					<?php
						if (!empty($og_image)) { ?>
					<div class="file-manager">
						<ul>
							<?php foreach ($og_image as $image) { ?>
								<li upload=<?=htmlspecialchars($image['id']); ?>>
									<div class="wrap">
										<img src="/thumbs/72x72/uploads/<?=urlencode($image['file']); ?>" border="0">
										<span><?=$image['file']; ?></span>
										<div class="actions" hidden><a href="#" class="btn delete"><?= __('Delete'); ?></a></div>
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
        if ($subdomain->isPrimary()) {
            Container::getInstance()->make(REW\Core\Interfaces\Page\Template\EditorInterface::class)->displayForm($defaults['template'],
                is_array($defaults['variables']) ? $defaults['variables'] : json_decode($defaults['variables'], true)
            );
        }

    ?>
	<div class="btns btns--stickyB"> <span class="R">
		<button class="btn btn--positive"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?> </button>
		</span>
    </div>
    </div>
</form>
