<?php

// Render agent summary header (menu/title/preview)
echo $this->view->render('inc/tpl/partials/agent/summary.tpl.php', [
    'title' => __('Delete Agent'),
    'agent' => $agent,
    'agentAuth' => $agentAuth
]);

?>
<div class="block">
    <form id="agent_delete" method="post">
        <input type="hidden" name="id" value="<?=$_GET['id']; ?>">
        <input type="hidden" name="leads_count" value="<?=$leads; ?>">
        <section id="agent-delete-buttons">
            <p><?= __('Are you sure you want to delete this agent?'); ?></p>
            <div class="btns">
                <button type="submit" class="btn btn--negative"><?= __('Yes, Delete'); ?></button>
                <a href="../../" class="btn"><?= __('Cancel'); ?></a>
            </div>
        </section>
        <?php

            // Require Leads
            if (!empty($leads)) {
                // Output
                echo '<h2>' . __(
                        'What would you like to do with %s\'s %s Leads?',
                        Format::htmlspecialchars($agent['first_name']),
                        '<a href="' . URL_BACKEND . 'leads/?submit=true&agents[]=' . $agent['id'] . '">' . Format::number($leads) . '</a>') .
                    '</h2>';

                // Re-Assign Leads
                if ($agents) {
                    echo '<div class="field">';
                    echo '<label class="field__label">' . __('Re-Assign %s\'s Leads To:', Format::htmlspecialchars($agent['first_name'])) . '</label>';
                    echo '<select class="w1/1" name="agent">';
                    foreach ($agents as $a) {
                        echo '<option value="' . $a['id'] . '"' . ($_POST['agent'] == $a['id'] ? ' selected' : '') . '>' . Format::htmlspecialchars($a['name']) . '</option>';
                    }
                    echo '</select>';
                    echo '</div>';
                }

                // Change Status
                if ($statuses) {
                    echo '<div class="field">';
                    echo '<label class="field__label">' . __('Update Leads Status To:');' . </label>';
                    echo '<select class="w1/1" name="status">';
                    echo '<option value="">-- ' . __('No Change') . ' --</option>';
                    foreach ($statuses as $value => $title) {
                        echo '<option value="' . Format::htmlspecialchars($value) . '"' . ($_POST['status'] == $value ? ' selected' : '') . '>' . Format::htmlspecialchars($title) . '</option>';
                    }
                    echo '</select>';
                    echo '</div>';
                }

        ?>
        <section id="progress" class="hidden">
            <h3 id="import-status-title"><?= __('Deleting Agent'); ?>&hellip;</h3>
            <div class="field">
                <div class="progress"><span class="ui-progressbar-text"></span></div>
            </div>
            <div class="field">
                <div id="import-success" class="notify-success hidden"></div>
            </div>
            <div class="field">
                <div id="import-errors" class="ui-state-error hidden"></div>
            </div>
        </section>
        <?php
            // Confirm
            } else {
                echo '';
            }
        ?>
        <section id="next-import-steps" class="hidden">
            <a href="<?=$settings->URLS['URL_BACKEND']; ?>agents/" class="btn btn--strong"><?=__('Back to Agents'); ?></a>
        </section>
    </form>
</div>
