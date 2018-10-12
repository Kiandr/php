<ul class="playerLinks">
    <?php foreach ($ads as $ad) { ?>
        <?php $title = $ad['title'] ?: preg_replace("/\.[a-zA-Z0-9]+$/", '', str_replace('_', ' ', $ad['audio'])); ?>
        <li><span><?= Format::htmlspecialchars($title); ?></span><audio src="/uploads/<?= Format::htmlspecialchars($ad['audio']); ?>" preload="none"></li>
    <?php } ?>
</ul>
