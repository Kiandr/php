<?php

// Render report summary header (menu/title/preview)
echo $this->view->render('inc/tpl/partials/report/summary.tpl.php', [
    'title' => __('Task Report'),
    'authuser' => $authuser,
    'reportsAuth' => $reportsAuth
]);
?>
<div class="block">

    <div id="task-statistics">

        <form id="task-report-form">
            <div class="field">
                <label class="field__label"><?= __('Agent'); ?></label>
                <select class="w1/1" name="agent_id">
                    <option value=""><?= __('All Agents'); ?></option>
                    <?php foreach ($all_agents as $agent) { ?>
                        <option value="<?=$agent['id']; ?>"<?=($agent['id'] == $_GET['agent_id']) ? ' selected' : ''; ?>><?=$agent['name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="cols">
                <div class="field col w1/2">
                    <label class="field__label"><?= __('Action Plan'); ?></label>
                    <select class="w1/1" name="actionplan_id">
                        <option value=""><?= __('All Plans'); ?></option>
                        <?php foreach ($action_plans as $plan) { ?>
                            <option value="<?=$plan['id']; ?>"<?=($plan['id'] == $_POST['plan']) ? ' selected' : ''; ?>><?=Format::htmlspecialchars($plan['name']); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="field col w1/2">
                    <label class="field__label"><?= __('Task Type'); ?></label>
                    <select class="w1/1" name="type">
                        <option value=""><?= __('All Types'); ?></option>
                        <?php foreach ($type_options as $option) { ?>
                            <option<?=($option == $_POST['type'])? ' selected' : ''; ?>><?=$option; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="cols -marB">

                <div class="col w1/2 field">
                    <label class="field__label"><?= __('Start Date'); ?></label>
                    <input class="w1/1" id="date_start" name="start" value="<?=(!empty($start) ? date('Y-m-d', $start) : 'all'); ?>">
                </div>
                <div class="col w1/2 field">
                    <label class="field__label"><?= __('End Date'); ?></label>
                    <input class="w1/1" id="date_end" name="end" value="<?=(!empty($end) ? date('Y-m-d', $end) : 'all'); ?>">
                </div>

            </div>

            <div class="btns">
                <button class="btn btn--positive" type="submit"><?= __('Save'); ?></button>
            </div>
        </form>

        <div class="table__wrap">
            <table class="table item_content_summaries agent-tasks">

                <thead>
                <tr>
                    <th width="175">&nbsp;</th>
                    <th>&nbsp;</th>
                    <th><?= __('Pending Tasks'); ?></th>
                    <th><?= __('Completed Tasks'); ?></th>
                    <th><?= __('Dismissed Tasks'); ?></th>
                    <th><?= __('Expired Tasks'); ?></th>
                </tr>
                </thead>

                <tbody id="agent-task-counts">
                <tr>
                    <td colspan="6"><?= __('Loading'); ?>...</td>
                </tr>
                </tbody>

            </table>
        </div>

        <?php if (!empty(Settings::getInstance()->MODULES['REW_ISA_MODULE'])) { ?>
            <div class="-marV"><sup style="color: #0096d8">&#8224;</sup> <?= __('Associates share a common Pending and Expired Task count as they are not assigned to specific leads.'); ?></div>
        <?php } ?>


        <div id="task-pie"></div>

    </div>

</div>