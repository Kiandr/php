<?php

/**
 * @see REW\Backend\Controller\Leads\Lead\DotloopController
 *
 * @var array $rate_limit_info
 * @var \Backend_Lead $lead
 * @var REW\Backend\Auth\Leads\LeadAuth $leadAuth
 * @var REW\Backend\View\Interfaces\FactoryInterface $view,
 */

// Render lead summary header (menu/title/preview)
echo $view->render('inc/tpl/partials/lead/summary.tpl.php', [
    'title' => 'DotLoop - Link Lead',
    'lead' => $lead,
    'leadAuth' => $leadAuth
]);

?>
<div class="block">
    <form method="post">
        <input type="hidden" name="connect_lead" value="<?=$lead->getId(); ?>">
        <p>This lead will be linked to your DotLoop account</p>
        <?php if (!empty($rate_limit_info) && $rate_limit_info['remaining'] <= 0) { ?>
            <p class="text text--negative">
                Unable to link lead to DotLoop system: API Rate Rimit has been exceeded. Please try again<span id="dotloop-rate-timer" data-remaining="<?=ceil($rate_limit_info['reset_countdown']/1000); ?>"> in <?=ceil($rate_limit_info['reset_countdown']/1000); ?> seconds</span>.
            </p>
        <?php } else { ?>
            <div class="btns">
                <button type="submit" class="btn btn--positive">Link Lead</button>
                <a href="<?=sprintf('%sleads/lead/summary/?id=%s', URL_BACKEND, $lead->getId());?>" class="btn">Cancel</a>
            </div>
        <?php } ?>
    </form>
</div>
