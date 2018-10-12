<form action="?submit" method="post" class="rew_check">
	<input type="hidden" name="id" value="<?=$edit_page['page_id']; ?>">

    <div class="menu menu--copy menu--drop hidden" id="menu--actions" style="min-width: 0;">
        <ul class="menu__list">
				<?php if (!in_array($edit_page['file_name'], (is_array(unserialize(REQUIRED_PAGES)) ? unserialize(REQUIRED_PAGES) : array('404', 'error', 'unsubscribe')))) { ?>
				<li class="menu__item"><a class="menu__link" href="../copy/?id=<?=$edit_page['page_id']; ?><?=$subdomain->getPostLink(true); ?>"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-copy"></use></svg> <?= __('Copy'); ?></a></li>
				<?php } ?>
				<li class="menu__item"><a class="menu__link" target="_blank" href="<?=$link; ?>"><svg class="icon icon-eye mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-view"></use></svg> <?= __('View'); ?></a></li>
			</span>
		</ul>
    </div>

    <div class="bar">
        <div class="bar__title"><?= __('Edit Page'); ?></div>
        <div class="bar__actions">
                <?php if (!in_array($edit_page['file_name'], (is_array(unserialize(REQUIRED_PAGES)) ? unserialize(REQUIRED_PAGES) : array('404', 'error', 'unsubscribe')))) { ?>
                    <a class="bar__action -hidden@sm" href="../copy/?id=<?=$edit_page['page_id']; ?><?=$subdomain->getPostLink(true); ?>"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-copy"></use></svg> <?= __('Copy'); ?></a>
                <?php } ?>
                <a class="bar__action -hidden@sm" target="_blank" href="<?=$link; ?>"><svg class="icon icon-eye mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-view"></use></svg> <?= __('View'); ?></a>
            <a class="bar__action -hidden@md -hidden@lg" data-drop="#menu--actions"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-ellipses"></use></svg></a>
            <a class="bar__action timeline__back" href="<?='/backend/cms/' . $subdomain->getPostLink(); ?>"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
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
				<input class="w1/1" placeholder="<?=tpl_lang('LBL_FORM_CMS_PAGE_TITLE'); ?>" name="page_title" value="<?=htmlspecialchars($edit_page['page_title']); ?>" required>
			</div>
			<div class="field">
				<label class="field__label"><?= __('Main Content'); ?></label>
				<textarea class="w1/1 tinymce" id="category_html" name="category_html" cols="24" rows="25"><?=htmlspecialchars($edit_page['category_html']); ?></textarea>
			</div>
			<div class="field">
				<label class="field__label">
					<?=tpl_lang('LBL_FORM_CMS_PAGE_FOOTER'); ?>
				</label>
				<textarea class="w1/1" name="footer" cols="24" rows="2"><?=htmlspecialchars($edit_page['footer']); ?></textarea>
			</div>
		</div>
		<div class="col w1/4">
			<?php if (!empty($edit_page['snippets'])) { ?>
			<div class="field">
				<label class="field__label"><?= __('Snippets Used'); ?></label>
				<div class="nodes">
					<?php foreach ($edit_page['snippets'] as $snippet) { ?>
					<?php if (in_array($snippet['type'], array('idx', 'form', 'cms', 'bdx'))) { ?>
					<li class="node">
						<div class="article"><span class="ttl"><a href="<?=URL_BACKEND; ?>cms/snippets/edit/?id=<?=$snippet['name']; ?><?=$subdomain->getPostLink(true); ?>"><svg class="icon icon-snippet"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-snippet"></use></svg>
							<?=$snippet['name']; ?>
							</span></a></span>
						</div>
					</li>
					<?php } elseif (in_array($snippet['type'], array('rt'))) { ?>
					<li class="node">
						<div class="article"><span class="ttl"><a href="<?=URL_BACKEND; ?>rt/snippets/edit/?id=<?=$snippet['name']; ?><?=$subdomain->getPostLink(true); ?>"><svg class="icon icon-snippet"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-snippet"></use></svg>
							<?=$snippet['name']; ?>
							</span></a>
						</div>
					</li>
					<?php } elseif ($snippet['type'] == 'Featured Community') { ?>
					<li class="node">
						<div class="article"><span class="ttl"><a href="<?=URL_BACKEND; ?>cms/tools/communities/edit/?id=<?=$snippet['id']; ?><?=$subdomain->getPostLink(true); ?>"><svg class="icon icon-snippet"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-snippet"></use></svg>
							<?=$snippet['name']; ?>
							</span></a>
						</div>
					</li>
					<?php } elseif ($snippet['name'] == 'radio-landing-page') { ?>
					<li class="node">
						<div class="article"><span class="ttl"><a href="<?=URL_BACKEND; ?>cms/tools/radio-landing-page/<?=$subdomain->getPostLink(); ?>"><svg class="icon icon-snippet"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-snippet"></use></svg>
							<?=$snippet['name']; ?>
							</span></a>
						</div>
					</li>
					<?php } else { ?>
					<li class="node">
						<div class="article"><span class="ttl"><svg class="icon icon-snippet"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-snippet"></use></svg>
							<?=$snippet['name']; ?>
							</span>
							<svg class="icon icon-lock"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-lock"/></svg>
						</div>
					</li>
					<?php } ?>
					<?php } ?>
				</div>
			</div>
			<?php } ?>
			<?php if (in_array($edit_page['file_name'], (is_array(unserialize(REQUIRED_PAGES)) ? unserialize(REQUIRED_PAGES) : array('404', 'error', 'unsubscribe')))) { ?>
			<input type="hidden" name="file_name" id="file_name" value="<?=$edit_page['file_name']; ?>">
			<?php } else { ?>
			<div class="field">
				<label class="field__label">
					<?=tpl_lang('LBL_FORM_CMS_PAGE_ALIAS'); ?>
					<em class="required">*</em></label>
				<input class="w1/1" name="file_name" id="file_name" value="<?=$edit_page['file_name']; ?>" data-slugify required>
			</div>
			<?php } ?>
			<div class="field">
				<label class="field__label">
					<?=tpl_lang('LBL_FORM_CMS_LINK_LABEL'); ?>
                    <em class="required">*</em>
				</label>
				<input class="w1/1" name="link_name" value="<?=Format::htmlspecialchars($edit_page['link_name']); ?>" required>
			</div>
			<?php if (in_array($edit_page['file_name'], (is_array(unserialize(REQUIRED_PAGES)) ? unserialize(REQUIRED_PAGES) : array('404', 'error', 'unsubscribe')))) { ?>
			<input class="w1/1" type="hidden" name="category" value="<?=$edit_page['file_name']; ?>">
			<?php } else { ?>
			<div class="field">
				<label class="field__label"><?= __('Re-Assign To'); ?></label>
				<select name="category" class="w1/1">
					<option value="<?=$edit_page['file_name']; ?>">(<?= __('Main Page'); ?>)</option>
					<?php
						// Main Categories
						if (is_array($pages)) {
						                     	foreach ($pages as $category) {
						                          $selected = ($category['file_name'] == $edit_page['category']) ? ' selected' : '';
						                          echo '<option value="' . $category['file_name'] . '"' . $selected . '>' . Format::htmlspecialchars($category['link_name']) . '</option>';
						   	}
						  	} else {
						      	echo '<option value="' . $edit_page['file_name'] . '">' . $pages . '</option>';
						  	}

						  ?>
				</select>
			</div>
			<?php } ?>
			<?php if (!empty($required)) { ?>
			<input type="hidden" class="checkbox" name="hide" value="t">
			<input type="hidden" class="checkbox" name="hide_sitemap" value="t">
			<?php } else { ?>
			<ul class="nodes">
				<li class="node">
					<label class="article"><span class="ttl">
						<input type="checkbox" name="hide" value="t"<?=($edit_page['hide'] == 't') ? ' checked' : ''; ?>>
						<?= __('Hide from Navigation'); ?></span></label>
				</li>
				<li class="node">
					<label class="article"><span class="ttl">
						<input type="checkbox" name="hide_sitemap" value="t"<?=($edit_page['hide_sitemap'] == 't') ? ' checked' : ''; ?>>
						<?= __('Hide from Sitemap'); ?></span></label>
				</li>
			</ul>
			<?php } ?>

			<div class="field">
				<label class="field__label">
					<?=tpl_lang('LBL_FORM_CMS_PAGE_DESCRIPTION'); ?>
				</label>
				<textarea class="w1/1" name="meta_tag_desc" cols="24" rows="6"><?=htmlspecialchars($edit_page['meta_tag_desc']); ?></textarea>
			</div>
			<div class="field">
				<label class="field__label"><?=tpl_lang('LBL_FORM_OG_IMAGE'); ?></label>
				<div data-uploader='<?=json_encode([
                    'inputName' => 'og_image[]',
                    'placeholder' => __('Upload photo'),
                    'extraParams' => ['type' => 'page:og:image', 'row' => (int) $edit_page['page_id']]
                ]); ?>'>
					<?php if (!empty($og_image)) { ?>
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
        Container::getInstance()->make(REW\Core\Interfaces\Page\Template\EditorInterface::class)
            ->displayForm($edit_page['template'],
                is_array($edit_page['variables']) ? $edit_page['variables'] : json_decode($edit_page['variables'], true)
            )
        ;

    ?>
	</div>
	<div class="btns btns--stickyB">
		<span class="R">
			<button class="btn btn--positive"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?> </button>
		</span>
	</div>

    </div>

</form>
