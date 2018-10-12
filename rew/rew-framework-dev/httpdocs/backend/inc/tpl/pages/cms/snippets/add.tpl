<form action="?submit" method="post" class="rew_check">

	<div class="bar">
		<div class="bar__title"><?= __('Add CMS Snippet'); ?></div>
		<div class="bar__actions">
			<a class="bar__action" href="/backend/cms/snippets/<?=$subdomain->getPostLink();?>"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
		</div>
	</div>

    <div class="block">

        <?php
            echo $this->view->render('::partials/subdomain/selector', [
                'subdomain' => $subdomain,
            ]);
        ?>

	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_CMS_SNIP_NAME'); ?>
			<em class="required">*</em></label>
		<input class="w1/1" class="search_input" name="snippet_id" id="snippet_id" value="<?=htmlspecialchars($_POST['snippet_id']); ?>" data-slugify required>
	</div>
	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_CMS_SNIP_CODE'); ?>
		</label>
		<textarea class="w1/1" name="code" cols="24" rows="20"><?=htmlspecialchars($_POST['code']); ?></textarea>
	</div>
	<div class="btns btns--stickyB"> <span class="R">
		<button class="btn btn--positive"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?> </button>
		</span>
    </div>

    </div>

</form>