<?php

include('inc/tpl/app/menu-associates.tpl.php');

// Render associate summary header (menu/title/preview)
echo $this->view->render('inc/tpl/partials/associate/summary.tpl.php', [
    'title' => __('Associate History'),
    'associate' => $associate,
    'associateAuth' => $associateAuth
]);
?>

<?php

// Render timeline
if (!empty($history)) {
    echo $this->view->render('inc/tpl/partials/history-timeline.tpl.php', [
        'pagination' => $pagination,
        'history' => $history,
        'view' => 'associate',
        'user' => $associate['id']
    ]);

} else {
    echo '<p class="-padH">' . __('Currently no history available.') . '</p>';

}