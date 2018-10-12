<div class="bar">
    <h2 class="bar__title mar0">Edit <?=$edit_link['link_name']; ?></h2>
    <div class="bar__actions">
        <a href="<?='/backend/cms/' . $subdomain->getPostLink(); ?>" class="bar__action btn btn--ghost timeline__back"><svg class="icon icon-left-a mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
    </div>
</div>
<div class="block">
<form action="?submit" method="post" class="rew_check">
	<input type="hidden" name="id" value="<?=$edit_link['page_id']; ?>">


        <?php
            echo $this->view->render('::partials/subdomain/selector', [
                'subdomain' => $subdomain,
                'subdomains' => $subdomains,
            ]);
        ?>

    <div class="field">
		<label class="field__label">Re-Assign To</label>
		<select class="w1/1" name="category">
			<option value="<?=$edit_link['file_name']; ?>">----- Set as Main Link -----</option>
			<?php if (is_array($pages)) : ?>
			<?php foreach ($pages as $category) : ?>
			<option value="<?=$category['file_name']; ?>"<?=($category['file_name'] == $edit_link['category']) ? ' selected="selected"' : ''; ?>>
			<?=Format::htmlspecialchars($category['link_name']); ?>
			</option>
			<?php endforeach; ?>
			<?php else : ?>
			<option value="<?=$edit_link['file_name']; ?>">
			<?=$pages; ?>
			</option>
			<?php endif; ?>
		</select>
		<p class="text--mute">
			<?=tpl_lang('DESC_FORM_CMS_PAGE_PARENT'); ?>
		</p>
	</div>
    <div class="cols">
    	<div class="field col w1/3">
    		<label class="field__label">Link URL <em class="required">*</em></label>
    		<input type="text" class="search_input w1/1" name="file_name" value="<?=$edit_link['file_name']; ?>" placeholder="http://" required>
    		<p class="text--mute text--small">This field is for the URL of the page you wish to link to. (Ex. http://www.google.com).</p>
    	</div>
    	<div class="field col w1/3">
    		<label class="field__label">Link Name <em class="required">*</em></label>
    		<input type="text" class="search_input w1/1" name="link_name" value="<?=Format::htmlspecialchars($edit_link['link_name']); ?>" required>
    		<p class="text--mute text--small">This field is for the link name as it will appear in the navigation menu on your site.</p>
    	</div>
    	<div class="field col w1/3">
    		<label class="field__label">Target</label>
    		<select class="w1/1" name="footer">
    			<option value="_self"<?=($edit_link['footer'] == '_self') ? ' selected="selected"' : ''; ?>>Current Browser Window</option>
    			<option value="_blank"<?=($edit_link['footer'] == '_blank') ? ' selected="selected"' : ''; ?>>New Browser Window</option>
    		</select>
    		<p class="text--mute text--small">This option allows you to have the link open in a new window, or in the current page.</p>
    	</div>
    </div>
	<div class="btns btns--stickyB"> <span class="R">
		<button class="btn btn--positive"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> Save </button>
		</span> </div>
</form>
</div>