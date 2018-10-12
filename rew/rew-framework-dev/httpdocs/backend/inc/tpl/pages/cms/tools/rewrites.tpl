<form action="?submit" method="post" class="rew_check">


	<?php if (!empty($edit)) { ?>

	<div class="bar">
		<div class="bar__title"><?= __('Edit Redirect Rule %s', Format::htmlspecialchars($edit['old']) ); ?></div>
		<div class="bar__actions">
			<a class="bar__action timeline__back" href="<?='/backend/cms/tools/rewrites/'; ?>"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
		</div>
	</div>

	<div class="btns btns--stickyB">
	<span class="R">
		<button type="submit" class="btn btn--positive"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?></button>
	</div>

	<?php } elseif (!empty($show_form)) { ?>
	<?php } else { ?>

        <div class="bar">
            <div class="bar__title">
                <?= __('Redirect Rules'); ?>
            </div>
            <div class="bar__actions">
                <a class="bar__action" href="?add">
                    <svg class="icon">
                        <use xlink:href="/backend/img/icos.svg#icon-add"/>
                    </svg>
                </a>
            </div>
        </div>

	<?php } ?>
	<?php

		// Edit Row
		if (!empty($edit)) {

	?>

    <div class="block">

	<input type="hidden" name="edit" value="<?=$edit['id']; ?>">
	<div class="field">
		<label class="field__label"><?= __('Old Filename'); ?> <em class="required">*</em></label>
		<input class="w1/1" type="text" name="old" value="<?=htmlspecialchars($edit['old']); ?>" required>
	</div>
	<div class="field">
		<label class="field__label"><?= __('New Filename'); ?> <em class="required">*</em></label>
		<input class="w1/1" type="text" name="new" value="<?=htmlspecialchars($edit['new']); ?>" required>
	</div>

    </div>

	<?php

		} else {

			// Add Row
			if (!empty($show_form))  {

	?>


	<div class="bar">
		<div class="bar__title"><?= __('Add Redirect Rule'); ?></div>
		<div class="bar__actions">
			<a class="bar__action timeline__back" href="/backend/cms/tools/rewrites/"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
		</div>
	</div>

	<div class="btns btns--stickyB">
	    <span class="R">
		    <button type="submit" class="btn btn--positive"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?></button>
		</span>
	</div>

    <div class="block">

	<input type="hidden" name="add" value="true">
	<div class="field">
		<label class="field__label"><?= __('Old Filename'); ?> <em class="required">*</em></label>
		<input class="w1/1" type="text" name="old" value="<?=htmlspecialchars($_POST['old']); ?>" required>
	</div>
	<div class="field">
		<label class="field__label"><?= __('New Filename'); ?> <em class="required">*</em></label>
		<input class="w1/1" type="text" name="new" value="<?=htmlspecialchars($_POST['new']); ?>" required>
	</div>

    </div>

	<?php

	// Manage Rows
	} else {

		// None Available
		if (empty($rewrites)) {
			echo '<p class="block">' . __('You currently have no redirect rules.') . '</p>';

		} else {

	?>

    <div class="nodes">
    	<ul class="nodes__list">

    		<?php foreach ($rewrites as $rewrite) { ?>
    		<li class="nodes__branch">
    		    <div class="nodes__wrap">
        			<div class="article">
                        <div class="article__body">
                            <div class="article__content">
                                <a class="text text--strong" href="?edit=<?=$rewrite['id']; ?>"> <?=Format::htmlspecialchars($rewrite['old']); ?></a>
                                <div class="text text--mute"><?= __('Redirects to:'); ?> <?=Format::htmlspecialchars($rewrite['new']); ?></div>
                            </div>
                        </div>
        			</div>
                    <div class="nodes__actions">
						<a class="btn btn--ico btn--ghost" href="<?=$rewrite['deleteLink']; ?>" onclick="return confirm('<?= __('Are you sure you would like to delete this redirect rule?'); ?>');">
						<svg class="icon icon-trash mar0">
							<use xlink:href="/backend/img/icos.svg#icon-trash"/>
						</svg>
						</a>
                    </div>
                </div>
    		</li>
    		<?php } ?>

    	</ul>
    </div>

	<?php

			}

		}
	}

?>
</form>

<?php if (!empty($paginationLinks) && (empty($edit) && !$show_form)) { ?>
<div class="nav_pagination">
    <?php if (!empty($paginationLinks['prevLink'])) { ?>
    <a class="prev marR" href="<?=$paginationLinks['prevLink']; ?>">
        <svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg>
    </a>
    <?php } ?>
    <?php if (!empty($paginationLinks['nextLink'])) { ?>
    <a class="next" href="<?=$paginationLinks['nextLink']; ?>">
        <svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-right-a"></use></svg>
    </a>
    <?php } ?>
</div>
<?php } ?>