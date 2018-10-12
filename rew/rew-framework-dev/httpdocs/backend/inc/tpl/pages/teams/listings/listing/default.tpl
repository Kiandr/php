<div class="bar">
    <div class="bar__title">Featured Agents for <?=Format::htmlspecialchars($listing_title); ?></div>
    <div class="bar__actions">
        <?php if (!empty($possible_agents)) { ?>
            <a class="bar__action positive" href="add/?id=<?=urlencode($_GET['id']); ?>&feed=<?=urlencode($_GET['feed']); ?>&team=<?=urlencode($_GET['team']); ?>">
                <svg class="icon">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use>
                </svg>
            </a>
        <?php }?>
        <a class="bar__action" href="<?=URL_BACKEND; ?>teams/listings/?id=<?=urlencode($_GET['team']); ?>">
            <svg class="icon">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"/>
            </svg>
        </a>
    </div>
</div>

<?php if (!empty($owning_agent)) { ?>
    <div class="block">
        <div class="article">
            <div class="article__body">
                <?php if (!empty($owning_agent['image'])) { ?>
                    <div class="article__thumb thumb thumb--large">
                        <img src="/thumbs/60x60/uploads/agents/<?=urlencode($owning_agent['image']); ?>" alt="">
                    </div>
                <?php } else { ?>
                    <div class="article__thumb thumb thumb--large -bg-<?=strtolower($owning_agent['last_name'][0]); ?>">
                        <span class="thumb__label"><?=$owning_agent['first_name'][0] . $owning_agent['last_name'][0]; ?></span>
                    </div>
                <?php } ?>
                <div class="article__content">
                    <div class="text text--strong text--large"><?=Format::htmlspecialchars($owning_agent['first_name'] . ' ' . $owning_agent['last_name']); ?></div>
                    <div class="text text--mute">
                        <span><?= __('Owning Agent'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<?php if (!empty($featured_agents)) { ?>
    <div class="nodes -pad">
        <ul id="sortable" class="nodes__list" data-params='<?=htmlentities(json_encode([
            'id' => $_GET['id'],
            'feed' => $_GET['feed'],
            'team' => $_GET['team']
        ])); ?>'>
            <?php foreach ($featured_agents as $agent) : ?>
                <li class="nodes__branch" id="items-<?=$agent['id']; ?>">
                    <div class="nodes__wrap">
                        <div class="handle"></div>
                        <div class="article">
                            <div class="article__body">
                                <?php if (!empty($agent['image'])) { ?>
                                    <div class="article__thumb thumb thumb--medium">
                                        <img src="/thumbs/60x60/uploads/agents/<?=urlencode($agent['image']); ?>" alt="">
                                    </div>
                                <?php } else { ?>
                                    <div class="article__thumb thumb thumb--medium -bg-<?=strtolower($agent['last_name'][0]); ?>">
                                        <span class="thumb__label"><?=$agent['first_name'][0] . $agent['last_name'][0]; ?></span>
                                    </div>
                                <?php } ?>
                                <div class="article__content">
                                    <a class="text text--strong" href="<?=URL_BACKEND; ?>agents/agent/summary/?id=<?=$agent['id']; ?>"><?=Format::htmlspecialchars($agent['name']); ?></a>
                                    <div class="text text--mute"><?=!empty($agent['title']) ? Format::htmlspecialchars($agent['title']) : '<em>' . __('No Title') . '</em>'; ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="nodes__actions">
                            <a class="btn btn--ico btn--ghost" title="<?= __('Delete'); ?>" href="?id=<?=urlencode($_GET['id']); ?>&feed=<?=urlencode($_GET['feed']); ?>&team=<?=urlencode($_GET['team']); ?>&delete=<?=$agent['id']; ?>" onclick="return confirm(<?=__('Are you sure you want to stop featuring this agent?'); ?>)">
                                <svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-trash"></use></svg>
                            </a>
                    </div>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php } else { ?>
    <div class="block">
        <p class="block"><?= __('There are currently no agents being featured by this team.'); ?></p>
    </div>
<?php } ?>