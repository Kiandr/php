<form action="?submit" method="post" enctype="multipart/form-data"<?=(!empty($edit_row) && !empty($show_form)) ? ' class="rew_check"' : ''; ?>>

	<div class="bar">

		<div class="bar__title"><?= __('Slideshow Manager'); ?></div>

		<?php if (!empty($edit_row)) : ?>

		<div class="bar__actions">
			<a class="bar__action" href="?"><svg class="icon icon-left-a mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
		</div>

		<div class="btns btns--stickyB">
			<span class="R">
				<button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?></button>
			</span>
		</div>

		<?php elseif (!empty($show_form)) : ?>

		<div class="bar__actions">
			<a class="bar__action" href="?"><svg class="icon icon-left-a mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
		</div>

		<div class="btns btns--stickyB">
			<span class="R">
				<button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Upload'); ?></button>
			</span>
		</div>

		<?php else : ?>
		<div class="bar__actions">
			<a class="bar__action" href="?add"><svg class="icon icon-add mar0"><use xlink:href="/backend/img/icos.svg#icon-add"/></svg></a>
		</div>
		<?php endif; ?>

	</div>


	<?php if (!empty($edit_row)) : ?>
	<?php if (!empty($show_form)) : ?>
	<div class="block">
	<input type="hidden" name="edit" value="<?=$edit_row['id']; ?>">
	<div class="field">
		<label class="field__label"><?= __('Slideshow Image'); ?> <em class="required">*</em></label>
		<input class="w1/1" type="file" name="image" value="">
		<p class="text--mute"><?= __('Select the image file that you would like to upload.'); ?></p>
	</div>
	<?php if (!empty($show_caption)) { ?>
	<div class="field">
		<label class="field__label"><?= __('Caption'); ?></label>
		<input type="text" name="caption" value="<?=htmlspecialchars($edit_row['caption']); ?>">
		<p class="text--mute"><?= __('Optional field. Include text to display with your slideshow image.'); ?></p>
	</div>
	<?php } ?>
	<div class="field">
		<label class="field__label"><?= __('Link URL'); ?></label>
		<input class="w1/1" type="text" name="link" value="<?=htmlspecialchars($edit_row['link']); ?>">
		<p class="text--mute"><?= __('Optional field. Allows you to add a link to your slideshow image to click to.'); ?></p>
	</div>
	<div class="field">
		<div class="photo full">
			<?php if (!empty($edit_row['link'])) : ?>
			<a href="<?=$edit_row['link']; ?>" target="_blank"><img src="<?=URL_SLIDESHOW_IMAGES . $edit_row['image']; ?>" border="0"></a>
			<?php else : ?>
			<img src="<?=URL_SLIDESHOW_IMAGES . $edit_row['image']; ?>" border="0">
			<?php endif; ?>
		</div>
	</div>
	</div>
	<?php endif; ?>
	<?php else : ?>
	<?php if (!empty($show_form)) : ?>
	<div class="block">
	<input type="hidden" name="add" value="true">
	<div class="field">
		<label class="field__label"><?= __('Slideshow Image'); ?> <em class="required">*</em></label>
		<input type="file" name="image" value="" required>
		<p class="text--mute"><?= __('Select the image file that you would like to upload.'); ?></p>
	</div>
	<?php if (!empty($show_caption)) { ?>
	<div class="field">
		<label class="field__label"><?= __('Caption'); ?></label>
		<input type="text" name="caption" value="<?=htmlspecialchars($_POST['caption']); ?>">
		<p class="text--mute"><?= __('Optional field. Include text to display with your slideshow image.'); ?></p>
	</div>
	<?php } ?>
	<div class="field">
		<label class="field__label"><?= __('Link URL'); ?></label>
		<input class="w1/1" type="text" name="link" value="<?=htmlspecialchars($_POST['link']); ?>">
		<p class="text--mute"><?= __('Optional field. Allows you to add a link to your slideshow image to click to.'); ?></p>
	</div>
	</div>
	<?php endif; ?>
	<?php endif; ?>
	<?php if (empty($show_form)) : ?>
	<?php if (!empty($slideshow_images)) : ?>
	<div class="block">
	<div class="file-manager file-manager--slideshow">
		<ul id="slideshow_images">
			<?php foreach ($slideshow_images as $index => $slideshow_image) : ?>
			<?php $image = '/thumbs/400x300/uploads/slideshow/' . $slideshow_image['image']; ?>
			<li class="slideshow_image" id="images-<?=$slideshow_image['id']; ?>">
				<div class="wrap">
                    <img src="<?=$image; ?>" border="0">
					<div class="actions">
                        <a class="btn edit" href="?edit=<?=$slideshow_image['id']; ?>" title="<?= __('Edit this image'); ?>">
                            <svg class="icon icon--invert mar0">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-pencil"></use>
                            </svg>
                        </a>
                        <a class="btn delete" href="?delete=<?=$slideshow_image['id']; ?>" title="<?= __('Delete this image'); ?>" onclick="return confirm('<?= __('Are you sure you want to delete this slideshow image?'); ?>');">
                            <svg class="icon icon--invert icon-trash mar0">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use>
                            </svg>
                        </a>
                    </div>
				</div>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>
	</div>
	<?php else : ?>
	<div class="block">
	    <p class="block"><?= __('You currently have no slideshow images uploaded.'); ?></p>
	</div>
	<?php endif; ?>
	<?php endif; ?>
</form>
