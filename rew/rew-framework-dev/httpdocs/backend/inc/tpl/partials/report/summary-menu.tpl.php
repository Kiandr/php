<?php

/**
 * Report menu template
 * @var AuthInterface $authuser
 * @var REW\Backend\Auth\ReportsAuth $reportsAuth
 */

// Current route name
$slugs = explode('/', $_GET['page']);
$slug = $slugs[1];

?>
<div class="menu menu--drop hidden" id="menu--filters">
    <ul class="menu__list">
    	<?php if ($reportsAuth->canViewAnalyticsReport($authuser)) { ?>
        	<li class="menu__item"><a class="menu__link<?=$slug === 'analytics' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>reports/analytics/"><?= __('Google Analytics'); ?></a></li>
        <?php } ?>
        <?php if ($reportsAuth->canViewResponseReport($authuser) || $reportsAuth->canViewOwnResponseReports($authuser)) { ?>
        	<li class="menu__item"><a class="menu__link<?=$slug === 'agents' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>reports/agents/"><?= __('Agent Response Report'); ?></a></li>
        <?php } ?>
        <?php if ($reportsAuth->canViewListingReport($authuser)) { ?>
        	<li class="menu__item"><a class="menu__link<?=$slug === 'listing' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>reports/listing/"><?= __('MLS® Listing Report'); ?></a></li>
        <?php } ?>
        <?php if ($reportsAuth->canViewDialerReport($authuser) || $reportsAuth->canViewOwnDialerReport($authuser)) { ?>
        	<li class="menu__item"><a class="menu__link<?=$slug === 'dialer' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>reports/dialer/"><?= __('Access REW Dialer Report'); ?></a></li>
        <?php } ?>
        <?php if ($reportsAuth->canViewActionPlanReports($authuser)) { ?>
        	<li class="menu__item"><a class="menu__link<?=$slug === 'action_plans' ? ' is-active' : ''; ?>" href="<?=URL_BACKEND; ?>reports/action_plans/"><?= __('Task Report'); ?></a></li>
        <?php } ?>
    </ul>
</div>