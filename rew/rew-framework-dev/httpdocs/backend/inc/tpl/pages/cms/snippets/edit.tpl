<form action="?saveSnippet" method="post" class="rew_check">
	<input type="hidden" name="id" value="<?=$snippet['name']; ?>">

    <?php if ($snippet['type'] == 'cms') : ?>
        <div class="menu menu--copy menu--drop hidden" id="menu--ellipses" style="min-width: 0;">
            <ul class="menu__list">
                <li class="menu__item"><a class="menu__link" href="<?=URL_BACKEND; ?>cms/snippets/copy/?id=<?=$snippet['name']; ?><?=$subdomain->getPostLink(true); ?>"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-copy"></use></svg> <?= __('Copy'); ?></a></li>
            </ul>
        </div>
    <?php endif; ?>

	<div class="bar">
		<div class="bar__title"><?= __('Edit Snippet'); ?></div>
		<div class="bar__actions">
            <?php if ($snippet['type'] == 'cms') echo '<a class="bar__action" href="#" data-drop="#menu--ellipses"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-ellipses"></use></svg></a>'; ?>
			<a class="bar__action timeline__back" href="<?='/backend/cms/snippets/' . $subdomain->getPostLink(); ?>"><svg class="icon icon-left-a mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
		</div>
	</div>

    <div class="block">
        <?php
            echo $this->view->render('::partials/subdomain/selector', [
                'subdomain' => $subdomain,
            ]);
        ?>
    </div>

    <div class="block">

	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_CMS_SNIP_NAME'); ?>
			<em class="required">*</em></label>
		<?php if ($snippet['type'] == 'form') : ?>
		<span class="snippet-name">#
		<?=$snippet['name']; ?>
		#</span>
		<input class="w1/1" type="hidden" name="snippet_id" id="snippet_id" value="<?=htmlspecialchars($snippet['name']); ?>">
		<?php else : ?>
		<input class="w1/1" type="text" name="snippet_id" id="snippet_id" value="<?=htmlspecialchars($_POST['snippet_id'] ?: $snippet['name']); ?>" data-slugify required>
		<?php endif; ?>
	</div>
	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_CMS_SNIP_CODE'); ?>
		</label>
		<textarea class="w1/1" name="code" cols="24" rows="20"><?=htmlspecialchars($_POST['code'] ?: $snippet['code']); ?></textarea>
		<?php if ($snippet['type'] == 'form') : ?>
		<label class="hint"><?= __('Tags'); ?>: {opt_in}</label>
		<?php endif; ?>
	</div>
	<div class="field">
		<?php if (!empty($snippet['pages'])) : ?>
		<ul class="checklist" style="list-style: none; padding-left: 0;">
			<?php foreach ($snippet['pages'] as $pg) : ?>
			<li>
				<div class="item_content_ico ico ico-page"></div>
				<a href="<?=$pg['href']; ?>">
				<?=$pg['text']; ?>
				</a> </li>
			<?php endforeach; ?>
		</ul>
		<?php else : ?>
		<p><?= __('This snippet is currently not being used on any pages.'); ?></p>
		<?php endif; ?>
		<?php if (!empty($can_revert)) : ?>
		<h3 style="font-size: 18px;"><?= __('Revert Snippet'); ?></h3>
		<p><?= __('This is a framework snippet and can be reverted back to its original code.'); ?></p>
		<label class="boolean toggle"><input type="checkbox" name="revert" value="true" />
            <span class="toggle__label"><?= __('Revert this Snippet'); ?></span>
        </label>
		<?php endif; ?>
	</div>
	<div class="btns btns--stickyB">
		<span class="R">
			<button class="btn btn--positive"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?> </button>
		</span>
	</div>

    </div>

</form>