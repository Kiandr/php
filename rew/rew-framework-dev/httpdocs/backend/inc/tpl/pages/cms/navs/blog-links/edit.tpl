<form action="?submit" method="post" class="rew_check">
	<input type="hidden" name="id" value="<?=$edit_link['id']; ?>">

	<div class="bar">
		<div class="bar__title"><?=Format::htmlspecialchars($edit_link['title']); ?></div>
		<div class="bar__actions">
			<a class="bar__action" href="/backend/cms/navs/blog-links/"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
		</div>
	</div>

    <div class="block">

	<div class="btns btns--stickyB">
    	<span class="R">
		    <button class="btn btn--positive" type="submit"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?></button>
		</span>
    </div>

    	<div class="field">
    		<label class="field__label"><?= __('Title'); ?> <em class="required">*</em></label>
    		<input class="w1/1" name="title" value="<?=Format::htmlspecialchars($edit_link['title']); ?>" required>
    	</div>
        <div class="cols">
        	<div class="field col w1/2">
        		<label class="field__label"><?= __('URL'); ?> <em class="required">*</em></label>
        		<input class="w1/1" type="url" name="link" value="<?=Format::htmlspecialchars($edit_link['link']); ?>" placeholder="http://" pattern="https?://.+" required>
        	</div>
        	<div class="field col w1/2">
        		<label class="field__label"><?= __('Target'); ?></label>
        		<select class="w1/1" name="target">
        			<option value="_blank"<?=($edit_link['target'] == '_blank') ? ' selected' : ''; ?>><?= __('New Browser Window'); ?></option>
        			<option value="_self"<?=($edit_link['target'] == '_self') ? ' selected' : ''; ?>><?= __('Current Browser Window'); ?></option>
        		</select>
        	</div>
        </div>

    </div>

</form>