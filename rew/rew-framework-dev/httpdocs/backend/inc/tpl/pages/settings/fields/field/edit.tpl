<?php
/**
 * @var int $id
 * @var string $title
 * @var bool $enabled
 * @var string $currentType
 * @var array $types
 */
?>

<form action="?submit" method="post" class="rew_check">
    <input type="hidden" name="id" value="<?=$id; ?>">

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

        <div class="field">
            <label class="field__label"><?= __('Field Enabled'); ?></label>
            <div>
                <input id="enabled_true" type="radio" name="enabled" value=1<?=($enabled) ? ' checked' : ''; ?>>
                <label for="enabled_true"><?= __('Yes'); ?></label>
                <input id="enabled_false" type="radio" name="enabled" value=0<?=(!$enabled) ? ' checked' : ''; ?>>
                <label for="enabled_false"><?= __('No'); ?></label>
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

    </div>
</form>