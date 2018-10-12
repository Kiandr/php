<?php

// Render lead summary header (menu/title/preview)
echo $this->view->render('inc/tpl/partials/lead/summary.tpl.php', [
    'title' => 'Lead Notes',
    'lead' => $lead,
    'leadAuth' => $leadAuth
]);

?>

<div class="block">

<?php if (!empty($history)) { ?>
<fieldset id="notes-timeline" class="timeline">
<?php foreach ($history as $date => $notes) { ?>
<div class="kicker">
<h2>
	<?php
		if (date('d-m-Y') == $date) {
			echo 'Today';
		} else if (date('d-m-Y', strtotime('-1 day')) == $date) {
			echo 'Yesterday';
		} else {
			echo date('D, F jS Y', strtotime($date));
		}
	?>
</h2>
</div>

<?php foreach ($notes as $note) { ?>
<div class="note">

	<div class="bd">
		<?=nl2br(Format::htmlspecialchars($note['note'])); ?>
	</div>

	<div class="btns dF">

	<span class="ttl">

	<?php

		// LEGACY SUPPORT: Display Note Type
		echo (!empty($note['type']) ? $note['type']  : $note['type']) . PHP_EOL;

		// Added by Agent
		if (!empty($note['agent'])) {

			// Added by Self
			if ($authuser->isAgent() && $authuser->info('id') == $note['agent']['id']) {
				echo 'You';

			// Allowed to View Agent
			} else if ($agentsAuth->canViewAgents($authuser)) {
				echo '<a href="' . URL_BACKEND . 'agents/agent/summary/?id=' . $note['agent']['id'] . '">' . Format::htmlspecialchars($note['agent']['name']) . '</a>';

			// Show Agent
			} else {
				echo Format::htmlspecialchars($note['agent']['name']);

			}

		// Added by ISA
		} elseif (!empty($note['associate'])) {

			// Added by Self
			if ($authuser->isAssociate() && $authuser->info('id') == $note['associate']['id']) {
				echo 'You';

			// Super Admin can View
			} else if ($associatesAuth->canViewAssociates($authuser)) {
				echo '<a href="' . URL_BACKEND . 'associates/associate/summary/?id=' . $note['associate']['id'] . '">' . Format::htmlspecialchars($note['associate']['name']) . '</a>';

			// Show Associate
			} else {
				echo Format::htmlspecialchars($note['associate']['name']);

			}

		// Added by Lender
		} elseif (!empty($note['lender'])) {

			// Added by Self
			if ($authuser->isLender() && $authuser->info('id') == $note['lender']['id']) {
				echo 'You';

			// Super Admin can View
			} else if ($lendersAuth->canViewLenders($authuser)) {
				echo '<a href="' . URL_BACKEND . 'lenders/lender/summary/?id=' . $note['lender']['id'] . '">' . Format::htmlspecialchars($note['lender']['name']) . '</a>';

			// Show Lender
			} else {
				echo Format::htmlspecialchars($note['lender']['name']);

			}
		}

		// Shared Note
		echo ($note['share'] == 'true') ? ', Shared' : '';

	?>
	</span>
			<?php if (!empty($note['can_delete']) || !empty($note['can_edit'])) { ?>


				<span class="R">
					<?php if (!empty($note['can_edit'])) echo '<a class="btn btn--ghost edit" href="?id=' . $lead['id'] . '&edit=' . $note['id'] . '">Edit</a>'; ?>
					<?php if (!empty($note['can_delete'])) echo '<a class="btn btn--ghost delete" href="?id=' . $lead['id'] . '&delete=' . $note['id'] . '" onclick="return confirm(\'Are you sure you want to remove this note?\');"><svg class="icon icon-trash mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use></svg></a>'; ?>
				</span>
			</div>

		<?php } ?>


</div>
<?php } ?>
<?php } ?>
</fieldset>
<?php } else { ?>
<p class="block">This lead currently has no notes.</p>
<?php } ?>

<section id="note-form">
	<?php if (!empty($edit)) { ?>
	<form action="?submit" method="post">
		<div class="block block--bg">
		<input type="hidden" name="id" value="<?=$lead['id']; ?>">
		<input type="hidden" name="edit" value="<?=$edit['id']; ?>">
		<h3>Edit Lead Note</h3>
		<textarea class="w1/1" name="note" rows="6" cols="85" placeholder="Note Details..." required><?=Format::htmlspecialchars($edit['note']); ?></textarea>

		<div class="btns">
			<?php if (!empty($can_share)) { ?>
			<label class="R pad">
				<input type="checkbox" name="share" value="true"<?=($edit['share'] == 'true') ? ' checked' : ''; ?>>
				Share this Note</label>
			<?php } ?>
			<button class="btn" type="submit">Save</button>
			<a class="btn cancel" href="?id=<?=$lead['id']; ?>">Cancel</a>
		</div>

		</div>
	</form>
	<?php } else { ?>
	<form action="?submit" method="post">
		<div class="block block--bg">
		<input type="hidden" name="id" value="<?=$lead['id']; ?>">
		<h3>Add New Note</h3>
		<textarea class="w1/1" name="note" rows="6" cols="85" placeholder="Note Details..." required></textarea>

		<div class="btns">
			<button class="btn" type="submit">Add Note</button>
			<?php if (!empty($can_share)) { ?>
			<label class="R pad"> <input type="checkbox" name="share" value="true" checked> Share this Note</label>
			<?php } ?>
		</div>

		<?php } ?>
		</div>
	</form>
</section>
</div>