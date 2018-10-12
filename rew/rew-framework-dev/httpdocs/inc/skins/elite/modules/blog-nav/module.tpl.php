<?php foreach ($navigation as $nav) { ?>
    <ul class="blog-nav">
        <li class="uk-nav-header"><?= Format::htmlspecialchars($nav['title']); ?></li>
        
        <?php foreach ($nav['pages'] as $page) { ?>
            <li<?=(strstr($page['link'], Http_Uri::getUri()) == Http_Uri::getUri() ? ' class="uk-active"' : ''); ?>><a href="<?=$page['link']; ?>"<?=!empty($page['target']) ? ' target="' . $page['target'] . '"' : ''; ?> title="<?= Format::htmlspecialchars($page['title']); ?>"><?= Format::htmlspecialchars($page['title']); ?></a>
                <?php if (!empty($page['subpages'])) { ?>
                    <ul>
                        <?php foreach ($page['subpages'] as $subpage) { ?>
                            <li<?=(strstr($subpage['link'], Http_Uri::getUri()) == Http_Uri::getUri() ? ' class="uk-active"' : ''); ?>><a href="<?= Format::htmlspecialchars($subpage['link']); ?>"<?=!empty($subpage['target']) ? ' target="' . Format::htmlspecialchars($subpage['target']) . '"' : ''; ?> title="<?= Format::htmlspecialchars($subpage['title']); ?>"><?= Format::htmlspecialchars($subpage['title']); ?></a></li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>
<?php } ?>
