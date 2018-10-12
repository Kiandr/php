<form action="?submit" method="post">
    <input type="hidden" name="id" value="<?=$snippet['name']; ?>">

    <div class="bar">
        <div class="bar__title"><?= __('Copy Snippet'); ?></div>
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
    </div>

    <div class="block">
        <h2>
            <?=htmlspecialchars($snippet['name']); ?>
        </h2>
        <div class="btns btns--stickyB">
            <span class="R">
                <button type="submit" class="btn btn--positive"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?> </button>
            </span>
        </div>
        <div class="field">
            <label class="field__label">
                <?=tpl_lang('LBL_FORM_CMS_SNIP_NAME'); ?>
                <em class="required">*</em></label>
            <input class="w1/1" name="snippet_id" id="snippet_id" value="<?=htmlspecialchars($name); ?>" data-slugify required>
            <p class="tip">
                <?=tpl_lang('DESC_FORM_CMS_SNIP_NAME'); ?>
            </p>
        </div>
    </div>
</form>