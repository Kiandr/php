<?php
/**
 * @var string $title
 * @var string $currentType
 * @var array $types
 */
?>

<form action="?submit" method="post" class="rew_check">

    <div class="bar">
        <div class="bar__title"><?= __('Add New Custom Field'); ?></div>
        <div class="bar__actions">
            <a class="bar__action timeline__back" href="javascript:void(0)" link="<?=URL_BACKEND;?>settings/fields/">
                <svg class="icon">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use>
                </svg>
            </a>
        </div>
    </div>

    <div class="block">

        <div class="field">
            <label class="field__label"><?= __('Field Name'); ?> <em class="required">*</em></label>
            <input class="w1/1" id="group-name" type="text" name="title" value="<?=$title; ?>" maxLength="120" required>
        </div>

        <div class="field">
            <label class="field__label"><?= __('Field Type'); ?> <em class="required">*</em></label>
            <select class="w1/1" name="type">
                <option value=""><?= __('Select a type'); ?></option>
                <?php if (!empty($types) && is_array($types)) {?>
                    <?php foreach ($types AS $type => $typeTitle) { ?>
                        <option value="<?=$type; ?>"<?=($type == $currentType) ? ' selected' : ''; ?>><?=$typeTitle; ?></option>
                    <?php } ?>
                <?php } ?>
            </select>
        </div>

    </div>

    <div class="btns btns--stickyB">
        <span class="R">
            <button class="btn btn--positive" type="submit">
                <svg class="icon icon-check mar0">
                    <use xlink:href="/backend/img/icos.svg#icon-check"></use>
                </svg>
                <?= __('Save'); ?>
            </button>
        </span>
    </div>

</form>