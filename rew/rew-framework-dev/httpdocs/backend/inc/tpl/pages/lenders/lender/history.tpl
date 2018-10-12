<?php include('inc/tpl/app/menu-lenders.tpl.php'); ?>
<div class="bar">
	<a class="bar__title" data-drop="#menu--filters" href="javascript:void(0);">
        Lender History
        <svg class="icon icon-drop">
            <use xlink:href="/backend/img/icos.svg#icon-drop" xmlns:xlink="http://www.w3.org/1999/xlink"/>
        </svg>
	</a>
	<div class="bar__actions">
		<a class="bar__action" href="<?=URL_BACKEND; ?>lenders/">
            <svg class="icon">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use>
            </svg>
        </a>
	</div>
</div>
<?php include('inc/tpl/app/summary-lender.tpl.php'); ?>

<?php

// Render timeline
if (!empty($history)) {
    echo $this->view->render('inc/tpl/partials/history-timeline.tpl.php', [
        'pagination' => $pagination,
        'history' => $history,
        'view' => 'lender',
        'user' => $lender['id']
    ]);

} else {
    echo '<p class="-padH">' . __('Currently no history available.') . '</p>';

}