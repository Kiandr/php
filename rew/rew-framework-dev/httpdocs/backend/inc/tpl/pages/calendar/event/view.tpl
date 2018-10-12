<?php

/**
 * @var array $event
 * @var array $types
 */

?>
<div class="bar">
    <div class="bar__title"><?= __('Event'); ?> <?= $event['title']; ?></div>
</div>

<div class="block">
    <div class="keyvals keyvals--bordered">
        <div class="keyvals__row keyvals__row--rows@sm">
            <span class="-padB0@sm keyvals__key text text--strong"><?= __('Title'); ?></span>
            <span class="-padT0@sm keyvals__val text"><?= $event['title']; ?></span>
        </div>
        <div class="keyvals__row keyvals__row--rows@sm">
            <span class="-padB0@sm keyvals__key text text--strong"><?= __('Type'); ?></span>
            <span class="-padT0@sm keyvals__val text"><?= $type['title']; ?></span>
        </div>
        <div class="keyvals__row keyvals__row--rows@sm">
            <span class="-padB0@sm keyvals__key text text--strong"><?= __('Description'); ?></span>
            <span class="-padT0@sm keyvals__val text"><?= $event['body'] == '' ? 'None' : $event['body']; ?></span>
        </div>
        <div class="keyvals__row keyvals__row--rows@sm">
            <span class="-padB0@sm keyvals__key text text--strong"><?= __('Start Date'); ?></span>
            <span class="-padT0@sm keyvals__val text"><?= $event['start_date']; ?></span>
        </div>
        <div class="keyvals__row keyvals__row--rows@sm <?= ($event['all_day'] == 'true' ? 'hidden' : ''); ?>">
            <span class="-padB0@sm keyvals__key text text--strong"><?= __('Start Time'); ?></span>
            <span class="-padT0@sm keyvals__val text"><?= $event['start_time']; ?></span>
        </div>
        <div class="keyvals__row keyvals__row--rows@sm">
            <span class="-padB0@sm keyvals__key text text--strong"><?= __('End Date'); ?></span>
            <span class="-padT0@sm keyvals__val text"><?= $event['end_date']; ?></span>
        </div>
        <div class="keyvals__row keyvals__row--rows@sm <?= ($event['all_day'] == 'true' ? 'hidden' : ''); ?>">
            <span class="-padB0@sm keyvals__key text text--strong"><?= __('End Time'); ?></span>
            <span class="-padT0@sm keyvals__val text"><?= $event['end_time']; ?></span>
        </div>
    </div>
</div>