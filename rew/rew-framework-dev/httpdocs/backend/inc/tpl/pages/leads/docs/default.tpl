<?php

// Manage Categories
if ($_GET['tab'] === 'categories') { ?>

    <?php if ($leadsAuth->canManageDocuments($authuser)) { ?>
        <div class="menu menu--drop menu--copy hidden" id="menu--filters" style="min-width: 0;">
            <ul class="menu__list">
                <li class="menu__item">
                    <a class="menu__link" href="<?= Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/campaigns'; ?>"<?= !isset($_GET['personal']) ? ' class="current"' : ''; ?>>
                        <?= __('All Files'); ?>
                    </a>
                </li>
                <li class="menu__item">
                    <a class="menu__link" href="<?= Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/campaigns?personal'; ?>"<?= isset($_GET['personal']) ? ' class="current"' : ''; ?>>
                        <?= __('Your Files'); ?>
                    </a>
                </li>
            </ul>
        </div>
    <?php } ?>

    <div class="bar">
        <div class="bar__title">
            <?= __('Categories'); ?><?= ($leadsAuth->canViewFiles($authuser) && isset($_GET['personal'])) ? ': ' . htmlspecialchars($authuser->info('first_name') . ' ' . $authuser->info('last_name')) : ''; ?>
        </div>
        <div class="bar__actions">
            <?php if ($leadsAuth->canManageDocuments($authuser)) { ?>
                <a class="bar__action" data-drop="#menu--filters">
                    <svg class="icon icon-ellipses mar0">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-ellipses"></use>
                    </svg>
                </a>
            <?php } ?>
            <a class="bar__action" href="category/">
                <svg class="icon icon-add mar0">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use>
                </svg>
            </a>
        </div>
    </div>

    <div class="block">
        <ul class="tabs">
            <li><a href="?tab=documents"><?= __('Form Letters'); ?></a></li>
            <li class="current"><a href="?tab="><?= __('Categories'); ?></a></li>
        </ul>
    </div>

    <?php if (!empty($docs)) { ?>
        <div class="nodes">
            <ul class="nodes__list">
                <?php foreach ($docs as $cat_id => $cat) { ?>
                    <li class="nodes__branch">

                        <div class="nodes__wrap">
                            <div class="article">
                                <div class="article__body">
                                    <div class="article__thumb thumb thumb--medium -bg-rew2">
                                        <svg class="icon icon--invert">
                                            <use xlink:href="/backend/img/icos.svg#icon-groups"></use>
                                        </svg>
                                    </div>
                                    <div class="article__content">
                                        <a class="text text--strong"
                                           href="category/?id=<?= $cat_id; ?>"><?= Format::htmlspecialchars($cat['name']); ?></a>
                                    </div>
                                </div>
                            </div>
                            <div class="nodes__actions">
                                <a class="btn btn--ico btn--ghost" href="?tab=categories&delete=<?= $cat_id; ?>"
                                   onclick="return confirm('<?= __('Are you sure you want to delete this category?'); ?>');">
                                    <svg class="icon">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </li>
                <?php } ?>
            </ul>
        </div>
    <?php } else { ?>
        <p class="block"><?= __('There are currently no categories to manage.'); ?></p>
    <?php } ?>
    <?php

// Manage Documents
} elseif ($_GET['tab'] === 'documents') { ?>

    <header class="bar">
        <div class="bar__title"><?= __('Form Letters'); ?></div>
        <?php if ($can_manage_all || $leadsAuth->canViewOwn($authuser)) { ?>
            <div class="bar__actions">
                <a class="bar__action" href="document/?category=<?= $cat_id; ?>">
                    <svg class="icon">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use>
                    </svg>
                </a>
            </div>
        <?php } ?>
    </header>

    <div class="block">
        <ul class="tabs">
            <li class="current"><a href="?tab=documents"><?= __('Form Letters'); ?></a></li>
            <li><a href="?tab="><?= __('Categories'); ?></a></li>
        </ul>
    </div>

    <?php if (!empty($docs)) { ?>
        <div class="nodes" id="documents-list">
            <?php foreach ($docs as $cat_id => $cat) { ?>
                <ul class="nodes__list">
                    <?php if (!empty($cat['docs'])) { ?>
                        <?php if (!empty($cat['name'])) { ?>
                            <div class="divider padL padT">
                                <span class="divider__label divider__label--left text text--small"><?= Format::htmlspecialchars($cat['name']); ?></span>
                            </div>
                        <?php } ?>

                        <?php foreach ($cat['docs'] as $doc_id => $doc) { ?>
                            <li class="nodes__branch">

                                <div class="nodes__wrap">
                                    <div class="article">
                                        <div class="article__body">
                                            <div class="article__thumb thumb thumb--medium -bg-rew2">
                                                <svg class="icon icon--invert">
                                                    <use xlink:href="/backend/img/icos.svg#icon-email"></use>
                                                </svg>
                                            </div>
                                            <div class="article__content">
                                                <?php if (!$can_manage_all && $authuser->info('id') != $doc['agent_id']) { ?>
                                                    <span class="text text--strong">
                                                        <a class="view" href="document/?view=<?= $doc_id; ?>">
                                                            <?= Format::htmlspecialchars($doc['doc_name']); ?>
                                                        </a>
                                                    </span>
                                                    <?php if (!empty($doc['shared'])) echo '(shared)'; ?>
                                                <?php } else { ?>
                                                    <span class="text text--strong">
                                                        <a href="document/?id=<?= $doc_id; ?>">
                                                            <?= Format::htmlspecialchars($doc['doc_name']); ?>
                                                        </a>
                                                    </span>
                                                    <?php if (!empty($doc['shared'])) echo '(shared)'; ?>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="nodes__actions">
                                        <?php if (!$can_manage_all && $authuser->info('id') != $doc['agent_id']) { ?>
                                        <?php } else { ?>
                                            <a class="btn btn--ico btn--ghost"
                                               href="?tab=documents&delete=<?= $doc_id; ?>"
                                               onclick="return confirm('<?= __('Are you sure you want to delete this document?'); ?>');">
                                                <svg class="icon">
                                                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use>
                                                </svg>
                                            </a>
                                        <?php } ?>
                                    </div>
                                </div>

                            </li>
                        <?php } ?>

                    <?php } else { ?>

                        <?php /* Commented out while categories are not present
            <li>
                <div class="block"><p>There are currently no form letters listed under this category. <a href="document/?category=<?=$cat_id; ?>">Add Form Letter</a></p></div>
            </li>
            */ ?>

                    <?php } ?>
                </ul>
            <?php } ?>
        </div>
    <?php } else { ?>
        <p class="block"><?= __('There are currently no form letters to manage.'); ?></p>
    <?php } ?>
    <?php

// Manage Documents
} elseif ($_GET['tab'] === 'templates') { ?>

    <div class="bar">
        <div class="bar__title"><?= __('Templates'); ?></div>
        <div class="bar__actions">
            <a class="bar__action" href="template/">
                <svg class="icon">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use>
                </svg>
            </a>
        </div>
    </div>

    <div class="block">
        <ul class="tabs">
            <li><a href="?tab=documents"><?= __('Form Letters'); ?></a></li>
            <li class="current"><a href="?tab=templates"><?= __('Templates'); ?></a></li>
            <li><a href="?tab="><?= __('Categories'); ?></a></li>
        </ul>
    </div>

    <?php if (!empty($tmps)) { ?>
        <div class="nodes">
            <ul id="templates-list" class="nodes__list">

                <?php foreach ($tmps as $tmp) { ?>
                    <li class="nodes__branch">

                        <div class="nodes__wrap">
                            <div class="article">
                                <div class="article__body">
                                    <div class="article__thumb thumb thumb--medium -bg-rew2">
                                        <svg class="icon icon--invert">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-page"></use>
                                        </svg>
                                    </div>
                                    <div class="article__content">
                                        <div class="text text--strong">
                                            <?php if ($tmp['agent_id'] != $authuser->info('id') && $tmp['share'] == 'true') { ?>
                                                <a class="text text--strong view"
                                                   href="template/?view=<?= $tmp['id']; ?>">
                                                    <?= Format::htmlspecialchars($tmp['name']); ?>
                                                </a>
                                            <?php } else { ?>
                                                <a class="text text--strong" href="template/?id=<?= $tmp['id']; ?>">
                                                    <?= Format::htmlspecialchars($tmp['name']); ?>
                                                </a>
                                            <?php } ?>
                                            <?php if ($tmp['share'] == 'true') echo '(shared)'; ?>
                                        </div>
                                        <a class="token"
                                           href="<?= URL_BACKEND; ?>agents/agent/summary/?id=<?= $tmp['agent_id']; ?>">
                                            <span class="token__thumb thumb thumb--tiny">
                                                <img src="/thumbs/312x312/uploads/agents/<?= (!empty($tmp['agent']['image']) ? $tmp['agent']['image'] : 'na.png'); ?>" alt="" height="24" width="24">
                                            </span>
                                            <span class="token__label"><?= Format::htmlspecialchars($tmp['agent']['name']); ?></span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="nodes__actions">
                                <?php if ($tmp['agent_id'] != $authuser->info('id') && $tmp['share'] == 'true') { ?>
                                    <a class="btn btn--ico btn--ghost view" href="template/?view=<?= $tmp['id']; ?>">
                                        <svg class="icon icon-view mar0">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-view"></use>
                                        </svg>
                                    </a>
                                <?php } else { ?>
                                    <a class="btn btn--ico btn--ghost view" href="template/?view=<?= $tmp['id']; ?>">
                                        <svg class="icon icon-view mar0">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-view"></use>
                                        </svg>
                                    </a>
                                    <a class="btn btn--ico btn--ghost delete" href="?tab=templates&delete=<?= $tmp['id']; ?>" onclick="return confirm('<?= __('Are you sure you want to delete this template?'); ?>');">
                                        <svg class="icon icon-trash mar0">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use>
                                        </svg>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>

                    </li>
                <?php } ?>
            </ul>
        </div>
    <?php } else { ?>
        <p class="block"><?= __('There are currently no templates to manage.'); ?></p>
    <?php } ?>
<?php } ?>