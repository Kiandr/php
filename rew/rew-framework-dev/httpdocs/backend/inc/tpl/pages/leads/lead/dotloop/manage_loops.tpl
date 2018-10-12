<?php

/**
 * @see REW\Backend\Controller\Leads\Lead\DotloopController
 *
 * @var REW\Core\Interfaces\AuthInterface $authuser
 * @var REW\Core\Interfaces\FormatInterface $format
 * @var \Backend_Lead $lead
 * @var REW\Backend\Auth\Leads\LeadAuth $leadAuth
 * @var REW\Backend\View\Interfaces\FactoryInterface $view
 * @var array $loop_deletion_statuses
 * @var array $loop_participant_types
 * @var array $loop_statuses
 * @var array $loop_transaction_types
 * @var array $profiles
 * @var array $profile_loops
 * @var array $profile_templates
 * @var array $assigned_loops
 * @var array $rate_limit_info
 */

// Render lead summary header (menu/title/preview)
echo $view->render('inc/tpl/partials/lead/summary.tpl.php', [
    'title' => 'DotLoop - Loop Manager',
    'lead' => $lead,
    'leadAuth' => $leadAuth
]);
?>

<div class="block">
    <div class="dotloop__loops">
        <?php if (!empty($assigned_loops)) { ?>
            <?php foreach ($assigned_loops as $loop_group => $loop_group_loops) { ?>
                <div class="-marB">
                    <label class="loop__group__title panel__hd"><?=$format->htmlspecialchars($loop_group); ?></label>
                    <div class="profile__section test">
                        <?php foreach ($loop_group_loops as $loop) { ?>
                            <div class="loop__wrap">
                                <?php if (!empty($loop['id'])) { ?>
                                    <div class="loop" id="loop-<?=$format->htmlspecialchars($loop['id']); ?>">
                                        <a href="<?=sprintf('%sleads/lead/dotloop/loop-%s/?id=%s', URL_BACKEND, $loop['id'], $lead->getId()); ?>">
                                            <div class="loop__name" style="font-weight: bold;"><?=$format->htmlspecialchars($loop['name']); ?></div>
                                            <div class="loop__participant_type">[ <?=$format->htmlspecialchars($loop['participant_role']); ?> ]</div>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>This lead is currently not attached to any loops.</p>
        <?php } ?>
    </div>
</div>

<div class="block">
    <div class="divider -marB">
        <span class="divider__label divider__label--left">Assign To:</span>
    </div>
</div>
<?php if (!empty($rate_limit_info) && $rate_limit_info['remaining'] <= 0) { ?>
    <div class="block">
        <p class="text text--negative">
            DotLoop account's API Rate Rimit has been exceeded. Please reload this page<span id="dotloop-rate-timer" data-remaining="<?=ceil($rate_limit_info['reset_countdown']/1000); ?>"> in <?=ceil($rate_limit_info['reset_countdown']/1000); ?> seconds</span> to assign this lead to new loops.
        </p>
    </div>
<?php } else { ?>
    <div class="block">
        <div class="grid">
            <button id="display-new-loop-form" class="grid__col btn btn--strong -w1/2" title="Assign to a New Loop" style="padding-left: 0;">
                <svg class="icon icon-add mar0">
                    <use xlink:href="/backend/img/icos.svg#icon-add" xmlns:xlink="http://www.w3.org/1999/xlink"></use>
                </svg>
                New Loop
            </button>
            <button id="display-existing-loop-form" class="grid__col btn btn--strong -w1/2" title="Assign to an Existing Loop">
                Existing Loop
            </button>
        </div>
    </div>
    <div>
        <form method="post" id="new-loop-form" class="rew_check" hidden>
            <input type="hidden" name="loop_connect_type" value="new">
            <div class="block">
                <div class="field">
                    <label class="field__label" for="loop_name">Loop Name <em class="required">*</em></label>
                    <input class="w1/1" type="text" name="loop_name" value="<?=$_POST['loop_name']; ?>" required>
                </div>
                <div class="cols">
                    <div class="field col w1/2">
                        <label class="field__label" for="loop_transaction_type">Transaction Type <em class="required">*</em></label>
                        <select class="w1/1" name="loop_transaction_type" required>
                            <option value=""></option>
                            <?php foreach ($loop_transaction_types as $type) { ?>
                                <option value="<?=$type; ?>" <?=($type == $_POST['loop_transaction_type']) ? 'selected' : ''; ?>>
                                    <?=str_replace('_', ' ', $type); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="field col w1/2">
                        <label class="field__label" for="loop_status">Status <em class="required">*</em></label>
                        <select class="w1/1" name="loop_status" required>
                            <?php foreach ($loop_statuses as $type => $statuses) { ?>
                                <?php foreach ($statuses as $status) { ?>
                                    <option data-transaction-type="<?=$type; ?>" value="<?=$status; ?>" disabled hidden <?=($status == $_POST['loop_status']) ? 'selected' : ''; ?>>
                                        <?=str_replace('_', ' ', $status); ?>
                                    </option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="field">
                    <label class="field__label" for="contact_type"><b><?=$lead->info('first_name'); ?>'s role</b> in this loop is&hellip; <em class="required">*</em></label>
                    <select class="w1/1" name="contact_type" required>
                        <option value=""></option>
                        <?php foreach ($loop_participant_types as $type) { ?>
                            <option value="<?=$type; ?>" <?=($type == $_POST['contact_type']) ? 'selected' : ''; ?>>
                                <?=ucwords(strtolower(str_replace('_', ' ', $type))); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <?php if (!empty($profiles)) { ?>
                    <div class="field">
                        <label class="field__label" for="profile_id">Target Profile <em class="required">*</em></label>
                        <select class="w1/1" name="profile_id" required data-selectize>
                            <option value=""></option>
                            <?php foreach ($profiles as $profile) { ?>
                                <option value="<?=$format->htmlspecialchars($profile['id']); ?>">
                                    <?=$format->htmlspecialchars($profile['name']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                <?php } ?>
                <div class="field">
                    <label class="field__label" for="template_id">Loop Template</label>
                    <select class="w1/1" name="template_id" data-selectize>
                        <option value=""></option>
                        <?php if (!empty($profile_templates)) { ?>
                            <?php foreach ($profile_templates as $data) { ?>
                                <?php if (!empty($data['templates'])) { ?>
                                    <?php foreach ($data['templates'] as $template) { ?>
                                        <option data-profile-id="<?=$format->htmlspecialchars($data['id']); ?>" value="<?=$format->htmlspecialchars($template['id']); ?>" disabled hidden <?=($template['id'] == $_POST['template_id']) ? 'selected' : ''; ?>>
                                            <?=$format->htmlspecialchars($template['name']); ?>
                                        </option>
                                    <?php } ?>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    </select>
                </div>
                <div class="field">
                    <div class="btns">
                        <button type="submit" class="btn btn--positive">
                            <svg class="icon icon-check mar0">
                                <use xlink:href="/backend/img/icos.svg#icon-check" xmlns:xlink="http://www.w3.org/1999/xlink"></use>
                            </svg>
                            Create and Assign Loop
                        </button>
                    </div>
                </div>
            </div>
        </form>
        <form method="post" id="existing-loop-form" class="rew_check" hidden>
            <?php if (!empty($profile_loops)) { ?>
                <input type="hidden" name="loop_connect_type" value="existing">
                <div class="block">
                    <div class="field">
                        <label class="field__label" for="profile_loop_ids">Target Loop <em class="required">*</em></label>
                        <select class="w1/1" name="profile_loop_ids" required data-selectize>
                            <option value=""></option>
                            <?php foreach ($profile_loops as $data) { ?>
                                <?php if (!empty($data['loops'])) { ?>
                                    <optgroup label="<?=$format->htmlspecialchars($data['name']); ?>">
                                        <?php foreach ($data['loops'] as $loop) { ?>
                                            <?php if (in_array($loop['status'], $loop_deletion_statuses)) continue; ?>
                                            <?php $value = $format->htmlspecialchars($data['id']) . ':' . $format->htmlspecialchars($loop['id']); ?>
                                            <option value="<?=$value; ?>" <?=($value == $_POST['profile_loop_ids']) ? 'selected' : ''; ?>>
                                                <?=$format->htmlspecialchars($loop['name']); ?>
                                            </option>
                                        <?php } ?>
                                    </optgroup>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="field">
                        <label class="field__label" for="contact_type"><b><?=$lead->info('first_name'); ?>'s role</b> in this loop is&hellip; <em class="required">*</em></label>
                        <select class="w1/1" name="contact_type" required>
                            <option value=""></option>
                            <?php foreach ($loop_participant_types as $type) { ?>
                                <option value="<?=$type; ?>" <?=($type == $_POST['contact_type']) ? 'selected' : ''; ?>>
                                    <?=ucwords(strtolower(str_replace('_', ' ', $type))); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="field">
                        <div class="btns">
                            <button type="submit" class="btn btn--positive">
                                <svg class="icon icon-check mar0">
                                    <use xlink:href="/backend/img/icos.svg#icon-check" xmlns:xlink="http://www.w3.org/1999/xlink"></use>
                                </svg>
                                Assign Loop
                            </button>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="block">
                    <p>There are currently no loops in the specified DotLoop profile.</p>
                </div>
            <?php } ?>
        </form>
    </div>
<?php } ?>