<form action="?submit" method="post" class="rew_check">
    <input type="hidden" name="id" value="<?=$event['id']; ?>">
    <input type="hidden" name="date_id" value="<?=$event['date_id']; ?>">

    <div class="bar">
        <div class="bar__title"><?= __('Edit Event'); ?></div>
        <div class="bar__actions">
            <?php if ($can_delete_events) { ?>
                <button class="bar__action delete" onclick="return confirm('<?= __('Are you sure you want to delete this event?'); ?>');" type="submit" name="delete" value=""><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-trash"></use></svg></button>
            <?php } ?>
            <a class="bar__action" href="/backend/calendar/"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
        </div>
    </div>

    <div class="block">
        <div class="cols">
            <div class="field col w3/4">
                <label class="field__label"><?= __('Title'); ?> <em class="required">*</em></label>
                <input class="w1/1" type="text" name="title" value="<?=$event['title']; ?>">
            </div>
            <div class="field col w1/4">
                <label class="field__label"><?= __('Type'); ?></label>
                <select class="w1/1" name="type">
                    <option value=""><?= __('Un-Categorized'); ?></option>
                    <?php foreach ($types as $type) { ?>
                        <option value="<?=$type['value']; ?>" <?=$event['type'] == $type['value'] ? 'selected' : ''; ?>><?=$type['title']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="field">
            <label class="field__label"><?= __('Description'); ?></label>
            <textarea class="w1/1" id="body" name="body" cols="24" rows="4"><?=$event['body']; ?></textarea>
        </div>
        <div class="cols">
            <div class="field col w1/2">
                <label class="field__label"><?= __('Start Date'); ?></label>
                <input class="w1/1" type="text" name="start_date" data-value="<?=$event['start_date']; ?>">
                <div class="hide-on-all-day">
                    <label class="field__label"><?= __('Start Time'); ?></label>
                    <input class="w1/1" type="text" name="start_time" data-value="<?=$event['start_time']; ?>">
                </div>
            </div>
            <div class="field col w1/2">
                <label class="field__label"><?= __('End Date'); ?></label>
                <input class="w1/1" type="text" name="end_date" data-value="<?=$event['end_date']; ?>">
                <div class="hide-on-all-day">
                    <label class="field__label"><?= __('End Time'); ?></label>
                    <input class="w1/1" type="text" name="end_time" data-value="<?=$event['end_time']; ?>">
                </div>
            </div>
        </div>
        <div class="field">
            <label class="toggle">
                <input type="checkbox" name="all_day" value="true" <?=($event['all_day'] == 'true' ? 'checked' : ''); ?>>
                <span class="toggle__label"><?= __('All Day Event'); ?></span>
            </label>
        </div>
        <?php if (!empty($options)) { ?>
            <label class="field__label"><?= __('Share With Agents:'); ?> <span class="hint">(<a href="#all"><?= __('All'); ?></a> / <a href="#none"><?= __('None'); ?></a>)</span></label>
            <div>
                <?php foreach ($options['agents'] as $agent) { ?>
                    <label class="boolean toggle toggle--stacked">
                        <input type="checkbox" name="agents[]" value="<?=$agent['value']; ?>" <?=(is_array($event['agents']) && in_array($agent['value'], $event['agents']) ? 'checked' : ''); ?>>
                        <span class="toggle__label"><?=$agent['title']; ?></span>
                    </label>
                <?php } ?>
            </div>
        <?php } ?>

        <div class="btns btns--stickyB">
            <span class="R">
                <button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?></button>
                <a href="/backend/calendar/event/edit/?id=<?=$event['id']; ?>" class="btn"><?= __('Reset'); ?></a>
            </span>
        </div>

    </div>

</form>
