<?php

/**
 * Agent menu template
 * @var int $agentId
 * @var REW\Backend\Auth\Agents\AgentAuth $agentAuth
 */

// Current route name
if ($_GET['page'] == 'email') {
    $slug = 'email';
} else {
    $slugs = explode('/', $_GET['page']);
    $slug = $slugs[2];
}

// Check Agent Authorization
$canEdit   = $agentAuth->canEditAgent();
$canDelete = $agentAuth->canDeleteAgent();

// Get Reports Authorization
$reportsAuth = new REW\Backend\Auth\ReportsAuth(Settings::getInstance());

?>
<div class="menu menu--drop hidden" id="menu--filters">
    <ul class="menu__list">
        <li class="menu__item"><a class="menu__link<?=$slug === 'summary' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>agents/agent/summary/?id=<?=$agentId; ?>"><?= __('Summary'); ?></a></li>
        <?php if ($agentAuth->canEmailAgent()) { ?>
            <li class="menu__item"><a class="menu__link<?=$slug === 'email' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>email/?id=<?=$agentId; ?>&type=agents"><?= __('Email'); ?></a></li>
        <?php } ?>
        <?php if ($agentAuth->canSetAutoresponders()) { ?>
            <li class="menu__item"><a class="menu__link<?=$slug === 'autoresponder' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>agents/agent/autoresponder/?id=<?=$agentId; ?>"><?= __('Auto-Responder'); ?></a></li>
        <?php } ?>
        <?php if ($canEdit) { ?>
            <li class="menu__item"><a class="menu__link<?=$slug === 'notifications' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>agents/agent/notifications/?id=<?=$agentId; ?>"><?= __('Notifications'); ?></a></li>
        <?php }?>
        <?php if ($agentAuth->canViewHistory()) { ?>
            <li class="menu__item"><a class="menu__link<?=$slug === 'history' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>agents/agent/history/?id=<?=$agentId; ?>"><?= __('History'); ?></a></li>
        <?php } ?>
        <?php if ($agentAuth->canManagePermissions() && (int) $agentId !== 1) { ?>
            <li class="menu__item"><a class="menu__link<?=$slug === 'permissions' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>agents/agent/permissions/?id=<?=$agentId; ?>"><?= __('Permissions'); ?></a></li>
        <?php }?>
        <?php if ($canEdit && $reportsAuth->canViewAnalyticsReport(Auth::get())) { ?>
            <li class="menu__item"><a class="menu__link<?=$slug === 'networks' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>agents/agent/networks/?id=<?=$agentId; ?>"><?= __('Networks'); ?></a></li>
        <?php }?>
        <?php if ($agentAuth->canSetTasks()) {?>
            <li class="menu__item"><a class="menu__link<?=$slug === 'tasks' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>agents/agent/tasks/?id=<?=$agentId; ?>"><?= __('Tasks'); ?></a></li>
        <?php } ?>
        <li class="menu__item divider"></li>

        <?php if ($canEdit || $canDelete) { ?>
            <li class="menu__item divider"></li>
            <?php if ($canEdit) { ?>
                <li class="menu__item"><a class="menu__link<?=$slug === 'edit' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>agents/agent/edit/?id=<?=$agentId; ?>"><?=$agentAuth->isSelf() ? __('Preferences') : __('Edit Mode'); ?></a></li>
            <?php } ?>
            <?php if ($canDelete) { ?>
                <li class="menu__item"><a class="menu__link menu__link--negative<?=$slug === 'delete' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>agents/agent/delete/?id=<?=$agentId; ?>"><?= __('Delete'); ?></a></li>
            <?php } ?>
        <?php } ?>
    </ul>
</div>