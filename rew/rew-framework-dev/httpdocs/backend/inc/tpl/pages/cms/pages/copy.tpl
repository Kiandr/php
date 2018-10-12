<form action="?submit" method="post">
	<input type="hidden" name="id" value="<?=$copy_page['page_id']; ?>">

    <div class="bar">
        <div class="bar__title"><?= __('Copy'); ?> '<?=Format::htmlspecialchars($copy_page['link_name']); ?>'</div>
        <div class="bar__actions">
            <a class="bar__action" href="/backend/cms/<?=$subdomain->getPostLink();?>"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
        </div>
    </div>

    <div class="block">


        <?php
            echo $this->view->render('::partials/subdomain/selector', [
                'subdomain' => $subdomain,
            ]);
        ?>

        <div class="field">
    		<label class="field__label"><?= __('Page Level'); ?> <em class="required">*</em></label>
    		<select class="w1/1" name="category">
    			<option value="">----- <?= __('Set as Main Page'); ?> -----</option>
    			<?php
    				// Main Categories
    				if (!empty($pages)&& is_array($pages)) {
    				   	foreach ($pages as $category) {
    				       	$selected = ($category['file_name'] == $_POST['category']) ? ' selected' : '';
    				       	echo '<option value="' . $category['file_name'] . '"' . $selected . '>' . Format::htmlspecialchars($category['link_name']) . '</option>';
    				   	}
    				}

    				?>
    		</select>
    	</div>
    	<div class="field">
    		<label class="field__label">
    			<?=tpl_lang('LBL_FORM_CMS_PAGE_ALIAS'); ?>
    			<em class="required">*</em></label>
    		<input class="w1/1" name="file_name" id="file_name" value="<?=Format::htmlspecialchars($_POST['file_name']); ?>" data-slugify required>
    	</div>
    	<div class="field">
    		<label class="field__label">
    			<?=tpl_lang('LBL_FORM_CMS_LINK_LABEL'); ?>
    			<em class="required">*</em></label>
    		<input class="w1/1" name="link_name" value="<?=Format::htmlspecialchars($_POST['link_name']); ?>" required>
    	</div>
    	<div class="field">
    		<label class="field__label">
    			<?=tpl_lang('LBL_FORM_CMS_PAGE_TITLE'); ?>
    			<em class="required">*</em></label>
    		<input class="w1/1" name="page_title" value="<?=Format::htmlspecialchars($_POST['page_title']); ?>" required>
    	</div>
    	<div class="btns btns--stickyB"> <span class="R">
    		<button class="btn btn--positive"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?> </button>
    		</span>
    </div>

    </div>

</form>