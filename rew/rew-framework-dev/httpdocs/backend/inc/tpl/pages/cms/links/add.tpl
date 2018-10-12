<div class="bar">
    <div class="bar__title">Add Link</div>
    <div class="bar__actions">
        <a href="/backend/cms/" class="bar__action"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
    </div>
</div>
<div class="block">
<form action="?submit" method="post" class="rew_check">


    <div>
        <?php
            echo $this->view->render('::partials/subdomain/selector', [
                'subdomain' => $subdomain,
            ]);
        ?>
    </div>

	<div class="field">
		<label class="field__label">Link Level</label>
		<select class="w1/1" name="category">
			<option value="">----- Set as Main Link -----</option>
			<?php if (!empty($pages) && is_array($pages)) : ?>
			<?php foreach ($pages as $category) : ?>
			<?php $selected = ($category['file_name'] == $_POST['category']) ? ' selected="selected"' : ''; ?>
			<option value="<?=$category['file_name']; ?>"<?=$selected; ?>>
			<?=Format::htmlspecialchars($category['link_name']); ?>
			</option>
			<?php endforeach; ?>
			<?php endif; ?>
		</select>
		<p class="text--mute">
			<?=tpl_lang('DESC_FORM_CMS_LINK_PARENT'); ?>
		</p>
	</div>
    <div class="cols">
    	<div class="field col w1/3">
    		<label class="field__label">Link URL <em class="required">*</em></label>
    		<input class="w1/1" type="text" class="search_input" name="file_name" value="<?=$_POST['file_name']; ?>" placeholder="http://" required>
    		<p class="text--mute text--small">This field is for the URL of the page you wish to link to. (Ex. http://www.google.com)</p>
    	</div>
    	<div class="field col w1/3">
    		<label class="field__label">Link Name <em class="required">*</em></label>
    		<input class="w1/1" type="text" class="search_input" name="link_name" value="<?=Format::htmlspecialchars($_POST['link_name']); ?>" required>
    		<p class="text--mute text--small">This field is for the link name as it will appear in the navigation menu on your site.</p>
    	</div>
    	<div class="field col w1/3">
    		<label class="field__label">Target</label>
    		<select class="w1/1" name="footer">
    			<option value="_self"<?=($_POST['footer'] == '_self') ? ' selected="selected"' : ''; ?>>Current Browser Window</option>
    			<option value="_blank"<?=($_POST['footer'] == '_blank') ? ' selected="selected"' : ''; ?>>New Browser Window</option>
    		</select>
    		<p class="text--mute text--small">This option allows you to have the link open in a new window, or in the current page.</p>
    	</div>
    </div>
	<div class="btns btns--stickyB">
		<span class="R">
			<button class="btn btn--positive"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> Save </button>
		</span>
	</div>

</form>
</div>