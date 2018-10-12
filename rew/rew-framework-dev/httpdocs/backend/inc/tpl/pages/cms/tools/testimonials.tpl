<form action="?submit" method="post" enctype="multipart/form-data" class="rew_check">

	<div class="bar">
		<div class="bar__title"><?= __('Testimonials'); ?></div>
		<?php if (!empty($edit_form) || !empty($add_form)) { ?>
		<div class="bar__actions">
			<a class="bar__action timeline__back" href="<?='/backend/cms/tools/testimonials/'; ?>"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
		</div>
		<?php } else { ?>
		<div class="bar__actions">
			<a class="bar__action" href="/backend/cms/tools/testimonials/?add"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use></svg></a>
		</div>
		<?php } ?>
	</div>

	<div class="btns btns--stickyB">
    	<span class="R">
    		<?php if (!empty($edit_form)) { ?>
    		<button class="btn btn--positive"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?> </button>
    		<?php } elseif (!empty($add_form)) { ?>
    		<button class="btn btn--positive"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?> </button>
    		<?php } ?>
		</span>
    </div>

	<?php if (!empty($edit_form)) { ?>
	<div class="block">
	<input type="hidden" name="edit" value="<?=$testimonial['id']; ?>">
	<?php
		// Assign agent to testimonial
		if (!empty($can_assign_agent) && !empty($agents)) {
			echo '<div class="field">';
			echo '<label class="field__label">' . __('Assign to Agent') . '</label>';
			echo '<select class="w1/1" name="agent_id">';
			echo '<option value="">-- ' . __('Select an Agent') . ' --</option>';
			foreach ($agents as $agent) {
				$selected = $agent['id'] === $testimonial['agent_id'] ? ' selected' : '';
				echo '<option value="' . $agent['id'] . '"' . $selected . '>' . Format::htmlspecialchars($agent['name']) . '</option>';
			}
			echo '</select>';
			echo '</div>';
		}

	?>
	<div class="field">
		<label class="field__label"><?= __('Client Name'); ?> </label>
		<input class="w1/1" type="text" class="search_input" name="client" value="<?=Format::htmlspecialchars($testimonial['client']); ?>">
	</div>
	<?php if ($can_include_link) { ?>
		<div class="field">
			<label class="field__label"><?= __('Link'); ?> </label>
			<input type="text" class="w1/1 search_input" name="link" value="<?=Format::htmlspecialchars($testimonial['link']); ?>">
		</div>
	<?php } ?>
	<div class="field">
		<label class="field__label"><?= __('Testimonial'); ?> <em class="required">*</em></label>
		<textarea class="w1/1 tinymce simple" name="testimonial" rows="5"><?=Format::htmlspecialchars($testimonial['testimonial']); ?></textarea>
	</div>
	</div>
	<?php } elseif (!empty($add_form)) { ?>
	<div class="block">
	<input type="hidden" name="add" value="true">
	<?php

		// Assign agent to testimonial
		if (!empty($can_assign_agent) && !empty($agents)) {
			echo '<div class="field">';
			echo '<label class="field__label">' . __('Assign to Agent') . '</label>';
			echo '<select class="w1/1" name="agent_id">';
			echo '<option value="">-- ' . __('Select an Agent') . ' --</option>';
			foreach ($agents as $agent) {
				$selected = $agent['id'] === $_POST['agent_id'] ? ' selected' : '';
				echo '<option value="' . $agent['id'] . '"' . $selected . '>' . Format::htmlspecialchars($agent['name']) . '</option>';
			}
			echo '</select>';
			echo '</div>';
		}

	?>
	<div class="field">
		<label class="field__label"><?= __('Client Name'); ?> </label>
		<input class="w1/1" type="text" class="search_input" name="client" value="<?=Format::htmlspecialchars($_POST['client']); ?>">
	</div>
	<?php if ($can_include_link) { ?>
		<div class="field">
			<label class="field__label"><?= __('Link'); ?> </label>
			<input type="text" class="w1/1 search_input" name="link" value="<?=Format::htmlspecialchars($_POST['link']); ?>">
		</div>
	<?php } ?>
	<div class="field">
		<label class="field__label"><?= __('Testimonial'); ?> <em class="required">*</em></label>
		<textarea class="w1/1 tinymce simple" name="testimonial" rows="5"><?=Format::htmlspecialchars($_POST['testimonial']); ?></textarea>
	</div>
	</div>
	<?php } else { ?>
	<?php if (!empty($testimonials)) { ?>
	<div class="nodes">
    	<ul class="nodes__list">

    		<?php foreach ($testimonials as $testimonial) { ?>
    		<li class="nodes__branch">
    		    <div class="nodes__wrap">
        			<div class="article">

    			        <?=!empty($testimonial['client']) ? '<a class="text text--strong" href="?edit=' . $testimonial['id'] . '">' . Format::htmlspecialchars($testimonial['client']) . '</a>' : '<a class="text text--strong" href="?edit=' . $testimonial['id'] . '">(' . __('None') . ')</a>'; ?>
						<?php if (!empty($can_assign_agent)) { ?>
						<?php if (!empty($testimonial['agent_name'])) { ?>
						<a class="text text--mute text--small" href="<?=URL_BACKEND; ?>agents/agent/summary/?id=<?=$testimonial['agent_id']; ?>">
						(<?=Format::htmlspecialchars($testimonial['agent_name']); ?>)
						</a>
						<?php } else { ?>
						<a class="text text--mute text--small" href="?edit=<?=$testimonial['id']; ?>">(<?= __('None'); ?>)</a>
						<?php } ?>
						<?php } ?>

    					<div class="text text--mute">

    							<?=strip_tags($testimonial['testimonial']); ?>
    						</a>
    					</div>
                    </div>
                    <div class="nodes__actions">
						<a class="btn btn--ghost btn--ico" href="<?=$testimonial['deleteLink']; ?>" onclick="return confirm('<?= __('Are you sure you would like to delete this testimonial?'); ?>');">
							<svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-trash"/></svg>
						</a>
                    </div>
    			</div>
    		</li>
    		<?php } ?>
    	</ul>
	</div>
	<?php } else { ?>
	<p class="block"><?= __('There are currently no testimonials.'); ?></p>
	<?php } ?>
	<?php } ?>

</form>

<?php if (!empty($paginationLinks) && (!$edit_form && !$add_form)) { ?>
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
