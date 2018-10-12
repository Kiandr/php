<?php

/**
 * Lead menu template
 *
 * @var REW\Core\Interfaces\AuthInterface $authuser
 * @var REW\Backend\Auth\LeadsAuth $leadsAuth
 */

?>
<div class="menu menu--drop hidden" id="menu--filters">
    <ul class="menu__list">
        <?php if ($leadsAuth->canManageLeads($authuser)) { ?>
            <li class="menu__item"><a class="menu__link" href="<?=Settings::getInstance()->URLS['URL_BACKEND'];?>leads/?view=all">All Leads</a></li>
        <?php } ?>
        <?php if ($leadsAuth->canBeAssignedLeads($authuser)) { ?>
            <li class="menu__item"><a class="menu__link" href="<?=Settings::getInstance()->URLS['URL_BACKEND'];?>leads/?view=my-leads">My Leads</a></li>
        <?php } ?>
        <li class="menu__item"><a class="menu__link" href="<?=Settings::getInstance()->URLS['URL_BACKEND'];?>leads/?view=inquiries">Inquired</a></li>
        <li class="menu__item"><a class="menu__link" href="<?=Settings::getInstance()->URLS['URL_BACKEND'];?>leads/?view=accepted">Accepted</a></li>
        <li class="menu__item"><a class="menu__link" href="<?=Settings::getInstance()->URLS['URL_BACKEND'];?>leads/?view=pending">Pending</a></li>
        <?php if ($leadsAuth->canManageLeads($authuser) || $authuser->isLender()) { ?>
            <li class="menu__item"><a class="menu__link" href="<?=Settings::getInstance()->URLS['URL_BACKEND'];?>leads/?view=unassigned">Unassigned</a></li>
            <li class="menu__item"><a class="menu__link" href="<?=Settings::getInstance()->URLS['URL_BACKEND'];?>leads/?view=rejected">Rejected</a></li>
        <?php } ?>
        <li class="menu__item"><a class="menu__link" href="<?=Settings::getInstance()->URLS['URL_BACKEND'];?>leads/?view=online">Online</a></li>
        <li class="menu__item divider"></li>
        <?php if ($leadsAuth->canAccessSharkTank($authuser) && $isSharktankEnabled) { ?>
            <li class="menu__item"><a class="menu__link" href="<?=Settings::getInstance()->URLS['URL_BACKEND'];?>leads/sharktank/">Shark Tank</a></li>
            <li class="menu__item divider"></li>
        <?php } ?>
        <li class="menu__item"><a class="menu__link" href="<?=Settings::getInstance()->URLS['URL_BACKEND'];?>leads/">Search Leads...</a></li>
        <li class="menu__item">
            <input type="checkbox" id="select_all_leads">
            <label for="select_all_leads"> &nbsp; <span>Select All</span></label>
        </li>
    </ul>
</div>