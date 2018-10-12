<?php

// Render lead summary header (menu/title/preview)
echo $this->view->render('inc/tpl/partials/lead/summary.tpl.php', [
    'title' => 'Lead Reminders',
    'lead' => $lead,
    'leadAuth' => $leadAuth
]);

?>
<?php if (!empty($reminders)) { ?>
<div class="nodes">
	<ul class="nodes__list">
	<?php foreach ($reminders as $reminder) { ?>
		<li class="nodes__branch" <?=($reminder['completed'] != 'true' && $reminder['timestamp'] < time()) ? ' class="flag_overdue"' : ''; ?>>
		    <div class="nodes__wrap">
    			<div class="article">
    			    <div class="article__body">
                        <div class="article__content">
        					<div class="text text--strong"><?=Format::htmlspecialchars($reminder['type']); ?></div>

        					<?=Format::htmlspecialchars($reminder['details']); ?>

        					<?php

        					// Added by Agent
        					if (!empty($reminder['agent'])) {

								if (!is_array($reminder['agent'])) {

        							$reminder['agent'] = array (
        								'id'                => $reminder['agent'],
        								'name'              => "an agent that has been deleted.",
        								'permissions_admin' => "N/A",
        								'deleted_agent'     => "true"
        							);
        						}

        						// Added by Self
        						if ($authuser->isAgent() && $authuser->info('id') == $reminder['agent']['id']) {

        							echo '<div class="text text--mute">Note Added by <strong>You</strong></div>';

        						// Allowed to View Agent
        						} else if (!isset($reminder['agent']['deleted_agent']) && $agentAuth->canViewAgents($authuser)) {
        							echo '<div class="text text--mute">Note Added by <a href="' . URL_BACKEND . 'agents/agent/summary/?id=' . $reminder['agent']['id'] . '">' . Format::htmlspecialchars($reminder['agent']['name']) . '</a></div>';

        						// Show Agent Name
        						} else {
        							echo '<div class="text text--mute">Note Added by <strong>' . Format::htmlspecialchars($reminder['agent']['name']) . '</strong></div>';
        						}

        					// Added by ISA
        					} else if (!empty($reminder['associate'])) {

        						// Added by Self
        						if ($authuser->isAssociate() && $authuser->info('id') == $reminder['associate']['id']) {
        							echo '<div class="text text--mute">Note Added by <strong>You</strong></div>';

        						// Allowed to View Associate
        						} else if ($assocAuth->canViewAssociates($authuser)) {
        							echo '<div class="text text--mute">Note Added by <a href="' . URL_BACKEND . 'associates/associate/summary/?id=' . $reminder['associate']['id'] . '">' . Format::htmlspecialchars($reminder['associate']['name']) . '</a></div>';

        						// Show Associate Name
        						} else {
        							echo '<div class="text text--mute">Note Added by <strong>' . Format::htmlspecialchars($reminder['associate']['name']) . '</strong></div>';

        						}

        					}

        					// Shared Note
        					echo ($reminder['share'] == 'true') ? ' <i>(Shared)</i>' : '';

        					?>

        					<div class="text text--mute"><?=date('D, M. j, Y g:ia', $reminder['timestamp']); ?></div>
        					<p>
        					<?php if ($reminder['completed'] == 'true') { ?>
        					<a href="?id=<?=$lead['id']; ?>&toggle=<?=$reminder['id']; ?>" class="btn btn--positive check" title="Click to Mark as Incomplete">Completed</a>
        					<?php } else { ?>
        					<a href="?id=<?=$lead['id']; ?>&toggle=<?=$reminder['id']; ?>" class="btn btn--negative alert" title="Click to Mark as Complete">Incomplete</a>
        					<?php } ?>
        					</p>

                        </div>
        					<div class="nodes__actions">
        						<?php if (!empty($reminder['can_edit'])) { ?>
        						<a class="btn btn--ghost edit" href="?id=<?=$lead['id']; ?>&edit=<?=$reminder['id']; ?>">Edit</a>
        						<?php } ?>
        						<?php if (!empty($reminder['can_delete'])) { ?>
        						<a class="btn btn--ico btn--ghost" href="?id=<?=$lead['id']; ?>&delete=<?=$reminder['id']; ?>" onclick="return confirm('Are you sure you want to delete this reminder?');">
        							<svg class="icon icon-trash mar0"><use xlink:href="/backend/img/icos.svg#icon-trash"/></svg>
        						</a>
        						<?php } ?>
        					</div>
                    </div>
				</div>
			</li>
		<?php } ?>
	</ul>
</div>
<?php } else { ?>
<div class="block">
<p id="reminder-message">
	<p>There are currently no reminders set for this lead.
</p>
</div>
<?php } ?>
<?php if (!empty($edit)) { ?>
<div class="block">
<form action="?submit " method="post" class="rew_check">
	<div class="block block--bg">
		<input type="hidden" name="id" value="<?=$lead['id']; ?>">
		<input type="hidden" name="edit" value="<?=$edit['id']; ?>">
		<h3>Edit Reminder</h3>
		<div class="field">
			<span class="input">
				<select id="select-reminder-types" name="type">
					<?php foreach ($types as $type) { ?>
					<option value="<?=$type['value']; ?>"<?=($edit['type'] == $type['value']) ? ' selected' : ''; ?>>
					<?=Format::htmlspecialchars($type['title']); ?>
					</option>
					<?php } ?>
				</select>
				<?php if ($authuser->isSuperAdmin()) { ?>
				<a href="javascript:void(0);" class="action-manage btn" id="manage-reminder-types">Manage</a>
				<?php } ?>
			</span>
		</div>
		<div class="field">
			<input id="reminder_date" class="w1/1" name="date" value="<?=$edit['date']; ?>" placeholder="Reminder Date">
		</div>
		<div class="field">
			<input id="reminder_time" class="w1/1" name="time" value="<?=$edit['time']; ?>" placeholder="Reminder Time">
		</div>
		<div class="field">
			<textarea class="w1/1" name="details" cols="24" rows="4" placeholder="Reminder Details"><?=Format::htmlspecialchars($edit['details']); ?></textarea>
		</div>

		<div class="btns">
			<?php if (!empty($can_share)) { ?>
			<label class="R">
				<input class="complete" type="checkbox" name="share" value="true"<?=($edit['share'] == 'true') ? ' checked' : ''; ?>>
				Share with Agent
			</label>
			<?php } ?>
			<label class="R">
				<input class="complete" type="checkbox" name="completed" value="true"<?=($edit['completed'] == 'true') ? ' checked' : ''; ?>>
				Completed
			</label>
			<button type="submit" class="btn">Save Changes</button>
			<a class="btn" href="?id=<?=$lead['id']; ?>">Cancel</a>
		</div>
	</div>
</form>
</div>
<?php } else { ?>

<div class="block">
<form action="?submit " method="post" class="rew_check">
	<div class="block block--bg">
		<input type="hidden" name="id" value="<?=$lead['id']; ?>">
		<h3 id="add">Add New Reminder</h3>
		<div class="field">
			<span class="input input--manage">
				<select name="type">
					<?php foreach ($types as $type) { ?>
					<option value="<?=$type['value']; ?>"<?=($_POST['type'] == $type['value']) ? ' selected' : ''; ?>>
					<?=Format::htmlspecialchars($type['title']); ?>
					</option>
					<?php } ?>
				</select>
				<?php if ($authuser->isSuperAdmin()) { ?>
				<a href="javascript:void(0);" class="action-manage btn" id="manage-reminder-types">Manage</a>
				<?php } ?>
			</span>
		</div>
		<div class="field">
			<input id="reminder_date" class="w1/1" name="date" value="<?=Format::htmlspecialchars($_POST['date']); ?>" placeholder="Reminder Date">
		</div>
		<div class="field">
			<input id="reminder_time" class="w1/1" name="time" value="<?=Format::htmlspecialchars($_POST['time']); ?>" placeholder="Reminder Time">
		</div>
		<div class="field">
			<textarea class="w1/1" name="details" cols="24" rows="4" placeholder="Reminder Details"><?=Format::htmlspecialchars($_POST['details']); ?></textarea>
		</div>

		<div class="btns">
			<?php if (!empty($can_share)) { ?>
			<label class="R">
				<input class="complete" type="checkbox" name="share" value="true"<?=($_POST['share'] == 'true') ? ' checked' : ''; ?>>
				Share with Agent
			</label>
			<?php } ?>

			<button type="submit" class="btn btn--positive">Add Reminder</button>
		</div>
	</div>
</form>
</div>

<?php } ?>