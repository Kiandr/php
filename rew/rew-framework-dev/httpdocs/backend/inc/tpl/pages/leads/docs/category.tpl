<form action="?submit" method="post" class="rew_check">

<div class="bar">
    <div class="bar__title">
        <?php if (!empty($category['id'])) { ?>
            <?= Format::htmlspecialchars($category['name']); ?>
            <input type="hidden" name="id" value="<?= $category['id']; ?>">
        <?php } else {
            echo __('Add Category');
        } ?>
    </div>
    <div class="bar__actions">
        <a class="bar__action" href="/backend/leads/docs/?tab=template"><svg class="icon icon-left-a mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
    </div>
</div>

<div class="block">

    <div class="btns btns--stickyB">
        <span class="R">
		<button class="btn btn--positive" type="submit">
		<svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?>
		</button>
		</span>
    </div>

	<div class="field">
		<label class="field__label"><?= __('Category Name'); ?> <em class="required">*</em></label>
		<input class="w1/1" name="name" value="<?=Format::htmlspecialchars($category['name']); ?>" required>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Description'); ?></label>
		<textarea class="w1/1" rows="4" name="description"><?=Format::htmlspecialchars($category['description']); ?></textarea>
	</div>

</div>

</form>
