<?php

// Render lead summary header (menu/title/preview)
echo $this->view->render('inc/tpl/partials/lead/summary.tpl.php', [
    'title' => 'Lead Tasks',
    'lead' => $lead,
    'leadAuth' => $leadAuth
]);

// Display task-list module
$page->container('task-list')->module($task_list)->display();