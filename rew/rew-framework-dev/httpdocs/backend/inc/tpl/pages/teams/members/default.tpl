<?php include('inc/tpl/app/menu-teams.tpl.php'); ?>
<div class="bar">
    <a class="bar__title" data-drop="#menu--filters" href="javascript:void(0);"><?= __('Team Members'); ?> <svg class="icon icon-drop"><use xlink:href="/backend/img/icos.svg#icon-drop" xmlns:xlink="http://www.w3.org/1999/xlink"/></svg></a>
    <div class="bar__actions">
        <?php if ($can_assign) {?>
            <a class="bar__action" href="/backend/teams/members/add/?id=<?=urlencode($_GET['id']); ?>">
                <svg class="icon">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use>
                </svg>
            </a>
        <?php } ?>
        <a class="bar__action timeline__back" href="/backend/teams/">
            <svg class="icon">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use>
            </svg>
        </a>
    </div>
</div>
<?php include('inc/tpl/app/summary-team.tpl.php'); ?>

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
                                <div class="article__thumb thumb thumb--medium -bg-<?=strtolower($owning_agent['last_name'][0]); ?>">
                                    <span class="thumb__label"><?=$owning_agent['first_name'][0] . $owning_agent['last_name'][0]; ?></span>
                                </div>
                            <?php } ?>
                            <div class="article__content">
                                <div class="text text--strong"><?=Format::htmlspecialchars($owning_agent['first_name'] . ' ' . $owning_agent['last_name']); ?></div>
                                <div class="text text--mute">
                                    <span><?= __('Team Owner'); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="nodes__actions">
                        <?=$can_edit ? '<a class="btn btn--ghost edit" href="edit/?id=' . urlencode($_GET['id']) . '&agent=' . $owning_agent['id'] . '">' . __('Edit') . '</a>' : '' ?>
                    </div>
                </div>
            </li>
            <?php if (!empty($agents)) { ?>
                <?php foreach ($agents as $agent_permissions) { ?>
                    <?php $agent = Backend_Agent::load($agent_permissions['agent_id']);?>
                    <li class="nodes__branch" agent="<?=Format::htmlspecialchars($agent->getId());?>">
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
                                        <?=$can_edit ? '<a class="text text--strong" href="edit/?id='.$_GET['id'].'&agent='.$agent_permissions['agent_id'].'">' : '';?><?=Format::htmlspecialchars($agent->getName());?><?=$can_edit ? '</a>' : '';?>
                                        <div class="text text--mute"><?=($agent_permissions['agent_id'] == $team->getPrimaryAgent()) ? 'Owner' : 'Member'; ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="nodes__actions">
                                <?php if ($agent_permissions['agent_id'] != $team->getPrimaryAgent()) { ?>
                                    <?=$can_edit ? '<a class="btn btn--ghost edit" href="edit/?id=' . urlencode($_GET['id']) . '&agent=' . $agent_permissions['agent_id'] . '">Edit</a>' : '' ?>
                                    <?=$can_unassign ? '<a class="btn btn--ghost delete" href="?id=' . urlencode($_GET['id']) . '&delete=' . $agent_permissions['agent_id'] . '" onclick="return confirm(\'' . __('Are you sure you want to remove this agent from the team?') . '\');"><svg class="icon icon-trash mar0"><use xlink:href="/backend/img/icos.svg#icon-trash"/></svg></a>' : '' ?>
                                <?php } ?>
                            </div>
                        </div>
                    </li>
                <?php } ?>
            <?php } ?>
        </ul>
    </div>

