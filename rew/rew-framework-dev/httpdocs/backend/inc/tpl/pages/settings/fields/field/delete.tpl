<?php
/**
 * @var int $id
 * @var int $customFieldUsage
 */
?>

<div class="block">
    <form method="post">
        <input type="hidden" name="id" value="<?= $id; ?>">
        <p><?= __('Are you sure you want to delete this custom field?'); ?>
            <?php
            if (!empty($customFieldUsage)) {
                echo __(
                    'This will delete data for %s.',
                    '<b>' . Format::htmlspecialchars($customFieldUsage) . ' ' . n__('lead', 'leads', $customFieldCount) . '</b>'
                );
            } else {
                echo __('No lead data will be lost.');
            }
            ?>
        </p>
        <div class="btns">
            <button type="submit" class="btn btn--negative"><?= __('Yes, Delete'); ?></button>
            <a href="../../" class="btn"><?= __('Cancel'); ?></a>
        </div>
    </form>
</div>
