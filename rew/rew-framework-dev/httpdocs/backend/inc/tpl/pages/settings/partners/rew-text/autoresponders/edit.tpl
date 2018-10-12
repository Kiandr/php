<div class="block">
    <div class="bar">
        <div class="bar_title">
            <?php if ($authuser->isSuperAdmin()) { ?>
                <h2 class="text"><?= __('%s\'s Auto-Responder', Format::htmlspecialchars($agent['name'])); ?></h2>
            <?php } else { ?>
                <h2 class="text"><?= __('Your REWText Auto-Responder'); ?></h2>
            <?php } ?>
        </div>
        <div class="bar__actions">
            <a class="btn btn--ghost" href="/backend/settings/partners/rew-text/autoresponders/"><svg class="icon icon-left-a mar0"><use xlink:href="/backend/img/icos.svg#icon-left-a"/></svg></a>
        </div>
    </div>
    <form action="?id=<?=$autoresponder['id']; ?>&submit" method="post">
        <div class="btns btns--stickyB">
            <span class="R">
                <button type="submit" class="btn btn--positive"><?= __('Save'); ?></button>
            </span>
        </div>
    	<div class="field">
    		<label class="field__label"><?= __('Auto-Responder Message'); ?> <em class="required">*</em></label>
    		<textarea name="body" class="w1/1" rows="4" maxlength="<?=$maxlength; ?>"<?=(!$autoresponder['media'] ? ' required' : ''); ?>><?=Format::htmlspecialchars($autoresponder['body']); ?></textarea>
    		<label class="text--mute"><em class="charsRemaining">&nbsp;</em></label>
    		<label class="text--mute"><strong><?= __('Available Tags'); ?>:</strong> {first_name}, {last_name}</label>
    	</div>
    	<div class="field">
    		<label class="field__label">Is Active</label>
    		<div>
    			<input id="active_true" type="radio" name="active" value="1"<?=!empty($autoresponder['active']) ? ' checked' : ''; ?>>
    			<label for="active_true" class="marR"><?= __('Yes'); ?></label>
    			<input id="active_false" type="radio" name="active" value="0"<?=empty($autoresponder['active']) ? ' checked' : ''; ?>>
    			<label for="active_false"><?= __('No'); ?></label>
    		</div>
    	</div>
    	<div class="field">
    		<h3 class="panel__hd"><?= __('Media Attachment'); ?></h3>
    		<div id="attach-preview" style="clear: both;"></div>
    		<button id="attach-media" type="button" class="btn<?=($autoresponder['media'] ? ' hidden' : ''); ?> btn--positive"><?= __('Attach an Image'); ?></button>
    		<?php
                // Media attachment
                if (!empty($autoresponder['media'])) {
                    echo '<div class="attached-media">';
                    echo '<input type="hidden" name="media" value="' . Format::htmlspecialchars($autoresponder['media']) . '">';
                    echo '<img src="' . Format::htmlspecialchars($autoresponder['media']) . '" alt="">';
                    echo '<em>(' . __('click image to remove') . ')</em>';
                    echo '</div>';
                }

            ?>
    	</div>
    </form>
</div>