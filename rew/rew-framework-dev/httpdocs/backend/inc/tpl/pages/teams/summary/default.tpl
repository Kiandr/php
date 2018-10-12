<?php include('inc/tpl/app/menu-teams.tpl.php'); ?>
<div class="bar">
    <a class="bar__title" data-drop="#menu--filters" href="javascript:void(0);"><?= __('Team Summary'); ?> <svg class="icon icon-drop"><use xlink:href="/backend/img/icos.svg#icon-drop" xmlns:xlink="http://www.w3.org/1999/xlink"/></svg></a>
    <div class="bar__actions">
        <a class="bar__action timeline__back" href="/backend/teams/">
            <svg class="icon">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use>
            </svg>
        </a>
    </div>
</div>
<?php include('inc/tpl/app/summary-team.tpl.php'); ?>

<div class="block">

    <div class="keyvals keyvals--bordered -marB">
        <div class="keyvals__row"><span class="keyvals__key text text--strong"><?= __('Created'); ?></span><span class="keyvals__val text text--mute"><?=Format::dateRelative($team['timestamp']); ?></span></div>
        <?php if ($teamSubdomainEnabled) { ?>
            <div class="keyvals__row">
                <span class="keyvals__key text text--strong"><?= __('Subdomain'); ?></span>
                <span class="keyvals__val text text--mute">
                    <?php if($team['subdomain'] != 'false') { ?><?=$team['subdomain_link']; ?><?php } else { echo __('None'); } ?>
                </span>
            </div>
            <div class="keyvals__row">
                <span class="keyvals__key text text--strong"><?=__('Enabled Subdomain Addons'); ?></span>
                <span class="keyvals__val text text--mute">
                    <?=ucwords(implode(', ', $team['subdomain_addons'])); ?>
                </span>
            </div>
        <?php } ?>
    </div>
</div>

<div class="divider pad">
    <span class="divider__label divider__label--left"><?= __('Members'); ?></span>
    <?php if ($can_assign) { ?>
        <a href="/backend/teams/members/add/?id=<?=urlencode($_GET['id']); ?>" class="divider__label divider__label--right">
            <svg class="icon">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use>
            </svg>
        </a>
    <?php } ?>
</div>

<div class="nodes">
    <ul id="team_manager" class="nodes__list">
        <li class="nodes__branch">
            <div class="nodes__wrap">
                <div class="article">
                    <div class="article__body">
                        <?php if (!empty($owning_agent['image'])) { ?>
                            <div class="article__thumb thumb thumb--medium">
                                <img src="/thumbs/60x60/uploads/agents/<?=urlencode($owning_agent['image']); ?>">
                            </div>
                        <?php } else { ?>
                            <div class="article__thumb thumb thumb--medium -bg-<?=strtolower($owning_agent->info('last_name')[0]); ?>">
                                <span class="thumb__label"><?=$owning_agent->info('first_name')[0]; ?><?=$owning_agent->info('last_name')[0]; ?></span>
                            </div>
                        <?php }?>
                        <div class="article__content">
                            <div class="text text--strong"><?=Format::htmlspecialchars($owning_agent['first_name'] . ' ' . $owning_agent['last_name']); ?></div>
                            <div class="text text--mute">
                                <span><?= __('Team Owner'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="nodes__actions">
                    <?=$can_edit
                        ? '<a class="btn btn--ghost edit" href="' . URL_BACKEND . 'teams/members/edit/?id=' . urlencode($_GET['id']) . '&agent=' . $owning_agent['id'] . '">' . __('Edit') . '</a>'

                        : ''
                    ?>
            	</div>
            </div>
        </li>
        <?php if (!empty($agents)) { ?>
            <?php foreach ($agents as $agent_permissions) { ?>
                <?php $agent = Backend_Agent::load($agent_permissions['agent_id']); ?>
                <li class="nodes__branch" agent="<?=Format::htmlspecialchars($agent->getId()); ?>">
                    <div class="nodes__wrap">
                        <div class="article">
                            <div class="article__body">
                                <?php if (!empty($agent['image'])) { ?>
                                    <div class="article__thumb thumb thumb--medium">
                                        <img src="/thumbs/60x60/uploads/agents/<?=urlencode($agent['image']); ?>">
                                    </div>
                                <?php } else { ?>
                                    <div class="article__thumb thumb thumb--medium -bg-<?=strtolower($agent['last_name'][0]); ?>">
                                        <span class="thumb__label"><?=$agent['first_name'][0] . $agent['last_name'][0]; ?></span>
                                    </div>
                                <?php } ?>
                                <div class="article__content">
                                    <?=$can_edit ? '<a class="text text--strong" href="'. URL_BACKEND . 'agents/agent/summary?id=' . $agent_permissions['agent_id'] . '">' : ''; ?><?=Format::htmlspecialchars($agent->getName()); ?><?=$can_edit ? '</a>' : ''; ?>
                                    <div class="text text--mute"><?=($agent_permissions['agent_id'] == $team->getPrimaryAgent()) ? __('Owner') : __('Member'); ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="nodes__actions">
                            <?php if ($agent_permissions['agent_id'] != $team->getPrimaryAgent()) { ?>
                                <?=$can_edit
                                    ? '<a class="btn btn--ghost edit" href="' . URL_BACKEND . 'teams/members/edit/?id=' . urlencode($_GET['id']) . '&agent=' . $agent_permissions['agent_id'] . '">' . __('Edit') . '</a>'
                                    : ''
                                ?>
                                <?=$can_unassign
                                    ? '<a class="btn btn--ghost delete" href="?id=' . urlencode($_GET['id']) . '&delete=' . $agent_permissions['agent_id'] . '" onclick="return confirm(\'' . __('Are you sure you want to remove this agent from the team?') . '\');"><svg class="icon icon-trash mar0"><use xlink:href="/backend/img/icos.svg#icon-trash"/></svg></a>'
                                    : ''
                                ?>
                            <?php } ?>
                        </div>
                    </div>
                </li>
            <?php } ?>
        <?php } ?>
    </ul>
</div>

<div class="divider pad">
    <span class="divider__label divider__label--left"><?= __('Listings'); ?></span>
</div>

<?php if (empty($listings)) { ?>
    <p class="block"><?= __('There are currently no listing shared with this team.'); ?></p>

<?php } else { ?>
    <div class="nodes">
        <ul id="team_listings_manager" class="nodes__list">

            <?php foreach ($listings as $listing) { ?>

                <li class="nodes__branch">
                    <div class="article">
                        <div class="nodes__wrap">
                            <div class="article">
                                <div class="article__body">
                                    <div class="article__thumb thumb thumb--medium">
                                        <?php if (!empty($listing['image'])) { ?>
                                            <img src="<?=$listing['image']; ?>" alt="">
                                        <?php } else { ?>
                                            <img src="/thumbs/200x200/uploads/listings/na.png" alt="">
                                        <?php } ?>
                                    </div>
                                    <div class="article__content">
                                       <a href="../listings/listing/?team=<?=$_GET['id']; ?>&id=<?=$listing['id']; ?>&feed=<?=$listing['feed']; ?>" class="text text--strong"><?=$listing['title']; ?></a>
                                       <div class="text text--mute">$<?=Format::number($listing['price']); ?> - <?=$listing['address']; ?>, <?=$listing['city']; ?>, <?=$listing['state']; ?></div>
                                    </div>
                                </div>

                            </div>
                            <div class="nodes__actions">
                                <?php if ($can_feature) {?>
                                    <a class="btn btn--ico btn--ghost edit" href="listings/listing/?team=<?=$_GET['id']; ?>&id=<?=$listing['id']; ?>&feed=<?=$listing['feed']; ?>"><svg class="icon icon-team mar0"><use xlink:href="/backend/img/icos.svg#icon-team"/></svg></a>
                                <?php } ?>
                                <a class="btn btn--ico btn--ghost view" href="<?=$listing['link']; ?>" target="_blank"><svg class="icon icon-view mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-view"></use></svg></a>
                            </div>
                        </div>
                    </div>
                </li>
            <?php } ?>
            <?php if ($moreListings) { ?>
                <li class="nodes__branch">
                    <div class="article">
                        <div class="nodes__wrap">
                            <a href="../listings/?id=<?= ((int) $team->getId()); ?>&limit=<?= ((int) $page_limit); ?>&p=2"><?= __('More Listings'); ?></a>
                        </div>
                    </div>
                </li>
            <?php } ?>

        </ul>
    </div>
<?php } ?>
