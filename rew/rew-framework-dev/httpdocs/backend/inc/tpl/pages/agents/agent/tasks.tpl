<?php
// Render agent summary header (menu/title/preview)
echo $this->view->render('inc/tpl/partials/agent/summary.tpl.php', [
    'title' => ($agent['id'] == $authuser->info('id') ? __('My Tasks') : __('Tasks for %s (Agent)', $agent['name'] )),
    'agent' => $agent,
    'agentAuth' => $agentAuth
]);
?>
<section>

    <?php $page->container('task-list')->module($task_list)->display(); ?>

</section>