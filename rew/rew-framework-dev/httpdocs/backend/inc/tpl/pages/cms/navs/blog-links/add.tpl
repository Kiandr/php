<form action="?submit" method="post" class="rew_check">

	<div class="bar">
		<div class="bar__title"><?= __('New Blog Link'); ?></div>
		<div class="bar__actions">
			<a class="bar__action" href="/backend/cms/navs/blog-links/"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
		</div>
	</div>

	<div class="block">

		<div class="field">
			<label class="field__label"><?= __('Title'); ?> <em class="required">*</em></label>
			<input class="w1/1" name="title" value="<?=Format::htmlspecialchars($_POST['title']); ?>" required>
		</div>
		<div class="cols">
			<div class="field col w1/2">
				<label class="field__label"><?= __('URL'); ?> <em class="required">*</em></label>
				<input class="w1/1" type="url" name="link" value="<?=Format::htmlspecialchars($_POST['link']); ?>" placeholder="http://" pattern="https?://.+" required>
			</div>
			<div class="field col w1/2">
				<label class="field__label"><?= __('Target'); ?></label>
				<select class="w1/1" name="target">
					<option value="_blank"<?=($_POST['target'] == '_blank') ? ' selected' : ''; ?>><?= __('New Browser Window'); ?></option>
					<option value="_self"<?=($_POST['target'] == '_self') ? ' selected' : ''; ?>><?= __('Current Browser Window'); ?></option>
				</select>
			</div>
		</div>
		<div class="btns btns--stickyB"> <span class="R">
			<button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?></button>
			</span>
		</div>

	</div>

</form>