<section class="colset">

    <div class="block">

	<div class="tabs marB">
		<ul>
			<?php foreach ($status_tabs as $tab) { ?>
				<li<?=($_GET['status'] == $tab ? ' class="current"' : ''); ?>>
					<a href="?<?=(!empty($user_id) ? 'id=' . $user_id . '&' : ''); ?>status=<?=$tab; ?>"><?=$tab; ?></a>
				</li>
			<?php } ?>
		</ul>
	</div>

    </div>

</section>

<section id="task-list" class="nodes">

		<?php
        if(empty($tasks_timeline)){
            echo '<div class="block text text--mute text--center"><span>' . __('No %s tasks', strtolower($_GET['status'])) . '</span></div>';
        }

		if (!empty($tasks_timeline)) {
			foreach ($tasks_timeline as $date => $tasks) {

				$divider_class = 'bg-blue';

				if ($date == 'due_now') {
					$divider = __('Due Now');
					$divider_class = 'bg-red';
				} else if (date('d-m-Y') == $date) {
					$divider = __('Today');
					$divider_class = 'bg-blue';
				} else if (date('d-m-Y', strtotime('+1 day')) == $date) {
					$divider = __('Tomorrow');
				} else {
					$divider = date('l, F jS Y', strtotime($date));
				}
        ?>

	<ul class="nodes__list" style="margin: 0;">
		<li class="nodes__branch">
		    <div class="nodes__wrap nodes__wrap--no-divider">
                <div class="article">
                    <div class="article__body">
                        <div class="article__content">
                            <div class="divider">
                                <span class="divider__label divider__label--left text text--small text--mute">
                            	<?=$divider;?>
                                </span>
                            </div>

                        	<ul class="nodes__list" style="margin: 0;">

            				    <?php foreach ($tasks as $task) { ?>
                                <li class="nodes__branch">
                                    <div class="nodes__wrap nodes__wrap--no-divider -padR0">
                                    	<div class="article">
                                            <div class="article__body">
                                                <div class="article__content">
                                                    <div class="text text--strong"><?=$task['name']; ?><?=($task['automated'] === 'Y' ? ' (Automated)' : ''); ?></div>
                                                    <div class="text text--mute">
                                                        <div>
                                                            <strong><?= __($task['status'] !== 'Pending' ?  $task['status'] : 'Due'); ?>: </strong>
                                                            <?=date('F jS \a\t g:i A', $task['time']); ?>
                                                            (<small><?=date_to_relative($task['time']); ?></small>)
                                                        </div>
                                                        <div>
                            							<?php if (!empty($task['expire_time'])) { ?>
                                                            <strong><?=__('Expires'); ?>: </strong>
                                                            <?=date('F jS \a\t g:i A', $task['expire_time']); ?>
                                                            (<small> <?=date_to_relative(strtotime($task['timestamp_expire'])); ?></small>)
                                                        <?php } ?>
                                                        </div>
                                                    </div>
                                                    <div class="text text--mute">
                                                        <?php if ($mode == 'Lead') { ?>
                                                            Assigned <?=$task['performer']; ?>: <a href="<?=URL_BACKEND; ?>agents/agent/summary/?id=<?=$task['agent_id']; ?>">
                                                            <div class="thumb-text-center token thumb thumb--tiny -bg-<?=strtolower(substr($task['performer_initials'],0,1)); ?>"><span class="thumb__label"><?=$task['performer_initials']; ?></span></div>
                                                            <?=$task['performer_name']; ?>
                                                            </a>
                                                        <?php } ?>
                                                        <?php if ($mode != 'Lead') { ?>
                                                            <?=$mode; ?> for <a href="<?=URL_BACKEND; ?>leads/lead/summary/?id=<?=$task['user_id']; ?>">
                                                            <div class="thumb-text-center token thumb thumb--tiny -bg-<?=strtolower(substr($task['user_initials'],0,1)); ?>"><span class="thumb__label"><?=$task['user_initials']; ?></span></div>
                                                            <?=$task['user_name']; ?>
                                                            </a>
                                                        <?php } ?>
                                                    </div>

                            						<div class="marT task-content task-<?=$task['id'] . '-' . $task['user_id']; ?> hidden">
                            								<div class="-marB"><b><?= __('Action Plan:'); ?></b><div class="task-info"><?=(!empty($task['plan_name']) ? $task['plan_name'] : 'Unknown'); ?></div></div>
                            								<?php if (!empty($task['info'])) { ?>
                            									<div class="-marB"><b><?= __('Instructions:'); ?></b><div class="task-info"><?=$task['info']; ?></div></div>
                            								<?php } ?>
                            								<?php if (!empty($task['extras'])) { ?>
                            									<div class="-marB"><b><?=$task['extras']['title']; ?></b><div class="task-info"><?=$task['extras']['content']; ?></div></div>
                            								<?php } ?>
                            								<?php if (!empty($task['notes']) && is_array($task['notes'])) { ?>
                            									<div class="-marB"><b><?= __('Notes:'); ?></b><div class="task-notes"><p><?=implode('</p><p>', $task['notes']); ?></p></div></div>
                            								<?php } ?>
                            								<?php if (!empty($task['can_edit'])) { ?>
                            									<ul class="horizonal-nav actions" style="margin: 0;" data-task='{"id":"<?=$task['id']; ?>","user":"<?=$task['user_id']; ?>","name":"<?=$task['name']; ?>"}'>
                            										<?php if ($task['status'] == 'Pending') { ?>
                                                                        <?php if (!empty($task['shortcut'])) { ?>
                                                                            <li class="-marB8 -marR8"><?=$task['shortcut']; ?></li>
                                                                        <?php } ?>
                                                                        <li class="-marB8 -marR8"><button class="btn task-action btn--positive" data-action="complete">Mark as Complete</button></li>
                                                                        <li class="-marB8 -marR8"><button class="btn task-action" data-action="snooze"><?= __('Snooze'); ?></button></li>
                                                                        <li class="-marB8 -marR8"><button class="btn task-action" data-action="dismiss"><?= __('Dismiss'); ?></button></li>
                            										<?php } ?>
                            										<li class="-marB8 -marR8"><button class="btn task-action" data-action="note"><?= __('Add Note'); ?></button></li>
                            									</ul>
                            								<?php } else { ?>
                            									<i><?= __('Task assigned to %s', $task['performer']); ?></i>
                            								<?php } ?>
                            						</div>

                                                </div>
                                            </div>
                                    	</div>
                                    	<div class="nodes__actions">
                                        	<a href="#" class="btn btn--ghost btn--ico expand-task" data-task="<?=$task['id'] . '-' . $task['user_id']; ?>"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-cog" /></svg></a>
                                    	</div>
                                    </div>
                                </li>
                                <?php } ?>

                        	</ul>

                        </div>
                    </div>
                </div>
            </div>
		</li>
	</ul>
	<?php } ?>


                        <!--
								<i class="<?=$task['icon_class']; ?> icon-task"></i>
								<?php if ($mode == 'Admin') { ?>
								<div class="performer-flag"><?=$task['performer_info']; ?></div>
								<?php } ?>
								<div class="automated-flag"><?=($task['automated'] == 'Y') ? 'Automated' : ''; ?></div>
						-->

				<?php


			// Pagination
			if (!empty($pagination['links'])) {
				echo '<div class="rewui nav_pagination">';
				if (!empty($pagination['prev'])) echo '<a href="' . $pagination['prev']['url'] . '" class="prev">&lt;&lt;</a>';
				if (!empty($pagination['links'])) {
					foreach ($pagination['links'] as $link) {
						echo '<a href="' . $link['url'] . '"' . (!empty($link['active']) ? ' class="current"' : '') . '>' . $link['link'] . '</a>';
					}
				}
				if (!empty($pagination['next'])) echo '<a href="' . $pagination['next']['url'] . '" class="next">&gt;&gt;</a>';
				echo '</div>';
			}
		?>

		<?php } ?>





<!--
				<form method="get" class="rew_check">

					<?php if (!empty($user_id)) { ?>
						<input type="hidden" name="id" value="<?=$user_id; ?>">
					<?php } ?>

					<h3>Filter Tasks</h3>

					<div class="field">
						<label class="field__label">Type</label>
						<select class="w1/1" name="type">
							<option>All</option>
							<?php foreach ($type_options as $option) { ?>
								<option <?=($option == $_GET['type'] ? ' selected' : ''); ?>><?=$option; ?></option>
							<?php } ?>
						</select>
					</div>

					<div class="field">
						<label class="field__label">Plan</label>
						<select class="w1/1" name="plan">
							<option value="">All</option>
							<?php foreach ($plan_options as $value => $title) { ?>
								<option value="<?=$value; ?>"<?=($value == $_GET['plan'] ? ' selected' : ''); ?>><?=$title; ?></option>
							<?php } ?>
						</select>
					</div>

					<div class="field">
						<label class="field__label"><?=($status == 'Pending' ? 'Due' : Format::htmlspecialchars($_GET['status'])); ?></label>
						<select class="w1/1" name="due">
							<?php foreach ($due_options as $value => $title) { ?>
								<option value="<?=$value; ?>"<?=($value == $_GET['due'] ? ' selected' : ''); ?>><?=($status != 'Pending' ? 'Last ' : 'Next '); ?><?=$title; ?></option>
							<?php } ?>
							<option value=""<?=(empty($_GET['due']) ? ' selected' : ''); ?>>Any Time</option>
						</select>
					</div>

					<?php if (in_array($status, array('Pending', 'Completed')) && $can_manage) { ?>
						<div class="field">
							<label class="field__label">Automated Tasks</label>
							<div>
								<input id="automated_true" type="radio" name="show_automated" value="Y"<?=($_GET['show_automated'] == 'Y') ? ' checked' : ''; ?>>
								<label for="automated_true" class="boolean">Show</label>
								<input id="automated_false" type="radio" name="show_automated" value="N"<?=($_GET['show_automated'] != 'Y') ? ' checked' : ''; ?>>
								<label for="automated_false" class="boolean">Hide</label>
							</div>
						</div>
					<?php } ?>

					<div class="btns">
						<button type="submit" value="update" class="btn">Filter</button>
					</div>

				</form>
-->


			<?php // [Morgan Temp Request] Commented Out Mass Action Features ?>
			<?php /*if (empty($_GET['status']) || $_GET['status'] == 'Pending') { ?>

				<div class="field">

					<h2>Mass Task Processing</h2>
					<p class="tip">
						Process and Complete tasks of a specific type group.
					</p>

					<?php if (!empty($count['tasks']['Email'])) { ?>
						<div class="field">
							<a href="#" class="btn neutral process-task-group" data-type="Email" data-ids="<?=implode(',', $count['tasks']['Email']); ?>" title="Process Emails">Email Tasks</a>
							<p class="tip">
								Email tasks that have not been set up with a default subject line and/or body content can not be processed this way.
							</p>
						</div>
					<?php } ?>

					<div class="field">
						<?php if (Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO']) { ?>
							<?php $tip_text = 'Text tasks that have not been set up with default body content can not be processed this way.'; ?>
							<?php if(!empty($count['tasks']['Text'])) { ?>
								<a href="#" class="btn neutral process-task-group" data-type="Text" data-ids="<?=implode(',', $count['tasks']['Text']); ?>" title="Process Texts (REW Text)">Text Tasks ( via REW Text )</a>
							<?php } ?>
						<?php } else { ?>
							<?php $tip_text = 'Purchase our REW Text product to enable this feature. Contact your product consultant for more information.'; ?>
							<button href="#" class="btn neutral" title="Purchase our REW Text product to enable this feature. Contact your product consultant for more information." disabled>Text Tasks ( via REW Text )</button>
						<?php } ?>
						<p class="tip"><?=$tip_text; ?></p>
					</div>

					<?php if ($authuser->isAgent()) { ?>
						<div class="field">
							<?php if (Settings::getInstance()->MODULES['REW_PARTNERS_ESPRESSO'] > 0) { ?>
								<?php if (!empty($count['tasks']['Call'])) { ?>
									<a href="#" class="btn neutral queue-dialer-task-group" data-type="Call" data-ids="<?=implode(',', $count['tasks']['Call']); ?>" title="Process Calls (REW Dialer)">Call Tasks ( via REW Dialer )</a>
								<?php } ?>
							<?php } else { ?>
								<button href="#" class="btn neutral" title="Purchase our REW Dialer product to enable this feature. Contact your product consultant for more information." disabled>Call Tasks ( via REW Dialer )</button>
								<p class="tip">Purchase our <a href="http://www.realestatewebmasters.com/dialer/" target="_blank">REW Dialer</a> product to enable this feature. Contact your product consultant for more information.</p>
							<?php } ?>
						</div>
					<?php } ?>

					<?php if (!empty($count['tasks']['Group'])) { ?>
						<div class="field">
							<a href="#" class="btn neutral process-task-group" data-type="Group" data-ids="<?=implode(',', $count['tasks']['Group']); ?>" title="Process Group Assignments">Group Assignment Tasks</a>
						</div>
					<?php } ?>

				</div>

			<?php }*/ ?>

			<?php if ($mode == 'Lead') { ?>

            <div class="block">

                <div class="divider"><span class="divider__label divider__label--left text text--mute text--small"><?= __('Action Plans'); ?></span></div>

						<?php if (!empty($assigned_plans)) { ?>

                        <div class="nodes">
                            <ul class="nodes__list">
							    <?php foreach ($assigned_plans as $plan) { ?>
								<li class="nodes__branch">
									<div class="nodes__wrap nodes__wrap--no-divider -padR0">
    									<div class="article">
        									<div class="article__body">
            									<div class="article__content">
                                                    <?php if ($can_manage) { ?>
                                                        <a class="text text--strong" href="<?=URL_BACKEND . 'leads/action_plans/edit/?id=' . $plan['id']; ?>"><?=(strlen($plan['name']) > 15 ? substr($plan['name'], 0, 12) . '...' : $plan['name']); ?></a>
                                                    <?php } else { ?>
                                                        <?=(strlen($plan['name']) > 15 ? substr($plan['name'], 0, 12) . '...' : $plan['name']); ?>
                                                    <?php } ?>

                									<div class="text text--mute"><strong>Assigned: </strong><?=date('M j, Y', strtotime($plan['timestamp_assigned'])); ?>, <?=(!empty($plan['timestamp_completed']) ? '<strong>Completed: </strong>'. date('M j, Y', strtotime($plan['timestamp_completed'])) : __('In Progress')); ?></div>
            									</div>
        									</div>
    									</div>
    									<div class="nodes__actions">
								            <?php if ($can_assign) { ?>
											<a onclick="return confirm('<?= __('Are you sure you would like to unassign this plan?'); ?>');" href="?unassign_plan&plan_id=<?=$plan['id']; ?>&id=<?=$user_id; ?>" title="Unassign" class="btn btn--ghost btn--ico">
    											<svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-trash" /></svg>
    								        </a>
                                            <?php } ?>
    									</div>
									</div>
								</li>
                                <?php } ?>

                            </ul>
                        </div>


				<?php } else { ?>
					<?= __('None'); ?>
				<?php } ?>


					<?php if ($can_assign) { ?>

                    <form method="post" action="?assign_plan" class="rew_check">

						<input type="hidden" name="id" value="<?=$user_id; ?>">
						<div class="field">
							<label class="field__label"><?= __('New Plan'); ?></label>
							<span class="input w1/1">
								<select name="plan_id">
									<option value=""><?= __('Select New Action Plan'); ?></option>
									<?php foreach ($plan_options as $value => $title) { ?>
										<?php if (array_key_exists($value, $assigned_plans)) continue; ?>
										<option value="<?=$value; ?>"><?=$title; ?></option>
									<?php } ?>
								</select>
								<button class="btn" type="submit"><?= __('Assign'); ?></button>
							</span>
						</div>

				    </form>

					<?php } ?>

			<?php } ?>

            </div>

<div id="modal-forms" class="hidden">

	<form id="snooze-form" class="task-action-form" method="post">

		<input type="hidden" name="task_action" value="snooze">
		<input type="hidden" name="id" value="<?=$user_id; ?>">

		<input type="hidden" name="task" value="">
		<input type="hidden" name="user" value="">

		<div class="field">
			<label class="field__label"><?= __('Reason/Note'); ?></label>
			<textarea class="w1/1" name="note" rows="3" required></textarea>
		</div>

		<div class="field">
			<label class="field__label"><?= __('Amount'); ?> <em class="required">*</em></label>
			<input class="w1/1" type="number" name="snooze_amount" value="1" min="0">
		</div>

		<div class="field -marB">
			<!-- <label>&nbsp;</label> -->
			<select class="w1/1" name="snooze_unit">
				<option value="1"><?= __('hours'); ?></option>
				<option value="24" selected><?= __('days'); ?></option>
				<option value="168"><?= __('weeks'); ?></option>
			</select>
		</div>

		<button type="submit" class="btn btn--positive -marR8"><?= __('Snooze'); ?></button>
		<button class="btn cancel-button"><?= __('Cancel'); ?></button>

	</form>

	<form id="dismiss-form" class="task-action-form" method="post">

		<input type="hidden" name="task_action" value="dismiss">
		<input type="hidden" name="id" value="<?=$user_id; ?>">

		<input type="hidden" name="task" value="">
		<input type="hidden" name="user" value="">


		<div class="field">
			<label class="field__label"><?= __('Reason/Note'); ?></label>
			<textarea class="w1/1" name="note" rows="3" required></textarea>
		</div>
		<div class="-marB">
			<label class="boolean toggle">
                <input type="checkbox" name="followup_dismiss" value="true">
                <span class="toggle__label"><?= __('Dismiss Follow-Up Tasks'); ?></span>
            </label>
		</div>

		<button type="submit" class="btn btn--positive -marR8"><?= __('Dismiss'); ?></button>
		<button class="btn cancel-button"><?= __('Cancel'); ?></button>

	</form>


	<form id="complete-form" class="task-action-form" method="post">

		<input type="hidden" name="task_action" value="complete">
		<input type="hidden" name="id" value="<?=$user_id; ?>">

		<input type="hidden" name="task" value="">
		<input type="hidden" name="user" value="">

		<div class="field">
            <label class="field__label"><?= __('Task Note'); ?></label>
            <textarea class="w1/1" name="note" rows="3"></textarea>
		</div>

		<button type="submit" class="btn btn--positive -marR8"><?= __('Mark as Complete'); ?></button>
		<button class="btn cancel-button"><?= __('Cancel'); ?></button>

	</form>

	<form id="note-form" class="task-action-form" method="post">

		<input type="hidden" name="task_action" value="note">
		<input type="hidden" name="id" value="<?=$user_id; ?>">

		<input type="hidden" name="task" value="">
		<input type="hidden" name="user" value="">


			<div class="field">
				<label class="field__label"><?= __('Note'); ?></label>
				<textarea class="w1/1" name="note" rows="3"></textarea>
			</div>


			<button type="submit" class="btn btn--positive"><?= __('Add Note'); ?></button>
			<button class="btn cancel-button"><?= __('Cancel'); ?></button>


	</form>

	<form id="process-form" class="task-action-form">

		<input type="hidden" name="lead" value="">

		<?php /* Populated in module.js.php */ ?>
		<div class="form-contents"></div>

		<button type="submit" class="btn btn--positive process-button -marB8 -marR8" data-action="submit"><?= __('Process + Mark as Complete'); ?></button>
		<button class="btn cancel-button -marB8" data-action="cancel"><?= __('Cancel'); ?></button>

	</form>

</div>

</section>
