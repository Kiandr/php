<?php

/**
 * @var REW\Backend\CMS\Subdomain $subdomain
 * @var string $subdomainSelector
 * @var boolean $homepage
 * @var boolean $canSort
 * @var string $filter
 * @var array $filters
 * @var array $subdomainPostLink
 * @var array $pages
 * @var array $paginationLinks
 * @var boolean $firstPage
 */

?>

<filter-pages name="Filter" filter-type="<?=$filter; ?>" subdomain-post-link="<?=$subdomainPostLink; ?>"></filter-pages>
<?php if (!empty($filters)) { ?>
    <div class="menu menu--drop hidden" id="menu--filters">
        <ul class="menu__list">
            <?php foreach ($filters as $link => $text) {
                printf(
                    '<li class="menu__item"><a class="menu__link%s" href="?filter=%s">%s</a></li>',
                    $link === $filter ? ' is-active' : '',
                    $link . $subdomain->getPostLink(true),
                    $text
                );
            } ?>
        </ul>
    </div>
<?php } ?>

<div class="bar">
    <a class="bar__title" href="javascript:void(0);" data-drop="#menu--filters">
        <?=htmlspecialchars($filters[$filter]); ?>
        <?php if (!empty($filters)) { ?>
            <svg class="icon icon-drop">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-drop"></use>
            </svg>
        <?php } ?>
    </a>
    <div class="bar__actions">
        <a class="bar__action" href="<?=URL_BACKEND; ?>cms/pages/add/<?=$subdomain->getPostLink(); ?>">
            <svg class="icon icon-add mar0">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use>
            </svg>
        </a>
        <?php if ($_GET['filter'] != 'nav') { ?>
        <btn-filter></btn-filter>
        <?php } ?>
    </div>
</div>

<div class="block">
    <?=$subdomainSelector; ?>
</div>

<div id="nav-pages" class="nodes dd">
    <ul class="nodes__list dd-list dd-list-first">
        <?php if (!empty($homepage) && !empty($firstPage)) { ?>
            <li class="nodes__branch">
                <div class="nodes__wrap">
                    <div class="article">
                        <div class="article__body">
                            <div class="article__thumb thumb thumb--medium -bg-rew2">
                                <svg class="icon icon--invert"><use xlink:href="/backend/img/icos.svg#icon-page"></use></svg>
                            </div>
                            <div class="article__content">
                                <a class="text text--strong" href="<?=URL_BACKEND; ?>cms/homepage/<?=$subdomain->getPostLink(); ?>"><?= __('Homepage'); ?></a>
                                <div class="text text--mute">/</div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        <?php } ?>
        <?php foreach ($pages as $page) { ?>
            <li class="nodes__branch dd-item" id="pages-<?=$page['page_id']; ?>">
                <div class="nodes__wrap">
                    <?php if (!empty($canSort)) { ?>
                        <div class="nodes__handle"></div>
                    <?php } ?>
                    <div class="article dd-handle">
                        <div class="article__body">
                            <div class="article__thumb thumb thumb--medium -bg-rew2">
                                <svg class="icon icon--invert">
                                    <use xlink:href="/backend/img/icos.svg#icon-<?=$page['is_link'] === 't' ? 'link' : 'page'; ?>"></use>
                                </svg>
                            </div>
                            <div class="article__content">
                                <a class="text text--strong" href="<?=URL_BACKEND; ?>cms/pages/edit/?id=<?=$page['page_id'] . $subdomain->getPostLink(true); ?>">
                                    <?=htmlspecialchars($page['link_name']); ?>
                                </a>
                                <div class="text text--mute">
                                    <?=sprintf($page['is_link'] === 'f' ? '/%s.php' : '%s', htmlspecialchars($page['file_name'])); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if (!empty($page['can_delete'])) { ?>
                        <div class="nodes__actions">
                            <form method="post" action="<?=$page['deleteFormAction']; ?>">
                                <input type="hidden" name="delete" value="<?=$page['delete']; ?>" />
                                <button onclick="return confirm('<?= __('Are you sure you would like to delete this page?'); ?>');" title="<?= __('Delete'); ?>" class="btn btn--ghost btn--ico">
                                    <icon name="icon--trash--row"></icon>
                                </button>
                            </form>
                        </div>
                    <?php } ?>
                </div>
                <?php if (!empty($page['subpages']) && is_array($page['subpages'])) { ?>
                    <ul class="nodes__list dd-list dd-list">
                        <?php foreach ($page['subpages'] as $subpage) { ?>
                            <li class="nodes__branch dd-item" id="pages-<?=$subpage['page_id']; ?>">
                                <div class="nodes__wrap">
                                    <?php if (!empty($canSort)) { ?>
                                        <div class="nodes__handle"></div>
                                    <?php } ?>
                                    <div class="article dd-handle">
                                        <div class="article__body">
                                            <div class="article__thumb thumb thumb--medium -bg-rew2">
                                                <svg class="icon icon--invert">
                                                    <use xlink:href="/backend/img/icos.svg#icon-<?=$subpage['is_link'] === 't' ? 'link' : 'page'; ?>"></use>
                                                </svg>
                                            </div>
                                            <div class="article__content">
                                                <a class="text text--strong" href="<?=URL_BACKEND; ?>cms/pages/edit/?id=<?=$subpage['page_id'] . $subdomain->getPostLink(true); ?>">
                                                    <?=htmlspecialchars($subpage['link_name']); ?>
                                                </a>
                                                <div class="text text--mute">
                                                    <?=sprintf($subpage['is_link'] === 'f' ? '/%s.php' : '%s', htmlspecialchars($subpage['file_name'])); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if (!empty($page['can_delete'])) { ?>
                                        <div class="nodes__actions" style="position: relative;">
                                        <form  method="post" action="<?=$subpage['deleteFormAction']; ?>">
                                            <input type="hidden" name="delete" value="<?=$subpage['page_id']; ?>" />
                                            <button onclick="return confirm('<?= __('Are you sure you would like to delete this sub-page?'); ?>');" title="<?= __('Delete'); ?>" class="btn btn--ghost btn--ico" style='padding-right: 25px;'><icon name="icon--trash--row"></icon></button>
                                        </form>
                                    <?php } ?>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>
</div>

<?php if (!empty($paginationLinks)) { ?>
    <div class="nav_pagination">
        <?php if (!empty($paginationLinks['prevLink'])) { ?>
            <a class="prev marR" href="<?=$paginationLinks['prevLink']; ?>">
                <svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg>
            </a>
        <?php } ?>
        <?php if (!empty($paginationLinks['nextLink'])) { ?>
            <a class="next" href="<?=$paginationLinks['nextLink']; ?>">
                <svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-right-a"></use></svg>
            </a>
        <?php } ?>
    </div>
<?php } ?>
