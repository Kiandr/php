<?php

// Render lead summary header
if ($type == 'leads' && !$multiple) {
    echo $this->view->render('inc/tpl/partials/lead/summary.tpl.php', [
        'title' => __('Email Lead'),
        'lead' => $lead,
        'popup' => isset($_GET['popup']),
        'leadAuth' => $leadAuth
    ]);

// Render agent summary header
} else if ($type == 'agents' && !$multiple) {
    echo $this->view->render('inc/tpl/partials/agent/summary.tpl.php', [
        'title' => __('Email Agent'),
        'agent' => $agent,
        'popup' => isset($_GET['popup']),
        'agentAuth' => $agentAuth
    ]);

} else if ($type == 'associates') {

    // Render agent summary header (menu/title/preview)
    echo $this->view->render('inc/tpl/partials/associate/summary.tpl.php', [
        'title' => __('Email Associate'),
        'associate' => $associate,
        'popup' => isset($_GET['popup']),
        'associateAuth' => $associateAuth
    ]);
} else {
?>

    <div class="bar">
        <div class="bar__title"><?=$multiple
            ? __('Emailing %s', Format::number($recipientCount) . ($type == 'leads' ? __(" Opted-In") : '') . ' ' . ucfirst(strtolower($type)))
            : __('Send Email to %s', Format::htmlspecialchars($recipient['first_name'] . ' ' . $recipient['last_name']));
        ?></div>
        <span class="bar__actions">
            <a class="bar__action timeline__back" href="/backend/" onclick="history.go(-1); return false;"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
        </span>
    </div>

<?php
}
?>

<div class="block">
    <?php if ($restrictRecipients) { ?>
        <div>
            <?= __('Restricting to the first %s %s', htmlspecialchars($restrictRecipients), htmlspecialchars($type)); ?>
            <?php if ($urlSearch) { ?>
                <?= __('Please visit the %s page to refine.', '<a href="<?= htmlspecialchars($urlSearch); ?>">' .__('search') . '</a>'); ?>
            <?php } ?>
        </div>
    <?php } ?>

    <?php if ($leadWarning) {?>
        <div class="warning hint"><?=htmlspecialchars($leadWarning); ?></div>
    <?php }?>

    <form action="?submit&type=<?=htmlspecialchars($type); ?>" method="post" enctype="multipart/form-data" class="rew_check">

        <?php if (!empty($id)) { ?>
            <input type="hidden" name="id" value="<?=$recipient['id']; ?>">
            <input type="hidden" name="type" value="<?=$type; ?>">
        <?php } ?>
        <?php if (isset($_GET['email_search'])) { ?>
        	<input type="hidden" name="email_search" value="<?=Format::htmlspecialchars($_GET['email_search']); ?>">
        <?php } ?>
        <?php if (!empty($_GET['post_task'])) { ?>
        <input type="hidden" name="post_task" value="<?=Format::htmlspecialchars($_GET['post_task']); ?>">
        <?php } ?>
        <input type="hidden" name="timeline_id" value="">
        <input type="hidden" name="redirect" value="<?=$redirect; ?>">
        <input type="hidden" name="recipientCount" value="<?=$recipientCount; ?>">

        <?php if (empty($id)) {?>
            <?php if(in_array($type, SELECTABLE_MODES)) {?>
                <div class="field">
                    <label class="field__label"><?=ucfirst(strtolower($type)); ?></label>
                    <select class="w1/1" name="<?=$type; ?>[]" data-selectize multiple>
                        <?php foreach ($persons as $person) { ?>
                            <option value="<?=$person['id'];?>"<?=(is_array($recipient_ids) && in_array($person['id'], $recipient_ids)) ? ' selected' : ''; ?>><?=$person['first_name'].' '.$person['last_name'];?></option>
                        <?php } ?>
                    </select>
                </div>
            <?php } else { ?>
                <?php foreach ($recipient_ids ?: [] as $recipient_id) { ?>
                    <input type="hidden" name="<?= htmlspecialchars($type); ?>[]" value="<?= htmlspecialchars($recipient_id); ?>">
                <?php } ?>
            <?php } ?>
        <?php } ?>

        <div class="btns btns--stickyB"> <span class="R">
            <button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Send'); ?></button>
            </span>
        </div>

        <div class="field">
            <label class="field__label"><?= __('Subject'); ?> <em class="required">*</em></label>
            <input class="w1/1" name="email_subject" value="<?=Format::htmlspecialchars($_POST['email_subject']); ?>" required>
        </div>
        <div id="emailCC" class="field <?=empty($_POST['cc_email']) ? ' hidden' : ''; ?>">
            <label class="field__label"><?= __('CC'); ?></label>
            <input class="w1/1" type="email" name="cc_email" value="<?=Format::htmlspecialchars($_POST['cc_email']); ?>">
        </div>
        <div id="emailBCC" class="field <?=empty($_POST['bcc_email']) ? ' hidden' : ''; ?>">
            <label class="field__label"><?= __('BCC'); ?></label>
            <input class="w1/1" type="email" name="bcc_email" value="<?=Format::htmlspecialchars($_POST['bcc_email']); ?>">
        </div>
        <p class="form_display_control">
            <?php if (empty($_POST['cc_email'])) echo '<a id="addCC" class="-marR8" href="javascript:void(0);">' . __('Add CC') . '</a>'; ?>
            <?php if (empty($_POST['bcc_email'])) echo '<a id="addBCC" href="javascript:void(0);">' . __('Add BCC') . '</a>'; ?>
        </p>

        <div class="field">
            <label class="field__label"><?= __('Message'); ?> <em class="required">*</em></label>
            <?php $tinymce = ($_POST['is_html'] != 'false' || !empty($_POST['tmp_id']) ? '' : ' off'); ?>
            <textarea class="tinymce w1/1 email<?=$tinymce; ?>"id="email_message" name="email_message" rows="20" cols="85"><?=Format::htmlspecialchars($_POST['email_message']); ?></textarea>
            <label class="hint"><?= __('Tags:'); ?> {first_name}, {last_name}, {email}
                <?=($authuser->isAgent() || $authuser->isAssociate() ? ', {signature}' : ''); ?>
            </label>
        </div>
        <div class="divider">
          <h3 class="divider__label divider__label--left text"><?= __('Settings'); ?></h3>
        </div>
        <div class="field">
            <?php $disabled = (!empty($_POST['tmp_id']) ? ' disabled' : ''); ?>
            <div class="toggle">
                <input<?=$disabled; ?> id="is_html_true" type="radio" name="is_html" value="true"<?=($_POST['is_html'] != 'false' ? ' checked' : ''); ?>>
                <label class="toggle__label" for="is_html_true"><?= __('HTML Email'); ?></label>
                <input<?=$disabled; ?> id="is_html_false" type="radio" name="is_html" value="false"<?=($_POST['is_html'] == 'false' ? ' checked' : ''); ?>>
                <label class="toggle__label" for="is_html_false"><?= __('Plain Text'); ?></label>
            </div>
        </div>
        <?php if ($authuser->isAgent()) { ?>
        <div class="cols">
        <div class="field col w1/2">
            <label class="field__label"><?= __('Template (optional)'); ?></label>
            <select class="w1/1" name="tmp_id">
                <option value=""><?= __('No Template'); ?></option>
                <?php

                    // Templates
                    foreach ($templates as $template) {
                        echo '<option value="' . $template['id'] . '"' . ($_POST['tmp_id'] == $template['id'] ? ' selected' : '') . '>' . Format::htmlspecialchars($template['name']) . '</option>';
                    }

                ?>
            </select>
        </div>
        <div class="field col w1/2">
            <label class="field__label"><?= __('Pre Built Message (optional)'); ?></label>
            <select class="w1/1" name="doc_id">
                <option value=""><?= __('Select a message'); ?></option>
                <?php

                    // Documents
                    foreach ($docs as $cat_id => $cat) {
                        echo '<optgroup label="' . Format::htmlspecialchars($cat['name']) . '">';
                        if (!empty($cat['docs'])) {
                            foreach ($cat['docs'] as $doc_id => $doc_name) {
                                if (!empty($doc_id)) {
                                    echo '<option value="' . $doc_id . '"' . ($_POST['doc_id'] == $doc_id ? ' selected' : '') . '>' . Format::htmlspecialchars($doc_name) . '</option>';
                                }
                            }
                        }
                        echo '</optgroup>';
                    }

                ?>
            </select>
        </div>
        </div>
        <?php } ?>
        <div class="field">
            <label class="toggle">
                <input type="checkbox" name="delay" value="Y"<?=($_POST['delay'] == 'Y' ? ' checked' : ''); ?>>
                <span class="toggle__label"><?= __('Delay this Email'); ?></span>
            </label>
        </div>
        <div id="email_delay"<?=($_POST['delay'] != 'Y') ? ' class="cols hidden"' : ''; ?>>
            <div class="field col w1/2">
                <label class="field__label"><?= __('Send Date'); ?></label>
                <input id="datePicker" class="w1/1" readonly name="send_date" value="<?=date('l, F j, Y', strtotime($_POST['send_date'])); ?>">
            </div>
            <div class="field col w1/2">
                <label class="field__label"><?= __('Send Time'); ?></label>
                <input id="timePicker" class="w1/1" readonly name="send_time" value="<?=Format::htmlspecialchars($_POST['send_time']); ?>">
            </div>
        </div>
        <div class="divider">
          <h3 class="divider__label divider__label--left text"><?= __('Attachments'); ?></h3>
        </div>
        <div id="email-attachments" class="field">
            <label class="field__label"><?= __('Email Attachments (3 Max.)'); ?></label>
            <input class="w1/1" type="file" name="attachments[]" value="">
            <p class="hint" title="<?= __('Allowed File Types'); ?>">
                <?=implode(', ', $allow); ?>
            </p>
        </div>
        <div class="field">
            <label class="field__label"><?= __('MLS&reg; Listing'); ?></label>
            <div class="input w1/1">
                <?php if (!empty(Settings::getInstance()->IDX_FEEDS)) { ?>
                <select name="feed" required>
                    <option value="">- <?= __('IDX Feed'); ?> -</option>
                    <?php foreach (Settings::getInstance()->IDX_FEEDS as $feed => $settings) { ?>
                    <?php $selected = Settings::getInstance()->IDX_FEED === $feed ? ' selected' : ''; ?>
                    <option value="<?=$feed; ?>"<?=$selected; ?>>
                    <?=Format::htmlspecialchars($settings['title']); ?>
                    </option>
                    <?php } ?>
                </select>
                <?php } ?>
                <input class="autocomplete listing" name="mls_number" value="" placeholder="<?= __('Enter MLS&reg; Number or Street Address'); ?>">
                <button id="insert-listing" class="btn" type="button"><?= __('Insert'); ?></button>
            </div>
        </div>
    </form>
</div>