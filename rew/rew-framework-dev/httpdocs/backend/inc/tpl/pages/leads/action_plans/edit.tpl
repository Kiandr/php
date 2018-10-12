<form action="?submit" method="post" class="rew_check">
	<input id="id" type="hidden" name="id" value="<?=$action_plan['id']; ?>">
    <input id="actionplan-id" type="hidden" name="actionplan_id" value="<?=$action_plan['id']; ?>">
    <div class="bar">
        <div class="bar__title"><?=$action_plan['name']; ?></div>
        <div class="bar__actions">
            <a class="bar__action" href="/backend/leads/action_plans/">
                <svg class="icon">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use>
                </svg>
            </a>
        </div>
    </div>
    <div class="block">
        <div id="actionPlan-Details">
            <div class="cols">
                <div class="field col w3/4">
                    <label class="field__label"><?= __('Plan Name'); ?> <em class="required">*</em></label>
                    <input class="w1/1" id="plan-name" name="name" value="<?=Format::htmlspecialchars($action_plan['name']); ?>" required>
                </div>
                <div class="field col w1/4">
                    <label class="field__label"><?= __('Label Color'); ?> <em class="required">*</em></label>
                    <select class="w1/1" name="style" required>
                        <?php foreach ($actionPlanLabels as $label) { ?>
                            <option value="<?=$label; ?>"<?=($action_plan['style'] == $label) ? ' selected': ''; ?>><?=$label; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="field">
                <label class="field__label"><?= __('Description'); ?></label>
                <textarea class="w1/1" rows="10" name="description"><?=Format::htmlspecialchars($action_plan['description']); ?></textarea>
            </div>
            <h3><?= __('Task Schedule Days'); ?></h3>
            <div class="field">
                <?php

                    // Days of the Week
                    $start = strtotime('last Sunday');
                    $day_adjust = explode(',', $action_plan['day_adjust']);
                    for ($d = 0; $d < 7; $d ++) {
                        echo sprintf('<label class="toggle--stacked"><input type="checkbox" name="day_adjust[]" value="%s"%s><span class="toggle__label"> %s</span></label>',
                            $d,
                            is_array($day_adjust) && in_array($d, $day_adjust) ? ' checked' : '',
                            date('l', $start)
                        ) . PHP_EOL;
                        $start += 60 * 60 * 24;
                    }

                ?>
            </div>
            <p><?= __('Tasks scheduled outside of the checked days will have their date adjusted to the %snext%s available day.', '<span class="text--strong">', '</span>'); ?></p>
        </div>
        <div id="plan-builder">
            <h3 class="panel__hd" style="margin-left: -8px; margin-right: -8px;"><?= __('Tasks'); ?></h3>
            <ul class="nodes -marB" id="all-tasks">
                <div class="loading"><p><?= __('Loading'); ?>...</p></div>
            </ul>
            <span class="trig">
                <button class="btn select-type" data-action="type">
                    <svg class="icon icon-add"><use xlink:href="/backend/img/icos.svg#icon-add"/></svg> <?= __('New Task'); ?>
                </button>
                <ul class="mnu task-type-menu" style="display:none;">
                    <?php foreach ($type_options as $option) { ?>
                        <li><a class="task-action" data-action="add" data-type="<?=$option; ?>" data-task=""><?=$option; ?></a></li>
                    <?php } ?>
                </ul>
            </span>
        </div>
        <br><br>
        <div class="btns btns--stickyB">
            <span class="R">
                <button type="submit" class="btn btn--positive"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"/></svg> <?= __('Save'); ?></button>
            </span>
        </div>
    </div>
</form>