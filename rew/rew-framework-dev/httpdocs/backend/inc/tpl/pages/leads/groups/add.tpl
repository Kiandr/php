<form action="?submit" method="post" class="rew_check">
    <div class="bar">
        <div class="bar__title"><?= __('Add New Group'); ?></div>
        <div class="bar__actions">
            <a class="bar__action" href="/backend/leads/groups/">
                <svg class="icon">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use>
                </svg>
            </a>
        </div>
    </div>
    <div class="block">
        <div class="field">
            <label class="field__label"><?= __('Group Name'); ?> <em class="required">*</em></label>
            <input class="w1/1" type="text" name="name" value="<?=Format::htmlspecialchars($_POST['name']); ?>" required>
        </div>
        <div class="field">
            <label class="field__label"><?= __('Label Color'); ?> <em class="required">*</em></label>
            <select class="w1/1" name="style" required>
                <?php foreach ($groupLabels as $groupLabel) { ?>
                    <option value="<?=$groupLabel; ?>"<?=($group['style'] == $groupLabel) ? ' selected': ''; ?>><?=ucfirst($groupLabel); ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="field">
            <label class="field__label"><?= __('Description'); ?></label>
            <textarea class="w1/1" rows="4" name="description"><?=Format::htmlspecialchars($_POST['description']); ?></textarea>
        </div>
        <?php if (!empty($can_share)) { ?>
            <div class="field">
                <label class="toggle">
                    <input type="checkbox" name="global" value="true"<?=(!empty($_POST['global']) ? ' checked' : ''); ?>>
                    <span class="toggle__label"><?= __('Shared Group'); ?></span>
                </label>
            </div>
        <?php } ?>
        <div class="btns btns--stickyB">
            <span class="R">
                <button class="btn btn--positive" type="submit">
                    <svg class="icon icon-check mar0">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use>
                    </svg>
                    <?= __('Save'); ?>
                </button>
            </span>
        </div>
    </div>
</form>
