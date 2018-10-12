<div class="block">
    <div class="bar">
        <div class="bar_title">
            <h2><?= __('Setup Auto-Responder'); ?></h2>
        </div>
        <div class="bar__actions">
            <a class="btn btn--ghost" href="/backend/settings/partners/rew-text/autoresponders/"><svg class="icon icon-left-a mar0"><use xlink:href="/backend/img/icos.svg#icon-left-a"/></svg></a>
        </div>
    </div>
    <form action="?submit" method="post">
    	<div class="btns btns--stickyB">
            <span class="R">
    		    <button type="submit" class="btn btn--positive"><?= __('Save'); ?></button>
    		</span>
        </div>
    	<div class="field">
    	<label class="field__label">
    	<?= __('Assign to Agent'); ?> <em class="required">*</em>
    	<?php
            if(!empty($selectedAgent)) {
                echo sprintf('<input type="hidden" name="agent_id" value="%s">', $selectedAgent);
            }
    		// Available agents
    		if (!empty($agents) && is_array($agents)) {
    			echo sprintf('<select class="w1/1" required %s>',(!empty($selectedAgent)) ? "disabled" : "name=\"agent_id\"");
    			echo '<option value="">-- ' . __('Select an Agent') . ' --</option>';
    			foreach ($agents as $agent) {
    				$selected = ($_POST['agent_id'] == $agent['id'] || $_GET['agent_id'] == $agent['id'] ? ' selected' : '');
    				echo '<option value="' . Format::htmlspecialchars($agent['id']) . '"' . $selected . '>';
    				echo Format::htmlspecialchars($agent['name']);
    				echo '</option>';
    			}
    			echo '</select>';
    		} else {
    			echo '<p class="boxed">' . __('No agents available to assign') . ' - <a href="../">' . __('try managing existing auto-responders.') . '</a></p>';
    		}

    		?>
    	</div>
    	<div class="field">
    		<label class="field__label">
    		<?= __('Auto-Responder Message'); ?> <em class="required">*</em>
    		</h2>
    		<textarea class="w1/1" name="body" rows="4" maxlength="<?=$maxlength; ?>"<?=(!$media ? ' required' : ''); ?>><?=Format::htmlspecialchars($_POST['body']); ?>
    </textarea>
    		<label class="hint"><em class="charsRemaining">&nbsp;</em></label>
    		<label class="hint"><?= __('Available Tags'); ?>: {first_name}, {last_name}</label>
    	</div>
    	<div class="field">
    		<label class="field__label"><?= __('Is Active'); ?></label>
    		<div class="buttonset radios compact">
    			<input id="active_true" type="radio" name="active" value="1"<?=!empty($_POST['active']) ? ' checked' : ''; ?>>
    			<label for="active_true" class="marR"><?= __('Yes'); ?></label>
    			<input id="active_false" type="radio" name="active" value="0"<?=empty($_POST['active']) ? ' checked' : ''; ?>>
    			<label for="active_false"><?= __('No'); ?></label>
    		</div>
    	</div>
    	<div class="field">
    		<h3 class="panel__hd"><?= __('Media Attachment'); ?></h3>
    		<div id="attach-preview" style="clear: both;"></div>
    		<button id="attach-media" type="button" class="btn<?=($media ? ' hidden' : ''); ?> btn--positive"><?= __('Attach an Image'); ?></button>
    		<?php

    			// Media attachment
    			if (!empty($media)) {
    				echo '<div class="attached-media">';
    				echo '<input type="hidden" name="media" value="' . Format::htmlspecialchars($media) . '">';
    				echo '<img src="' . Format::htmlspecialchars($media) . '" alt="">';
    				echo '<em>(' . __('click image to remove') . ')</em>';
    				echo '</div>';
    			}

    		?>
    	</div>
    </form>
</div>