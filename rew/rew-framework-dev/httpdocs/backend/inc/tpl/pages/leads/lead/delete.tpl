<?php

/**
 * @var REW\Backend\View\Interfaces\FactoryInterface $view,
 * @var \Backend_Lead $lead
 * @var REW\Backend\Auth\Leads\LeadAuth $leadAuth
 * @var string $leadId
 */

// Render lead summary header (menu/title/preview)
echo $view->render('::partials/lead/summary.tpl.php', [
    'title' => 'Delete Lead',
    'lead' => $lead,
    'leadAuth' => $leadAuth
]);

?>
<div class="block">
    <form method="post">
        <input type="hidden" name="delete" value="1">
        <p>Are you sure you want to delete this lead?</p>
        <div class="btns">
            <button type="submit" class="btn btn--negative">Yes, Delete</button>
            <a href="<?=sprintf('%sleads/lead/summary/?id=%s', URL_BACKEND, $leadId); ?>" class="btn">Cancel</a>
        </div>
    </form>
</div>
