<?php

/**
 * @see REW\Backend\Controller\Leads\Lead\Dotloop\LoopController
 *
 * @var REW\Core\Interfaces\FormatInterface $format
 * @var array $loop
 * @var array $rate_limit_info
 */

?>

<?php if (!empty($rate_limit_info) && $rate_limit_info['remaining'] <= 0) { ?>
    <div class="block -marB">
        <p class="text text--negative">
            DotLoop account's API Rate Rimit has been exceeded. Please reload this page<span id="dotloop-rate-timer" data-remaining="<?=ceil($rate_limit_info['reset_countdown']/1000); ?>"> in <?=ceil($rate_limit_info['reset_countdown']/1000); ?> seconds</span> to view additional loop details.
        </p>
    </div>
<?php } ?>

<div>
    <div class="block -marB">
        <h3 class="panel__hd mar0" style="font-size: 18px;">Loop Summary</h3>
        <div class="keyvals keyvals--bordered panel__bd">
            <div class="keyvals__row keyvals__row--rows@sm">
                <span class="keyvals__key text text--strong -padB0@sm">Profile</span>
                <span class="keyvals__val text -padT0@sm"><?=!empty($loop['basic_info']['profile_name']) ? $format->htmlspecialchars($loop['basic_info']['profile_name']) : '-'; ?></span>
            </div>
            <div class="keyvals__row keyvals__row--rows@sm">
                <span class="keyvals__key text text--strong -padB0@sm">Loop ID</span>
                <span class="keyvals__val text -padT0@sm"><?=!empty($loop['basic_info']['dotloop_loop_id']) ? $format->htmlspecialchars($loop['basic_info']['dotloop_loop_id']) : '-'; ?></span>
            </div>
            <div class="keyvals__row keyvals__row--rows@sm">
                <span class="keyvals__key text text--strong -padB0@sm">Name</span>
                <span class="keyvals__val text -padT0@sm"><?=!empty($loop['basic_info']['name']) ? $format->htmlspecialchars($loop['basic_info']['name']) : '-'; ?></span>
            </div>
            <div class="keyvals__row keyvals__row--rows@sm">
                <span class="keyvals__key text text--strong -padB0@sm">Transaction Type</span>
                <span class="keyvals__val text -padT0@sm"><?=!empty($loop['basic_info']['transaction_type']) ? $format->htmlspecialchars($loop['basic_info']['transaction_type']) : '-'; ?></span>
            </div>
            <div class="keyvals__row keyvals__row--rows@sm">
                <span class="keyvals__key text text--strong -padB0@sm">Status</span>
                <span class="keyvals__val text -padT0@sm"><?=!empty($loop['basic_info']['status']) ? $format->htmlspecialchars($loop['basic_info']['status']) : '-'; ?></span>
            </div>
            <div class="keyvals__row keyvals__row--rows@sm">
                <span class="keyvals__key text text--strong -padB0@sm">Completed Tasks</span>
                <span class="keyvals__val text -padT0@sm"><?=number_format($loop['basic_info']['completed_task_count']); ?> / <?=number_format($loop['basic_info']['total_task_count']); ?></span>
            </div>
            <div class="keyvals__row keyvals__row--rows@sm">
                <span class="keyvals__key text text--strong -padB0@sm">Created</span>
                <span class="keyvals__val text -padT0@sm">
                    <?php if (!empty($loop['basic_info']['dotloop_created_timestamp']) && $loop['basic_info']['dotloop_created_timestamp'] !== '0000-00-00 00:00:00') { ?>
                        <time datetime="<?=date('c', strtotime($loop['basic_info']['dotloop_created_timestamp'])); ?>" title="<?=date('l, F jS Y \@ g:ia', strtotime($loop['basic_info']['dotloop_created_timestamp'])); ?>">
                            <?=$format->dateRelative($loop['basic_info']['dotloop_created_timestamp']); ?>
                        </time>
                    <?php } else { ?>
                        -
                    <?php } ?>
                </span>
            </div>
            <div class="keyvals__row keyvals__row--rows@sm">
                <span class="keyvals__key text text--strong -padB0@sm">Updated</span>
                <span class="keyvals__val text -padT0@sm">
                    <?php if (!empty($loop['basic_info']['dotloop_created_timestamp']) && $loop['basic_info']['dotloop_created_timestamp'] !== '0000-00-00 00:00:00') { ?>
                        <time datetime="<?=date('c', strtotime($loop['basic_info']['dotloop_updated_timestamp'])); ?>" title="<?=date('l, F jS Y \@ g:ia', strtotime($loop['basic_info']['dotloop_updated_timestamp'])); ?>">
                            <?=$format->dateRelative($loop['basic_info']['dotloop_updated_timestamp']); ?>
                        </time>
                    <?php } else { ?>
                        -
                    <?php } ?>
                </span>
            </div>
        </div>
    </div>

    <?php if (!empty($loop['details'])) { ?>
        <div id="loopDetails">
            <?php foreach ($loop['details'] as $title => $details) { ?>
                <div class="block block--loopDetails -marB8">
                    <h3 class="panel__hd mar0" style="font-size: 18px;">
                        <?=$format->htmlspecialchars($title); ?>
                        <svg class="icon R" style="width: 38px !important; height: 38px !important; margin-top: -6px;">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-drop"></use>
                        </svg>
                    </h3>
                    <div class="keyvals keyvals--bordered panel__bd">
                        <?php if (!empty($details) && is_array($details)) { ?>
                            <?php foreach ($details as $label => $value) { ?>
                                <div class="keyvals__row keyvals__row--rows@sm">
                                    <span class="keyvals__key text text--strong -padB0@sm"><?=$format->htmlspecialchars($label); ?></span>
                                    <span class="keyvals__val text -padT0@sm"><?=!empty($value) ? $format->htmlspecialchars($value) : '-'; ?></span>
                                </div>
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

    <div class="block -marB">
        <div class="divider -marB">
            <span class="divider__label divider__label--left text text--large">Participants</span>
        </div>
        <?php if (!empty($loop['participants'])) { ?>
            <div id="participantsList">
                <?php foreach ($loop['participants'] as $participant) { ?>
                    <div class="block--participants">
                        <div class="divider divider--participants -marB">
                            <span class="divider__label divider__label--left" style="font-size: 18px;"><?=!empty($participant['full_name']) ? $format->htmlspecialchars($participant['full_name']) : '-'; ?></span>
                            <button class="btn btn--ghost">
                                <svg class="icon icon--expand" style="width: 38px !important; height: 38px !important;">
                                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use>
                                </svg>
                            </button>
                        </div>
                        <div class="keyvals keyvals--bordered -marB">
                            <div class="keyvals__row keyvals__row--rows@sm">
                                <span class="keyvals__key text text--strong -padB0@sm">Email</span>
                                <span class="keyvals__val text -padT0@sm"><?=!empty($participant['email']) ? $format->htmlspecialchars($participant['email']) : '-'; ?></span>
                            </div>
                            <div class="keyvals__row keyvals__row--rows@sm">
                                <span class="keyvals__key text text--strong -padB0@sm">Role</span>
                                <span class="keyvals__val text -padT0@sm"><?=!empty($participant['role']) ? $format->htmlspecialchars($participant['role']) : '-'; ?></span>
                            </div>
                            <div class="keyvals__row keyvals__row--rows@sm">
                                <span class="keyvals__key text text--strong -padB0@sm">Assigned</span>
                                <span class="keyvals__val text -padT0@sm">
                                    <time datetime="<?=date('c', $participant['timestamp_created']); ?>" title="<?=date('l, F jS Y \@ g:ia', $participant['timestamp_created']); ?>">
                                        <?=$format->dateRelative($participant['timestamp_created']); ?>
                                    </time>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="text--center">
                <img src="/backend/img/ghost.png" width="72">
                <p class="text">
                    No participants are attached to this loop
                </p>
            </div>
        <?php } ?>
    </div>

    <div class="block -marB">
        <div class="divider -marB">
            <span class="divider__label divider__label--left text text--large">Activity</span>
        </div>
        <?php if (!empty($loop['activities'])) { ?>
            <div class="timeline">
                <div class="timeline__body">
            <?php foreach ($loop['activities'] as $activity) { ?>
                <div class="timeline__event">
                    <time class="date__wrap" datetime="<?=date('c', $activity['date']); ?>" title="<?=date('l, F jS Y \@ g:ia', $activity['date']); ?>">
                        <span class="date"><?=$format->dateRelative($activity['date']); ?></span>
                    </time>
                    <div class="timeline__card">
                        <div class="tail"></div>
                        <div><?=!empty($activity['message']) ? $format->htmlspecialchars($activity['message']) : '-'; ?></div>
                    </div>
                </div>
            <?php } ?>
                </div>
            </div>
        <?php } else { ?>
            <div class="text--center">
                <img src="/backend/img/ghost.png" width="72">
                <p class="text">
                    No activity history is available for this loop
                </p>
            </div>
        <?php } ?>
    </div>
</div>
