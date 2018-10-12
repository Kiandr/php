<div class="menu menu--drop hidden" id="menu--filters">
    <ul class="menu__list">
        <li class="menu__item"><a class="menu__link" href="<?=URL_BACKEND; ?>teams/"><?= __('All'); ?></a></li>
        <li class="menu__item"><a class="menu__link" href="<?=$url . '?type=owned'; ?>"><?= __('Owned'); ?></a></li>
        <li class="menu__item"><a class="menu__link" href="<?=$url . '?type=subscribed'; ?>"><?= __('Subscribed'); ?> </a></li>
        <?php if ($can_view_unsubscribed) {?>
            <li class="menu__item"><a class="menu__link" href="<?=$url . '?type=unsubscribed'; ?>"><?= __('Unsubscribed'); ?></a></li>
        <?php }?>
    </ul>
</div>

<div class="bar">
    <a href="#" class="bar__title" data-drop="#menu--filters"><?=(isset($_GET['type']) ? ' ' . ucwords(htmlspecialchars($_GET['type'])) : ''); ?> <?= __('Teams'); ?> <svg class="icon icon-drop"><use xlink:href="/backend/img/icos.svg#icon-drop" xmlns:xlink="http://www.w3.org/1999/xlink"/></svg></a>
    <div class="bar__actions">
        <?=$can_create ? '<a class="bar__action" href="/backend/teams/add/"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-add"></use></svg></a>' : ''; ?>
    </div>
</div>

<?php if (empty($teams)) { ?>
    <p class="block"><?= __('There are currently no available teams.'); ?></p>

<?php } else { ?>

    <div class="nodes">
        <ul id="team_manager" class="nodes__list">
            <?php foreach ($teams as $team_row) { ?>
                <?php
                    $agent = Backend_Agent::load($team_row['agent_id']);
                    $primary_agent = Backend_Agent::load($team_row->getPrimaryAgent());
                ?>
                <li class="nodes__branch" team="<?=Format::htmlspecialchars($team_row['id']); ?>">
                    <div class="nodes__wrap">
                        <div class="article">
                            <div class="article__body">

                                <?php if (!empty($team_row['image'])) { ?>
                                    <div class="article__thumb thumb thumb--medium">
                                        <img src="/thumbs/60x60/uploads/teams/<?=urlencode($team_row['image']); ?>">
                                    </div>
                                <?php } else if (!empty($primary_agent['image'])) { ?>
                                    <div class="article__thumb thumb thumb--medium">
                                        <img src="/thumbs/60x60/uploads/agents/<?=urlencode($primary_agent['image']); ?>">
                                    </div>
                                <?php } else { ?>
                                    <div class="article__thumb thumb thumb--medium -bg-<?=strtolower($primary_agent['last_name'][0]); ?>">
                                    	<span class="thumb__label"><?=$primary_agent['first_name'][0] . $primary_agent['last_name'][0]; ?></span>
                                    </div>
                                <?php } ?>
                                <div class="article__content">
                                    <a class="text text--strong" href="<?=Settings::getInstance()->URLS['URL_BACKEND'] .'teams/summary/?id=' . $team_row['id']; ?>"><?=Format::htmlspecialchars($team_row['name']); ?></a>
                                    <div class="text text--mute"><?=Format::htmlspecialchars($team_row['description']); ?></div>
                                </div>
                            </div>
                            <div class="article__foot">

                                <?php if (!empty($primary_agent)) {?>
                                    <a href="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>teams/members/edit/?id=<?=$team_row['id']; ?>&agent=<?=$primary_agent->getId(); ?>" class="token">
                                        <?php if (!empty($primary_agent['image'])) { ?>
                                            <div class="token__thumb thumb thumb--tiny" title="<?=($primary_agent->getId() == $authuser->info('id')) ? 'Me' : Format::htmlspecialchars($primary_agent->getName()); ?>">
                                                <img src="/thumbs/60x60/uploads/agents/<?=urlencode($primary_agent['image']); ?>">
                                            </div>
                                        <?php } else { ?>
                                            <div class="token__thumb thumb thumb--tiny -bg-<?=strtolower($primary_agent['last_name'][0]); ?>" title="<?=($primary_agent->getId() == $authuser->info('id')) ? 'Me' : Format::htmlspecialchars($primary_agent->getName()); ?>">
                                                <span class="thumb__label"><?=$primary_agent['first_name'][0] . $primary_agent['last_name'][0]; ?></span>
                                            </div>
                                        <?php } ?>
                                        <span class="token__label"><?=$primary_agent['first_name'] . ' ' .  $primary_agent['last_name']; ?></span>
                                    </a>
                                <?php }?>

                                <?php
                                	$secondary_agents = $team_row->getSecondaryAgents();
                                ?>

                                <?php foreach ($secondary_agents as $secondary_agent) {?>
                                    <?php $secondary_agent = Backend_Agent::load($secondary_agent); ?>
                                    <?php if (!empty($secondary_agent)) {?>
                                        <a href="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>teams/members/edit/?id=<?=$team_row['id']; ?>&agent=<?=$secondary_agent->getId(); ?>" class="token">
                                            <?php if (!empty($secondary_agent['image'])) { ?>
                                                <div class="token__thumb thumb thumb--tiny" title="<?=$secondary_agent['first_name'] . ' ' . $secondary_agent['last_name']; ?>">
                                                    <img src="/thumbs/60x60/uploads/agents/<?=urlencode($secondary_agent['image']); ?>">
                                                </div>
                                            <?php } else { ?>
                                                <div class="token__thumb thumb thumb--tiny -bg-<?=strtolower($secondary_agent['last_name'][0]); ?>" title="<?=$secondary_agent['first_name'] . ' ' .  $secondary_agent['last_name']; ?>">
                                                    <span class="thumb__label"><?=$secondary_agent['first_name'][0] . $secondary_agent['last_name'][0]; ?></span>
                                                </div>
                                            <?php } ?>
                                            <!--<span class="token__label"><?=($secondary_agent->getId() == $authuser->info('id')) ? 'Me' : Format::htmlspecialchars($secondary_agent->getName()); ?></span>-->
                                        </a>
                                    <?php }?>
                                <?php }?>
                            </div>
                        </div>
                        <div class="nodes__actions">
                            <?php if ($team_row['can_delete']) {?>
                                <a class="btn btn--ico btn--ghost delete" href="?delete=<?=$team_row['id']; ?>" onclick="return confirm('<?= __('Are you sure you want to delete this team?'); ?>');"><svg class="icon icon-trash"><use xlink:href="/backend/img/icos.svg#icon-trash"></use></svg></a>
                            <?php } ?>
                        </div>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>

<?php } ?>