<?php
/**
 * @var int $id
 * @var bool $enabled
 */
?>

<div class="block">
    <?php if ($enabled) {?>
        <form method="post">
            <input type="hidden" name="id" value="<?=$id; ?>">
            <p><?= __('Are you sure you want to disable this custom field?'); ?></p>
            <div class="btns">
                <button type="submit" class="btn btn--negative"><?= __('Yes, Disable'); ?></button>
                <a href="../../" class="btn"><?= __('Cancel'); ?></a>
            </div>
        </form>
    <?php } else { ?>
        <p><?= __('This custom field has already been disabled.'); ?></p>
    <?php }?>
</div>
