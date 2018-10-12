<div class="main" style="display: flex; align-items: center;">
    <?= errorMsg(htmlspecialchars($e->getMessage()),
        '<img src="/backend/img/ills/nothin.png"/>' . __('An Error Occurred')); ?>
</div>
