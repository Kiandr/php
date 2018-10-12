<?php
/**
 * @var int $id
 * @var bool $enabled
 */
?>

<div class="block">
    <?php if (!$enabled) {?>
        <form method="post">
            <input type="hidden" name="id" value="<?=$id; ?>">
            <p><?= __('Are you sure you want to enable this custom field?'); ?></p>
            <div class="btns">
                <button type="submit" class="btn btn--positive"><?= __('Yes, Enable'); ?></button>
                <a href="../../" class="btn"><?= __('Cancel'); ?></a>
            </div>
        </form>
    <?php } else { ?>
        <p><?= __('This custom field has already been enabled.'); ?></p>
    <?php }?>
</div>
