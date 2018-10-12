<?php foreach ($navigation as $nav) { ?>
    <?php if (empty($nav['pages'])) continue; ?>
    <div<?=(!empty($nav['class']) ? ' class="' . Format::htmlspecialchars($nav['class']) . '"' : ''); ?>>
        <?php if (!empty($nav['link'])) { ?>
            <header>
                <a href="<?=$nav['link']; ?>">
                    <h4><?=$nav['title']; ?></h4>
                </a>
            </header>
        <?php } else { ?>
            <header><h4><?=$nav['title']; ?></h4></header>
        <?php } ?>
        <ul class="nav">
            <?php foreach ($nav['pages'] as $page) { ?>
                <?php $current = (strstr($page['link'], Http_Uri::getUri()) == Http_Uri::getUri()) || ($page['link'] != '/' && strstr(Http_Uri::getUri(), str_replace('.php', '/', $page['link'])) !== false); ?>
                <li<?=(!empty($current) ? ' class="current"' : ''); ?>><a href="<?= Format::htmlspecialchars($page['link']); ?>"<?=!empty($page['target']) ? ' target="' . Format::htmlspecialchars($page['target']) . '"' : ''; ?>><?= Format::htmlspecialchars($page['title']); ?></a>
                    <?php if (!empty($page['subpages'])) { ?>
                        <ul>
                            <?php foreach ($page['subpages'] as $subpage) { ?>
                                <?php $current = (strstr($subpage['link'], Http_Uri::getUri()) == Http_Uri::getUri()) || ($page['link'] != '/' && strstr(Http_Uri::getUri(), str_replace('.php', '/', $subpage['link'])) !== false); ?>
                                <li<?=(!empty($current) ? ' class="current"' : ''); ?>><a href="<?=$subpage['link']; ?>"<?=!empty($subpage['target']) ? ' target="' . $subpage['target'] . '"' : ''; ?>><?= Format::htmlspecialchars($subpage['title']); ?></a></li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php } ?>
