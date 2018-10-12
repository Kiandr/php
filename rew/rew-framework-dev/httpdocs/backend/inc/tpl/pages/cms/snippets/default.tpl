<?php

    $canAdd = $can_add;
    $type = 'CMS';
    if($_GET['filter']=='idx') {
        $type = 'IDX';
        $addUrl = '/backend/idx/snippets/add/' . $subdomain->getPostLink();
    } else if($_GET['filter']=='bdx') {
        $type = 'BDX';
        $addUrl = '/backend/bdx/snippets/add/' . $subdomain->getPostLink();
    } elseif($_GET['filter']=='directory') {
        $type = 'Directory';
        $addUrl = '/backend/directory/snippets/add/' . $subdomain->getPostLink();
    } elseif(in_array($_GET['filter'], ['form','module'])) {
        $canAdd = false;
    } else{
        $addUrl = '/backend/cms/snippets/add/' . $subdomain->getPostLink();
    }

?>
<filter-snippets name="Filter" filter-type="<?=$filter; ?>" subdomain-post-link="<?=$subdomain->getPostLink(true); ?>"></filter-snippets>
<div class="menu menu--drop hidden" id="menu--filters">
    <ul class="menu__list">
    <?php foreach($filters as $filter) { ?>
    <li class="menu__item <?php if($filter['current']) echo ' current';?>"><a class="menu__link" href="/backend/<?=$filter['href'];?>"><?=$filter['text'];?></a></li>
    <?php } ?>
    </ul>
</div>

<div class="bar">
    <a href="#" data-drop="#menu--filters" class="bar__title">
        <?=$title; ?> <svg class="icon icon-drop"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-drop"></use></svg>
    </a>
    <div class="bar__actions">
        <?php if ($canAdd) { ?>
        <a href="<?=$addUrl;?>" class="bar__action" title="<?= __('Add %s Snippet', $type); ?>">
            <svg class="icon">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use>
            </svg>
        </a>
        <?php }?>
        <btn-filter></btn-filter>
    </div>
</div>

<div class="block">
    <?php
        echo $this->view->render('::partials/subdomain/selector', [
            'subdomain' => $subdomain,
            'subdomains' => $subdomains,
        ]);
    ?>
</div>

<?php if (!empty($snippets)) { ?>
<div class="nodes">
    <ul class="nodes__list">
        <?php foreach ($snippets as $snippet) { ?>
            <?php if (strtoupper($snippet['type']) != $group) { ?>
                <?php $group = strtoupper($snippet['type']); ?>
                <?php if (in_array($group, array('IDX', 'CMS', 'BDX', 'RT'))) { ?>
                    <li class="divider -pad">
                        <span class="divider__label divider__label--left text text--small"><?=$group; ?> <?= __('Snippets'); ?></span>
                    </li>
                <?php } else { ?>
                    <li class="divider -pad">
                        <span class="divider__label divider__label--left text text--small"><?=ucwords(strtolower($group)); ?> <?= __('Snippets'); ?></span>
                    </li>
                <?php } ?>
            <?php } ?>
            <li class="nodes__branch">
                <div class="nodes__wrap">
                    <div class="article">
                        <div class="article__body">
                            <div class="article__thumb thumb thumb--medium -bg-rew2">
                                <svg class="icon icon--invert"><use xlink:href="/backend/img/icos.svg#icon-snippet"/></svg>
                            </div>
                            <div class="article__content">
                            <?php if ($snippet['can_edit']) { ?>
                                <?php if ($snippet['type'] === 'rt') { ?>
                                    <a class="text text--strong" href="/backend/rt/snippets/edit/?id=<?=$snippet['name'] . $subdomain->getPostLink(true);?>">
                                        <?=ucwords(str_replace('-',' ',$snippet['name']));?>
                                    </a>
                                    <div class="text text--mute">#<?=$snippet['name'];?>#</div>
                                <?php } elseif (in_array($snippet['type'], array('idx', 'cms', 'directory', 'bdx', 'form'))) { ?>
                                    <a class="text text--strong" href="edit/?id=<?=$snippet['name']; ?><?=$subdomain->getPostLink(true);?>">
                                        <?=ucwords(str_replace('-',' ',$snippet['name']));?>
                                    </a>
                                    <div class="text text--mute">#<?=$snippet['name'];?>#</div>
                                <?php } else { ?>
                                    <span class="text text--strong">
                                        <?=ucwords(str_replace('-',' ',$snippet['name']));?>
                                    </span>
                                    <div class="text text--mute">#<?=$snippet['name'];?>#</div>
                                <?php } ?>
                            <?php } else { ?>
                                <span class="text text--strong">
                                    <?=ucwords(str_replace('-',' ',$snippet['name']));?>
                                </span>
                                <div class="text text--mute">#<?=$snippet['name'];?>#</div>
                            <?php } ?>
                            </div>

                            <div class="btns">
                                <?php if ($snippet['can_edit']) { ?>
                                    <?php if (in_array($snippet['type'], array('idx', 'cms', 'directory', 'bdx', 'rt'))) { ?>
                                        <?php if (!empty($can_delete)) { ?>
                                            <a class="btn btn--ghost btn--ico" href="<?=$snippet['deleteLink']; ?>" onclick="return confirm('<?= __('Are you sure you want to delete this snippet?'); ?> #<?=Format::htmlspecialchars($snippet['name']); ?>#');">
                                                <svg class="icon icon-trash mar0">
                                                    <use xlink:href="/backend/img/icos.svg#icon-trash"></use>
                                                </svg>
                                            </a>
                                        <?php } ?>
                                    <?php } } else { ?>
                                    <svg class="icon icon-lock"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-lock"></use></svg>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        <?php } ?>
    </ul>
</div>
<?php } else { ?>
<div class="block">
    <p class="block">
       <?= __(' There are currently no %s.', $title); ?>
    </p>
</div>
<?php } ?>

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