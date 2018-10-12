<?php
    if (empty($feeds)) return;
?>
<h4><?= Format::htmlspecialchars($this->config('heading')); ?></h4>
<ul class="uk-list uk-list-line">
    <?php foreach ($feeds as $feed) { ?>
        <?php if ($feed['active']) { ?>
            <input type="hidden" name="feed" value="<?= Format::htmlspecialchars($feed['name']); ?>">
        <?php } ?>
        <li class="<?= $feed['active'] ? 'uk-active' : ''; ?>"><a href="<?= Format::htmlspecialchars($feed['link']); ?>"><?= Format::htmlspecialchars($feed['title']); ?></a></li>
    <?php } ?>
</ul>
