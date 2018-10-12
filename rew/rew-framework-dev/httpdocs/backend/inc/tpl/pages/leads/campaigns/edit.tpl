	<div class="bar">
		<div class="bar__title"><?=Format::htmlspecialchars($campaign['name']); ?></div>
		<div class="bar__actions">
			<a href="/backend/leads/campaigns/" class="bar__action"><svg class="icon "><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
		</div>
	</div>
<div class="block">

<form action="?submit" method="post" class="rew_check">
	<input type="hidden" name="id" value="<?=$campaign['id']; ?>">

	<div class="btns btns--stickyB"> <span class="R">
		<button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?></button>
		</span> </div>
	<?php if ($can_manage_all && $campaign['agent_id'] == 1) { ?>
	<h3><?= __('Sender Information'); ?> <em class="required">*</em></h3>
	<div class="field">
		<div>
			<label class="toggle" for="sender_admin">
				<input id="sender_admin" type="radio" name="sender" value="admin"<?=($campaign['sender'] == 'admin') ? ' checked' : ''; ?>>
				<span class="toggle__label"><?=Format::htmlspecialchars($super_admin['first_name']); ?> <?=Format::htmlspecialchars($super_admin['last_name']); ?></span>
			</label>

			<label class="toggle" for="sender_agent">
				<input id="sender_agent" type="radio" name="sender" value="agent"<?=($campaign['sender'] == 'agent') ? ' checked' : ''; ?>>
				<span class="toggle__label"><?= __('Assigned Agent'); ?></span>
			</label>

			<label class="toggle" for="sender_custom">
				<input id="sender_custom" type="radio" name="sender" value="custom"<?=($campaign['sender'] == 'custom') ? ' checked' : ''; ?>>
				<span class="toggle__label"><?= __('Custom'); ?></span>
			</label>
		</div>
	</div>
	<div id="campaign-sender"<?=($campaign['sender'] == 'custom') ? '' : ' class="hidden"'; ?>>
		<div class="field">
			<label class="field__label"><?= __('Sender Name'); ?> <em class="required">*</em></label>
			<input class="w1/1" name="sender_name" value="<?=Format::htmlspecialchars($campaign['sender_name']); ?>">
		</div>
		<div class="field">
			<label class="field__label"><?= __('Sender Email'); ?> <em class="required">*</em></label>
			<input class="w1/1" name="sender_email" value="<?=Format::htmlspecialchars($campaign['sender_email']); ?>">
		</div>
	</div>
	<?php } ?>

  <h3 class="panel__hd"><?= __('Campaign Settings'); ?></h3>

	<div class="field">
		<label class="field__label"><?= __('Status'); ?></label>
		<div>
			<label class="toggle" for="active_true">
				<input id="active_true" type="radio" name="active" value="Y"<?=(($campaign['active'] == 'Y') ? ' checked' : ''); ?>>
				<span class="toggle__label"><?= __('Active'); ?></span>
			</label>

			<label class="toggle" for="active_false">
				<input id="active_false" type="radio" name="active" value="N"<?=(($campaign['active'] != 'Y') ? ' checked' : ''); ?>>
				<span class="toggle__label"><?= __('Inactive'); ?></span>
			</label>
		</div>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Start Date'); ?></label>
		<input class="w1/1" name="starts" value="<?=!empty($campaign['starts']) ? date('D, M. j, Y', strtotime($campaign['starts'])) : $campaign['starts']; ?>" placeholder="<?= __('Select a Date&hellip;'); ?>">
	</div>
    <div class="cols">
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Groups'); ?></label>

            <select multiple class="w1/1" name="groups[]">
    			<?php foreach ($groups as $group) { ?>
                <option data-data='{ "style": "<?= $group['style']?>" }' value="<?=$group['id'];?>"<?=in_array($group['id'], $campaign['groups']) ? ' selected' : ''; ?>><?=$group['name'];?></option>
    			<?php } ?>
    		</select>

    	</div>
      <div class="field col w1/2">
        <label class="field__label"><?= __('Template (Optional)'); ?></label>
        <select class="w1/1" id="tempid" name="tempid">
          <option value=""><?= __('No Template'); ?></option>
          <?php foreach ($templates as $template) :?>
          <option value="<?=$template['id']; ?>"<?=(($campaign['tempid'] == $template['id']) ? ' selected' : ''); ?>>
          <?=$template['name']; ?>
          </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

	<h3 class="panel__hd"><?= __('Campaign Details'); ?></h3>
	<?php if ($can_manage_all && $authuser->info('id') != $campaign['agent_id']) { ?>
	<div class="field">
		<label class="field__label"><?= __('Campaign Owner'); ?></label>
		<a href="<?=URL_BACKEND; ?>agents/agent/summary/?id=<?=$agent['id']; ?>">
		<?=Format::htmlspecialchars($agent['first_name']); ?>
		<?=Format::htmlspecialchars($agent['last_name']); ?>
		</a> </div>
	<?php } ?>
	<div class="field">
		<label class="field__label"><?= __('Campaign Name'); ?> <em class="required">*</em></label>
		<input class="w1/1" name="name" value="<?=Format::htmlspecialchars($campaign['name']); ?>" required>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Description'); ?></label>
		<textarea class="w1/1" name="description"><?=Format::htmlspecialchars($campaign['description']); ?></textarea>
	</div>

	<h3><?= __('Campaign Emails'); ?></h3>

	<div id="campaign-emails">
		<?php foreach ($emails as $i => $email) { ?>
		<div class="block block--bg email">
			<div class="main-header">
				<h3 class="ttl"><?= __('Email'); ?></h3>
				<div class="btns R">
                    <a class="btn btn--ghost delete" href="javascript:void(0);">
                        <svg class="icon icon-trash mar0">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use>
                        </svg>
                    </a>
                </div>
			</div>
			<input type="hidden" name="emails[<?=$i; ?>][id]" value="<?=$email['id']; ?>">
            <div class="cols">
    			<div class="field col w1/4">
    				<label class="field__label"><?= __('Day'); ?> <em class="required">*</em></label>
    				<input class="w1/1 numeric" type="number" min="0" name="emails[<?=$i; ?>][send_delay]" value="<?=Format::htmlspecialchars($email['send_delay']); ?>">
    			</div>
    			<div class="field col w3/4">
    				<label class="field__label"><?= __('Email Subject'); ?> <em class="required">*</em></label>
    				<input class="w1/1" name="emails[<?=$i; ?>][subject]" value="<?=Format::htmlspecialchars($email['subject']); ?>">
    			</div>
            </div>
			<div class="field">
				<label class="field__label"><?= __('Email Message'); ?> <em class="required">*</em></label>
				<select class="w1/1" name="emails[<?=$i; ?>][doc_id]">
					<option value=""><?= __('Select a message'); ?></option>
					<?php
						// Documents
						foreach ($docs as $cat_id => $cat) {
							echo '<optgroup label="' . $cat['name'] . '">';
							if (!empty($cat['docs'])) {
								foreach ($cat['docs'] as $doc_id => $doc_name) {
									if (!empty($doc_id)) {
										echo '<option value="' . $doc_id . '"' . ($email['doc_id'] == $doc_id ? ' selected' : '') . '>' . Format::htmlspecialchars($doc_name) . '</option>';
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
				</div>

		<p class="btns">
			<a id="add-campaign-email" class="btn" href="javascript:void(0);"><?= __('Add Campaign Email'); ?></a>
		</p>

</div>

</form>