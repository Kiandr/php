<?php

/**
 * Lead menu template
 * @var int $leadId
 * @var REW\Backend\Auth\Leads\LeadAuth $leadAuth
 * @var REW\Backend\Partner\Firstcallagent $fca
 */

// Current route name
if ($_GET['page'] == 'email'){
    $slug = 'email';
} else {
    $slugs = explode('/', $_GET['page']);
    $slug = $slugs[2];
}

// Check Lead Authorization
$canEdit   = $leadAuth->canEditLead();
$canDelete = $leadAuth->canDeleteLead();

?>
<div class="menu menu--drop hidden" id="menu--filters">
    <ul class="menu__list">
        <li class="menu__item"><a class="menu__link<?=$slug === 'summary' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>leads/lead/summary/?id=<?=$leadId; ?>">Summary</a></li>
        <?php if ($leadAuth->canEmailLead()) { ?>
            <li class="menu__item"><a class="menu__link<?=$slug === 'email' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>email?id=<?=$leadId; ?>&type=leads">Email</a></li>
        <?php }?>
        <?php if ($leadAuth->canTextLead()) { ?>
            <li class="menu__item"><a class="menu__link<?=$slug === 'text' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>leads/lead/text/?id=<?=$leadId; ?>">Text</a></li>
        <?php } ?>
        <li class="menu__item"><a class="menu__link<?=$slug === 'history' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>leads/lead/history/?id=<?=$leadId; ?>">History</a></li>
        <li class="menu__item"><a class="menu__link<?=$slug === 'notes' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>leads/lead/notes/?id=<?=$leadId; ?>">Notes</a></li>
        <li class="menu__item"><a class="menu__link<?=$slug === 'visits' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>leads/lead/visits/?id=<?=$leadId; ?>">Visits</a></li>
        <?php if ($leadAuth->canViewMessages()) { ?>
            <li class="menu__item"><a class="menu__link<?=$slug === 'messages' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>leads/lead/messages/?id=<?=$leadId; ?>">Messages</a></li>
        <?php } ?>
        <li class="menu__item"><a class="menu__link<?=$slug === 'forms' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>leads/lead/forms/?id=<?=$leadId; ?>">Forms</a></li>
        <li class="menu__item"><a class="menu__link<?=$slug === 'listings' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>leads/lead/listings/?id=<?=$leadId; ?>">Listings</a></li>
        <li class="menu__item"><a class="menu__link<?=$slug === 'searches' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>leads/lead/searches/?id=<?=$leadId; ?>">Searches</a></li>
        <?php if ($leadAuth->canViewReminders()) { ?>
            <li class="menu__item"><a class="menu__link<?=$slug === 'reminders' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>leads/lead/reminders/?id=<?=$leadId; ?>">Reminders</a></li>
        <?php } ?>
        <?php if ($leadAuth->canViewTransactions()) { ?>
            <li class="menu__item"><a class="menu__link<?=$slug === 'transactions' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>leads/lead/transactions/?id=<?=$leadId; ?>">Transactions</a></li>
        <?php } ?>
        <?php if ($leadAuth->canViewActionPlans()) { ?>
            <li class="menu__item"><a class="menu__link<?=$slug === 'tasks' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>leads/lead/tasks/?id=<?=$leadId; ?>">Tasks</a></li>
        <?php } ?>
        <?php if ($leadAuth->canManageFirstcallagent()) { ?>
            <li class="menu__item"><a class="menu__link<?=$slug === 'fca' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>leads/lead/fca/?id=<?=$leadId; ?>">First Call Agent</a></li>
        <?php } ?>
        <?php if ($canEdit || $canDelete) { ?>
            <li class="menu__item divider"></li>
            <?php if ($canEdit) { ?>
                <li class="menu__item"><a class="menu__link<?=$slug === 'edit' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>leads/lead/edit/?id=<?=$leadId; ?>">Edit Mode</a></li>
            <?php } ?>
            <?php if ($canDelete) { ?>
                <li class="menu__item"><a class="menu__link menu__link--negative<?=$slug === 'delete' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>leads/lead/delete/?id=<?=$leadId; ?>">Delete</a></li>
            <?php } ?>
        <?php } ?>
    </ul>
</div>
