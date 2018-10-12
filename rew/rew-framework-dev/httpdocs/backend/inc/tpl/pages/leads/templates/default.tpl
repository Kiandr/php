<div class="bar">
    <div class="bar__title">Templates</div>
    <div class="bar__actions">
        <a class="bar__action" href="template/">
            <svg class="icon">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use>
            </svg>
        </a>
    </div>
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
                                <a class="btn btn--ico btn--ghost delete" href="?tab=templates&delete=<?= $tmp['id']; ?>" onclick="return confirm('Are you sure you want to delete this template?');">
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
    <p class="block">There are currently no templates to manage.</p>
<?php } ?>
