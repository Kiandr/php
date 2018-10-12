<?php

// Preview Document
if (!empty($preview)) {

?>

<div class="bar">
    <div class="bar__title"><?=Format::htmlspecialchars($document['name']); ?></div>
    <div class="bar__actions">
        <a class="bar__action" href="<?=URL_BACKEND; ?>leads/docs/?tab=documents"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
    </div>
</div>

<div class="block">

    <textarea id="preview-document" cols="24" rows="12" readonly disabled><?=Format::htmlspecialchars($document['document']); ?></textarea>
    <?php
    	return;

    // Require Categories
    } elseif (empty($categories)) {

    	echo '<div class="block"><h2>' . __('Add New Form Letter') . '</h2>';
        echo '<div class="field"><p>' . __('To add a form letter, you must first %s.', '<a href="../category/">' . __('create a category') . '</a>') . '</p></div></div>';
    	return;

    }

    ?>
    <form action="?submit" method="post" class="rew_check">

        <div class="bar">
            <div class="bar__title">
        		<?php if (!empty($document['id'])) { ?>
                    <?=Format::htmlspecialchars($document['name']); ?>
                    <input type="hidden" name="id" value="<?=$document['id']; ?>">
                <?php } else {
                    echo __('New Form Letter');
                } ?>
            </div>
            <div class="bar__actions">
                <a class="bar__action" href="/backend/leads/docs/?tab=template"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
            </div>
        </div>

        <div class="block">

    	<div class="btns btns--stickyB">
    		<span class="R">
    			<button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?></button>
    		</span>
    	</div>

    	<input type="hidden" name="is_html" value="<?=$document['is_html']; ?>">
        <div class="cols">
        	<div class="field col w3/4">
        		<label class="field__label"><?= __('Form Letter Name'); ?> <em class="required">*</em></label>
        		<input class="w1/1" type="text" name="name" value="<?=Format::htmlspecialchars($document['name']); ?>" required>
        	</div>
        	<div class="field col w1/4">
        		<label class="field__label"><?= __('Category'); ?> <em class="required">*</em></label>
        		<select class="w1/1" name="category" required>
        			<option value=""><?= __('Select a Category'); ?></option>
        			<?php

        				// Document categories
        				foreach ($categories as $category) {
        					echo '<option value="' . $category['id'] . '"' . ($document['category'] == $category['id'] ? ' selected' : '') . '>' . Format::htmlspecialchars($category['name']) . '</option>';
        				}

        			?>
        		</select>
        	</div>
        </div>
    	<div class="field">
    		<label class="field__label">
                <?= __('Message'); ?>
                <em class="required">*</em>
                <span class="R">
                    <a href="javascript:void(0);" id="toggle-editor"><?= __('Switch to'); ?> <?=($document['is_html'] != 'false') ? __('Plain Text') : __('WYSIWYG Editor'); ?></a>
                </span>
            </label>
    		<textarea class="w1/1 tinymce email<?=($document['is_html'] != 'false' ? '' : ' off'); ?>" id="document" name="document" rows="15" cols="80"><?=Format::htmlspecialchars($document['document']); ?></textarea>
            <label class="hint"><?= __('Tags'); ?>: {first_name}, {last_name}, {email}, {signature}, {unsubscribe}, {verify}</label>
    	</div>
    	<?php if (!empty($can_share)) { ?>
    	<div class="field">
    		<label class="field__label toggle">
    			<input type="checkbox" name="share" value="true"<?=($document['share'] === 'true' ? ' checked' : ''); ?>>
    			<span class="toggle__label"><?= __('Share this Form Letter'); ?></span>
            </label>
    	</div>
    	<?php } ?>
    </form>

</div>