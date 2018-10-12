<?php include('inc/tpl/app/menu-associates.tpl.php'); ?>
<div class="bar">
    <a class="bar__title" data-drop="#menu--filters" href="javascript:void(0);">
        <?= __('Delete Associate'); ?>
        <svg class="icon icon-drop">
            <use xlink:href="/backend/img/icos.svg#icon-drop" xmlns:xlink="http://www.w3.org/1999/xlink"/>
        </svg>
    </a>
    <div class="bar__actions">
        <a class="bar__action" href="<?=URL_BACKEND; ?>associates/">
            <svg class="icon">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use>
            </svg>
        </a>
    </div>
</div>
<?php include('inc/tpl/app/summary-associate.tpl.php'); ?>

<div class="block">
    <form method="post">
        <input type="hidden" name="delete" value="1">
        <p><?= __('Are you sure you want to delete this associate?'); ?></p>
        <div class="btns">
            <button type="submit" class="btn btn--negative"><?= __('Yes, Delete'); ?></button>
            <a href="../../" class="btn"><?= __('Cancel'); ?></a>
        </div>
    </form>
</div>