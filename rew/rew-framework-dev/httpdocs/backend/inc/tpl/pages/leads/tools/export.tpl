		<div class="bar">
            <div class="bar__title">Export Leads (<span id="leads-count"><?=Format::number($count); ?></span>)</div>
			<div class="bar__actions">
				<a class="bar__action" href="/backend/leads/tools/"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
			</div>
		</div>

<div class="block">

	<form action="?submit" method="post" id="export-leads">

		<?php
		// Leads to Export
		if (!empty($leads) && is_array($leads)) {
			foreach ($leads as $lead) {
				echo '<input type="hidden" name="leads[]" value="' . Format::htmlspecialchars($lead) . '">' . PHP_EOL;
			}
		}
		?>

		<div class="btns btns--stickyB">
			<span class="R">
				<button type="submit" class="btn btn--positive">Download</button>
			</span>
		</div>

    <?php if ($leadsAuth->canExportLeads($authuser) && !empty($agents)) { ?>


    <div class="field">
        <label class="field__label">Leads Assigned to...</label>
        <div class="input w1/1">
            <select id="export-agent" name="agent">
                <option value="" selected>-- Export All --</option>
                <?php foreach ($agents as $agent) { ?>
                    <option value="<?=$agent['id']; ?>">
                        <?=htmlspecialchars($agent['first_name'].' '.$agent['last_name']); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>
    <?php } ?>

    <br>

    <div class="divider">
        <span class="divider__label divider__label--left text text--mute">Data</span>
        <span class="divider__label divider__label--right"><a href="#all">Reset</a> <a href="#none">Clear</a></span>
    </div>

    <div class="field">
		<div class="input w1/1">
			<select id="export-columns">
				<option value="" selected>-- Select Data --</option>
				<?php foreach ($columns as $group => $fields) { ?>
					<optgroup label="<?=$group; ?>">
						<?php foreach ($fields as $column => $field) { ?>
							<option value="<?=$column; ?>"<?=is_array($export) && in_array($column, $export) ? ' disabled' : ''; ?>><?=$field['title']; ?></option>
						<?php } ?>
					</optgroup>
				<?php } ?>
			</select>
			<button type="button" class="btn add">Add</button>
		</div>
    </div>



		<div id="export-data">
			<?php foreach ($panels as $id => $panel) { ?>
				<dl class="panel open<?=is_array($export) && in_array($id, $export) ? '' : ' hidden'; ?>">
					<dt class="trigger">
						<span class="ttl"><?=$panel['title']; ?></span>
						<span class="btns R"><a class="btn btn--ghost delete" href="javascript:void(0);" title="Click to Remove"><svg class="icon icon-trash mar0"><use xlink:href="/backend/img/icos.svg#icon-trash"/></svg></a></span>
						<input type="hidden" name="export[]" value="<?=$id; ?>"<?=is_array($export) && in_array($id, $export) ? '' : ' disabled'; ?>>
					</dt>
				</dl>
			<?php } ?>
		</div>


		<h3>Export Details</h3>

		<div class="field">
			<label class="field__label">File Name</label>
			<div class="input w1/1">
				<input name="filename" value="<?=$filename; ?>">
				<span readonly>.csv</span>
			</div>
		</div>

	</form>

</div>