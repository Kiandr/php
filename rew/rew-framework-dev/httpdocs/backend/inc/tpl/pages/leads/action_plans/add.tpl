<form action="?submit" method="post" class="rew_check">
    <div class="bar">
        <div class="bar__title"><?= __('New Action Plan'); ?></div>
        <div class="bar__actions">
            <a class="bar__action" href="/backend/leads/action_plans/">
                <svg class="icon">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use>
                </svg>
            </a>
        </div>
    </div>
    <div class="block">
        <div class="cols">
            <div class="field col w3/4">
                <label class="field__label"><?= __('Plan Name'); ?> <em class="required">*</em></label>
                <input class="w1/1" name="name" value="<?=Format::htmlspecialchars($_POST['name']); ?>" required>
            </div>
            <div class="field col w1/4">
                <label class="field__label"><?= __('Label Color'); ?> <em class="required">*</em></label>
                <select class="w1/1" name="style" required>
                    <?php foreach ($actionPlanLabels as $label) { ?>
                        <option value="<?=$label; ?>"<?=($_POST['style'] == $label) ? ' selected': ''; ?>><?=$label; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="field">
            <label class="field__label"><?= __('Description'); ?></label>
            <textarea class="w1/1" rows="10" name="description"><?=Format::htmlspecialchars($_POST['description']); ?></textarea>
        </div>
        <h3><?= __('Task Schedule Days'); ?></h3>
        <div class="field">
            <?php

                // Days of the Week
                $start = strtotime('last Sunday');
                for ($d = 0; $d < 7; $d ++) {
                    echo sprintf('<label class="toggle toggle--stacked"><input type="checkbox" name="day_adjust[]" value="%s"%s><span class="toggle__label"> %s</span></label>',
                        $d,
                        is_array($_POST['day_adjust']) && in_array($d, $_POST['day_adjust']) ? ' checked' : '',
                        date('l', $start)
                    ) . PHP_EOL;
                    $start += 60 * 60 * 24;
                }

            ?>
        </div>
        <p><?= __('Tasks scheduled outside of the checked days will have their date adjusted to the %snext%s available day.', '<span class="text--strong">', '</span>'); ?></p>
        <div class="btns btns--stickyB">
            <span class="R">
                <button type="submit" class="btn btn--positive">
                    <svg class="icon icon-check mar0">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"/>
                    </svg>
                    <?= __('Save'); ?>
                </button>
            </span>
        </div>
    </div>
</form>