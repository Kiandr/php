<ul class="uk-list uk-grid uk-grid-small">
    <?php foreach ($ads as $ad) { ?>
        <?php $title = $ad['title'] ?: preg_replace("/\.[a-zA-Z0-9]+$/", '', str_replace('_', ' ', $ad['audio'])); ?>
        <?php $image = $ad['image'] ? '/uploads/' . $ad['image'] : null; ?>
        <li class="uk-width-1-1 uk-width-small-1-2 uk-width-medium-1-3">
            <div class="uk-margin uk-alert uk-border-rounded">
                <h5><?= Format::htmlspecialchars($title); ?></h5>
                <audio src="/uploads/<?= Format::htmlspecialchars($ad['audio']); ?>" preload="none">
            </div>
        </li>
    <?php } ?>
</ul>
