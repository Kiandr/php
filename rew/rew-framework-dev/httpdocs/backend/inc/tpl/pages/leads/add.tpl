<form action="?submit" method="post" class="rew_check">

	<div class="bar">
		<div class="bar__title">Add New Lead</div>
		<div class="bar__actions">
			<a class="bar__action" href="/backend/leads/"><svg class="icon icon-left-a mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
		</div>
	</div>

    <div class="block">

	<div class="btns btns--stickyB"> <span class="R">
		<button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> Save</button>
		</span>
    </div>
    <div class="cols">
    	<?php if (!empty($can_assign)) { ?>
    	<div class="field col w1/2">
    		<label class="field__label">Assigned Agent <em class="required">*</em></label>
    		<select class="w1/1" name="agent" id="assign-agent">
    			<option value="<?=$authuser->info('id'); ?>">Select an Agent</option>
    			<?php foreach ($agents as $agent) { ?>
    			<option value="<?=$agent['id']; ?>"<?=($lead['agent'] == $agent['id']) ? ' selected' : ''; ?>>
    			<?=Format::htmlspecialchars($agent['name']); ?>
    			</option>
    			<?php } ?>
    		</select>
    	</div>
    	<?php } ?>
    	<?php if (!empty($can_assign_lender)) { ?>
    	<div class="field col w1/2">
    		<label class="field__label">Assigned Lender</label>
    		<select class="w1/1" name="lender">
    			<option value="">-- No Lender --</option>
    			<?php foreach ($lenders as $lender) { ?>
    			<option value="<?=$lender['id']; ?>"<?=($lead['lender'] == $lender['id']) ? ' selected' : ''; ?>>
    			<?=Format::htmlspecialchars($lender['name']); ?>
    			</option>
    			<?php } ?>
    		</select>
    	</div>
    	<?php } ?>
    	<div class="field col w1/2">
    		<label class="field__label">Lead Heat</label>
    		<select class="w1/1" name="heat">
    			<option value=""></option>
    			<option value="hot"<?=(($lead['heat'] == 'hot') ? ' selected' : ''); ?>>Hot</option>
    			<option value="mediumhot"<?=(($lead['heat'] == 'mediumhot') ? ' selected' : ''); ?>>Medium Hot</option>
    			<option value="warm"<?=(($lead['heat'] == 'warm') ? ' selected' : ''); ?>>Warm</option>
    			<option value="lukewarm"<?=(($lead['heat'] == 'lukewarm') ? ' selected' : ''); ?>>Luke Warm</option>
    			<option value="cold"<?=(($lead['heat'] == 'cold') ? ' selected' : ''); ?>>Cold</option>
    		</select>
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label">Status <em class="required">*</em></label>
    		<select class="w1/1" name="status" id="lead_status" required>
    			<option value="unassigned"<?=(($lead['status'] == 'unassigned') ? ' selected' : ''); ?><?=$lead['agent'] !== '1' ? ' disabled' : ''; ?>>Un-Assigned</option>
    			<option value="pending"<?=(($lead['status'] == 'pending') ? ' selected' : ''); ?>>Pending</option>
    			<option value="accepted"<?=(($lead['status'] == 'accepted') ? ' selected' : ''); ?>>Accepted</option>
    			<option value="rejected"<?=(($lead['status'] == 'rejected') ? ' selected' : ''); ?>>Rejected</option>
    			<option value="closed"<?=(($lead['status'] == 'closed') ? ' selected' : ''); ?>>Closed</option>
    		</select>
    	</div>
        <?php if (
            !empty(Settings::getInstance()->MODULES['REW_SHARK_TANK'])
            && $leadsAuth->canAccessSharkTank($authuser)
        ) { ?>
            <div id="in_shark_tank_field"<?=(($lead['status'] !== 'unassigned') ? ' class="hidden"' : ''); ?>>
                <div class="field col w1/1">
                    <label class="field__label">In Shark Tank</label>
                    <select class="w1/1" name="in_shark_tank" id="in_shark_tank"<?=(($lead['status'] !== 'unassigned') ? ' disabled' : ''); ?>>
                        <option value="true"<?=(($lead['status'] === 'unassigned' && $lead['in_shark_tank'] === 'true') ? ' selected' : ''); ?>>Yes</option>
                        <option value="false"<?=(($lead['status'] !== 'unassigned' || $lead['in_shark_tank'] !== 'true') ? ' selected' : ''); ?>>No</option>
                    </select>
                    <label class="hint">If <b>Yes</b> is selected this lead will be entered into the Shark Tank. Notifications will not be sent to agents for this action, but they will be able to view and claim the lead.</label>
                </div>
            </div>
        <?php } ?>
    	<div id="rejectwhy_field" class="cols padH clear <?=($lead['status'] != 'rejected' ? ' hidden' : ''); ?>">
    		<div class="field">
    			<label class="field__label">Reason for rejection <em class="required">*</em></label>
    			<input class="w1/1" name="rejectwhy" value="<?=Format::htmlspecialchars($lead['rejectwhy']); ?>">
    		</div>
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label">First Name <em class="required">*</em></label>
    		<input class="w1/1" name="first_name" value="<?=Format::htmlspecialchars($lead['first_name']); ?>" required>
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label">Last Name <em class="required">*</em></label>
    		<input class="w1/1" name="last_name" value="<?=Format::htmlspecialchars($lead['last_name']); ?>" required>
    	</div>
    </div>
	<div class="field">
		<label class="field__label">Email Address <em class="required">*</em></label>
		<input class="w1/1" type="email" name="email" value="<?=htmlspecialchars($lead['email']); ?>" required>
	</div>
	<div class="field">
		<label class="field__label">Password</label>
		<input class="w1/1" type="password" name="password" value="<?=Format::htmlspecialchars($lead['password']); ?>">
	</div>
	<div class="field">
		<label class="field__label">Quick Notes</label>
		<input class="w1/1" name="notes" value="<?=Format::htmlspecialchars($lead['notes']); ?>">
	</div>
	<div class="field">
		<label class="field__label">Agent Remarks</label>
		<textarea class="w1/1" name="remarks" rows="6" cols="85"><?=Format::htmlspecialchars($lead['remarks']); ?>
</textarea>
	</div>
	<div class="field">
		<label class="field__label">Origin / Referer</label>
		<input class="w1/1" name="referer" value="<?=Format::htmlspecialchars($lead['referer']); ?>">
	</div>
	<div class="field">
		<label class="field__label">Keywords</label>
		<input class="w1/1" name="keywords" value="<?=Format::htmlspecialchars($lead['keywords']); ?>">
	</div>
    <div class="field">
        <label class="field__label">Lead Photo</label>
        <input class="w1/1" name="lead_photo" type="file">
    </div>
	<h3 class="panel__hd">Contact Information</h3>
	<div class="field">
		<label class="field__label">Alternate Email</label>
		<input class="w1/1" type="email" name="email_alt" value="<?=Format::htmlspecialchars($lead['email_alt']); ?>">
        <label class="toggle toggle--stacked">
            <input type="checkbox" name="email_alt_cc_searches" value="saved_searches"<?=($lead['email_alt_cc_searches'] === 'true' ? ' checked' : ''); ?>>
            <span class="toggle__label">Send CC of saved search updates to this email address.</span>
        </label>
	</div>
	<div class="field">
		<label class="field__label">Primary Phone</label>
		<input class="w1/1" type="tel" name="phone" value="<?=Format::htmlspecialchars($lead['phone']); ?>">
		<label class="hint">(###) ###-####</label>
	</div>
	<div class="field">
		<label class="field__label">Status</label>
		<select class="w1/1" name="phone_home_status">
			<option value=""></option>
			<?php

            	// Home # Status
            	foreach (Backend_Lead::$phone_status as $value => $title) {
					echo '<option value="' . $value . '"' . ($lead['phone_home_status'] == $value ? ' selected' : '') . '>' . $title . '</option>';
				}

			?>
		</select>
	</div>
	<div class="field">
		<label class="field__label">Secondary Phone</label>
		<input class="w1/1" type="tel" name="phone_cell" value="<?=Format::htmlspecialchars($lead['phone_cell']); ?>">
		<label class="hint">(###) ###-####</label>
	</div>
	<div class="field">
		<label class="field__label">Status</label>
		<select class="w1/1" name="phone_cell_status">
			<option value=""></option>
			<?php

            	// Cell # Status
            	foreach (Backend_Lead::$phone_status as $value => $title) {
					echo '<option value="' . $value . '"' . ($lead['phone_cell_status'] == $value ? ' selected' : '') . '>' . $title . '</option>';
				}

			?>
		</select>
	</div>
	<div class="field">
		<label class="field__label">Work Phone</label>
		<input class="w1/1" type="tel" name="phone_work" value="<?=Format::htmlspecialchars($lead['phone_work']); ?>">
		<label class="hint">(###) ###-####</label>
	</div>
	<div class="field">
		<label class="field__label">Status</label>
		<select class="w1/1" name="phone_work_status">
			<option value=""></option>
			<?php

            	// Work # Status
            	foreach (Backend_Lead::$phone_status as $value => $title) {
					echo '<option value="' . $value . '"' . ($lead['phone_work_status'] == $value ? ' selected' : '') . '>' . $title . '</option>';
				}

			?>
		</select>
	</div>
	<div class="field">
		<label class="field__label">Fax</label>
		<input class="w1/1" type="tel" name="phone_fax" value="<?=Format::htmlspecialchars($lead['phone_fax']); ?>">
		<label class="hint">(###) ###-####</label>
	</div>

	<div class="field">
		<label class="field__label">Preferred Contact Method</label>
		<div class="toggleset">
		<label> <input type="radio" name="contact_method" value="email"<?=($lead['contact_method'] === 'email' ? ' checked' : ''); ?>> Email</label>
		<label> <input type="radio" name="contact_method" value="phone"<?=($lead['contact_method'] === 'phone' ? ' checked' : ''); ?>> Phone</label>
		<label> <input type="radio" name="contact_method" value="text"<?=($lead['contact_method'] === 'text' ? ' checked' : ''); ?>> Text</label>
        </div>
	</div>

	<h3 class="panel__hd">Mailing Address</h3>
	<div class="field">
		<label class="field__label">Street Address</label>
		<input class="w1/1" name="address1" value="<?=Format::htmlspecialchars($lead['address1']); ?>">
	</div>
	<div class="field">
		<label class="field__label">Street Address (Line 2)</label>
		<input class="w1/1" name="address2" value="<?=Format::htmlspecialchars($lead['address2']); ?>">
	</div>
	<div class="field">
		<label class="field__label">City</label>
		<input class="w1/1" name="city" value="<?=Format::htmlspecialchars($lead['city']); ?>">
	</div>
	<div class="field">
		<label class="field__label">State / Province</label>
		<input class="w1/1" name="state" value="<?=Format::htmlspecialchars($lead['state']); ?>">
	</div>
	<div class="field">
		<label class="field__label">Zip / Postal</label>
		<input class="w1/1" name="zip" value="<?=Format::htmlspecialchars($lead['zip']); ?>">
	</div>

	<?php if (!empty($customFields)) : ?>
		<h3 class="panel__hd">Custom Fields</h3>
		<div class="fld">
			<div id="custom-list">
				<?php foreach ($customFields as $customField) : ?>
					<?=$customField->renderInput($_POST[$customField->getName()]); ?>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endif; ?>

	<h3 class="panel__hd">Search Preferences</h3>
    <?php if ($lead['search_auto'] == 'true') { ?>
	<div class="field">
		<p>This information is collected from the listings that this lead has viewed. Once you change these preferences, it will not be automatically updated.</p>
		<input type="hidden" name="search_preferences" value="<?=Format::htmlspecialchars($search_preferences); ?>">
	</div>
	<?php } ?>
	<div class="field">
		<label class="field__label">Types of Property</label>
		<input class="w1/1" name="search_type" value="<?=Format::htmlspecialchars($lead['search_type']); ?>">
	</div>
	<div class="field">
		<label class="field__label">Cities</label>
		<input class="w1/1" name="search_city" value="<?=Format::htmlspecialchars($lead['search_city']); ?>">
	</div>
	<div class="field">
		<label class="field__label">Subdivisions</label>
		<input class="w1/1" name="search_subdivision" value="<?=Format::htmlspecialchars($lead['search_subdivision']); ?>">
	</div>
	<div class="field">
		<label class="field__label">Minimum Price</label>
		<input class="w1/1" data-currency name="search_minimum_price" value="<?=preg_replace('/[^0-9]/', '', $lead['search_minimum_price']); ?>">
	</div>
	<div class="field">
		<label class="field__label">Maximum Price</label>
		<input class="w1/1" data-currency name="search_maximum_price" value="<?=preg_replace('/[^0-9]/', '', $lead['search_maximum_price']); ?>">
	</div>
	<div class="field">
		<h3 class="panel__hd">Groups</h3>
        <select multiple class="w1/1" name="groups[]">
            <?php foreach ($groups as $group) { ?>
            <option data-data='{ "style": "<?= $group['style']?>" }' value="<?=$group['id'];?>"<?=in_array($group['id'], $lead['groups']) ? ' selected' : ''; ?>><?=$group['name'];?></option>
            <?php } ?>
        </select>
	</div>
                <?php if (!empty(Settings::getInstance()->MODULES['REW_ACTION_PLANS'])) { ?>
                    <?php if ($can_assign_action_plans) { ?>

                            <h3 class="panel__hd">Action Plans</h3>

                                <div class="action_plans">
                                    <?php
                                        // Display Action Plans
                                        if (!empty($action_plans)) {
                                            foreach ($action_plans as $action_plan) {
                                                echo '<label class="toggle toggle--stacked plan plan_' . $action_plan['style'] . ' plan_' . $action_plan['id'] . '" title="' . Format::htmlspecialchars($action_plan['name']) . '">';
                                                echo '<input type="checkbox" name="action_plans[]" value="' . $action_plan['id'] . '"' . (is_array($_POST['action_plans']) && in_array($action_plan['id'], $_POST['action_plans']) ? ' checked' : '') . '> ';
                                                echo '<span class="toggle__label">' . Format::htmlspecialchars(Format::truncate($action_plan['name'], 45)) . '</span></label>';
                                            }
                                        }
                                    ?>
                                </div>


                    <?php } ?>
                <?php } ?>
	<h3 class="panel__hd">Lead Settings</h3>
	<div class="field">
		<label class="field__label">Notify Agent on Saved Listing</label>
		<div>
			<input type="radio" id="notify_favs_yes" name="notify_favs" value="yes"<?=(($lead['notify_favs'] == 'yes') ? ' checked ' : ''); ?>>
			<label class="toggle__label" for="notify_favs_yes">Yes</label>
			<input type="radio" id="notify_favs_no" name="notify_favs" value="no"<?=(($lead['notify_favs'] != 'yes') ? ' checked ' : ''); ?>>
			<label class="toggle__label" for="notify_favs_no">No</label>
		</div>
	</div>
	<div class="field">
		<label class="field__label">Notify Agent on Saved Search</label>
		<div>
			<input type="radio" id="notify_searches_yes" name="notify_searches" value="yes"<?=(($lead['notify_searches'] == 'yes') ? ' checked ' : ''); ?>>
			<label class="toggle__label" for="notify_searches_yes">Yes</label>
			<input type="radio" id="notify_searches_no" name="notify_searches" value="no"<?=(($lead['notify_searches'] != 'yes') ? ' checked ' : ''); ?>>
			<label class="toggle__label" for="notify_searches_no">No</label>
		</div>
	</div>
    <?php if (!empty(Settings::getInstance()->MODULES['REW_TEAMS'])) {?>
    	<div class="field">
    		<label class="field__label">Share Lead with Teams</label>
    		<div>
    			<label class="toggle" for="share_lead_1"><input type="radio" id="share_lead_1" name="share_lead" value=1<?=(($lead['share_lead'] == 1) ? ' checked ' : ''); ?>><span class="toggle__label"> Yes</span></label>
    			<label class="toggle" for="share_lead_0"><input type="radio" id="share_lead_0" name="share_lead" value=0<?=(($lead['share_lead'] != 1) ? ' checked ' : ''); ?>><span class="toggle__label"> No</span></label>
    		</div>
    	</div>
	<?php }?>

</div>

</form>
