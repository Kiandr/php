<?php if (!empty($pagination['links'])) { ?>
<div class="toolbar-pagination">
    <?php if (!empty($pagination['prev'])) { ?>
    <div class="uk-button-group">
        <a class="uk-button" id="previous" href="<?= Format::htmlspecialchars($pagination['prev']['url']); ?>"><i class="ion-ios-arrow-left"></i></a>
    </div>
    <?php } ?>
    <div class="uk-button-group">
        <button class="uk-button" id="pagination"><span class="uk-margin-left uk-margin-right">Page <?= ((int) $pagination['page']); ?> of <?= ((int) $pagination['pages']); ?></span>
    </div>
    <?php if (!empty($pagination['next'])) { ?>
    <div class="uk-button-group">
        <a class="uk-button" id="next" href="<?= Format::htmlspecialchars($pagination['next']['url']); ?>"><i class="ion-ios-arrow-right"></i></a>
    </div>
    <?php } ?>
</div>
<?php } ?>
