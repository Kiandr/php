<form id="<?=$mode; ?>-task-form" class="task-form">

	<?php if ($mode == 'edit') { ?>
		<input type="hidden" name="task_id" value="<?=$task['id']; ?>">
	<?php } ?>

	<input type="hidden" name="type" value="<?=$task['type']; ?>">
	<input type="hidden" name="actionplan_id" value="<?=$task['actionplan_id']; ?>">
	<input type="hidden" name="parent_id" value="<?=$task['parent_id']; ?>">
	<input type="hidden" name="mode" value="<?=$mode; ?>">
	<div class="modal-title hidden"><?=$form_title; ?></div>

	<div class="colset">


				<div class="field">
					<label class="field__label"><?= __('Task Name'); ?> <em class="required">*</em></label>
					<input class="w1/1" type="text" name="name" value="<?=$task['name']; ?>">
				</div>

				<?php if ($task['type'] == 'Email') { // Task Type specific inputs ?>

				<div class="field">
					<label class="field__label"><?= __('Subject'); ?></label>
					<input class="w1/1" type="text" name="subject" value="<?=$task['subject']; ?>">
					<p class="tip"><?= __('This will be presented as the suggested email subject during the task completion process.'); ?></p>
				</div>

				<div class="field">
					<label class="field__label"><?= __('Pre-Built Message (optional)'); ?></label>
					<select class="w1/1" id="doc_id" name="doc_id" class="prebuilt_message">
						<option value=""><?= __('Select a message'); ?></option>
						<?php
						// Documents
						foreach ($documents as $cat_id => $cat) {
							echo '<optgroup label="' . Format::htmlspecialchars($cat['name']) . '">';
							if (!empty($cat['docs'])) {
								foreach ($cat['docs'] as $doc_id => $doc_name) {
									if (!empty($doc_id)) {
										echo '<option value="' . $doc_id . '"' . ($task['doc_id'] == $doc_id ? ' selected' : '') . '>' . Format::htmlspecialchars($doc_name) . '</option>';
									}
								}
							}
							echo '</optgroup>';
						}
						?>
					</select>
				</div>

				<div class="field">
					<label class="field__label"><?= __('Email Message'); ?> <em class="required">*</em></label>
					<textarea name="body" class="w1/1 body" rows="10" data-target="email_message"><?=$task['body']; ?></textarea>
					<label class="warning"><span class="hint">&nbsp;<?= __('Tags'); ?>: {first_name}, {last_name}, {email}, {signature}, {verify}</span></label>
					<p class="tip"><?= __('This will be presented as the suggested email body during the task completion process.'); ?></p>
				</div>

				<?php } else if ($task['type'] == 'Text') { ?>

				<div class="field">
					<label class="field__label"><?= __('Text Message'); ?></label>
						<textarea class="w1/1" id="taskTextMsg" name="message" rows="4" maxlength="<?=$text_msg_limit; ?>"><?=$task['message']; ?></textarea>
						<?php if (!empty(Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO'])) { ?>
							<label class="warning"><span class="hint">&nbsp;<?= __('Tags'); ?>: {first_name}, {last_name}</span></label>
						<?php } ?>
						<p class="tip"><?= __('This will be presented as the suggested text message body during the task completion process.'); ?></p>
					</div>

				<?php } else if ($task['type'] == 'Group') { ?>

				<div class="field">
					<label class="field__label"><?= __('Group(s)'); ?></label>
					<div class="groups">
                        <select multiple class="w1/1" name="groups[]">
                            <?php foreach ($groups as $group) { ?>
                            <option data-data='{ "style": "<?= $group['style']?>" }' value="<?=$group['id'];?>"<?=((is_array($task['groups']) && in_array($group['id'], $task['groups'])) ? ' selected' : ''); ?>> <?=$group['name']; ?></option>
                            <?php } ?>
                        </select>
					</div>
				</div>
				<?php } // End Task Type specific inputs ?>

				<div class="field">
					<label class="field__label"><?= __('Additional Instructions'); ?></label>
					<textarea class="w1/1 info super simple" name="info"><?=$task['info']; ?></textarea>
					<p class="tip"><?= __('This will display in the task\'s details section for the benefit of the task\'s performer.'); ?></p>
				</div>


					<?php // [Morgan Temp Request] Commented Additional Options ?>
					<input type="hidden" name="performer" value="Agent">
					<?php /* ?>
					<fieldset>
						<label>Performer</label>
						<select name="performer">
							<?php foreach ($performer_options as $option) { ?>
								<option <?=($task['performer'] == $option) ? ' selected' : ''; ?>><?=$option; ?></option>
							<?php } ?>
						</select>
						<p class="tip">The backend user required to complete this task.</p>
					</fieldset>
					<?php */ ?>

					<?php if ($can_automate) { ?>
						<div class="field">
							<label class="field__label"><?= __('Automated'); ?></label>
							<div>
								<input id="automated_true" type="radio" name="automated" value="Y"<?=($task['automated'] == 'Y') ? ' checked' : ''; ?>>
								<label for="automated_true"><?= __('Yes'); ?></label>
								<input id="automated_false" type="radio" name="automated" value="N"<?=($task['automated'] != 'Y') ? ' checked' : ''; ?>>
								<label for="automated_false"><?= __('No'); ?></label>
							</div>
							<p class="tip"><?= __('This task will be automatically completed by the system.'); ?> <?=($task['type'] == 'Email') ? __('Automated %s tasks require a default subject and body.',  Format::htmlspecialchars($task['type'])) : ''; ?></p>
						</div>
					<?php } ?>


					<div class="field">
						<label class="field__label"><?= __('Due Offset (Days)'); ?></label>
						<input type="number" name="offset" value="<?=(!empty($task['offset']) ? $task['offset'] : 0); ?>" min="0">
						<?php if (!empty($task['parent_id'])) { ?>
							<p class="tip"><?= __('The number of days until this task is due. This timer starts once the parent task has been resolved.'); ?></p>
						<?php } else { ?>
							<p class="tip"><?= __('The number of days after the plan was assigned that this task will be due.'); ?></p>
						<?php } ?>
					</div>

					<div class="field">
						<label class="field__label"<?= __('Due Time'); ?></label>
						<input class="timepicker" readonly="readonly" name="time" value="<?=(!empty($task['time']) ? $task['time'] : '12:00 AM'); ?>">
						<p class="tip"><?= __('The time of day this task is due.'); ?></p>
					</div>

					<div class="field">
						<label class="field__label"><?= __('Expire In (Days)'); ?></label>
						<input type="number" name="expire" value="<?=(!empty($task['expire']) ? $task['expire'] : 1); ?>" min="1">
						<p class="tip"><?= __('The maximum number of days this task can be overdue before it expires/fails.'); ?></p>
					</div>

				</fieldset>

			</div>

		<div class="btns">
			<button class="btn btn--positive task-submit" data-action="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"/></svg> <?= __('Save'); ?></button>
			<button class="btn cancel-button"><?= __('Cancel'); ?></button>
		</div>

	</div>

</form>