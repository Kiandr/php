<div id="directory-add" class="rew-directory">

	<h1>Add Listing</h1>

	<?=rew_snippet('business-directory-add-listing-page'); ?>

	<?php if (!empty($errors)) { ?>
		<div class="msg negative">
			<ul><li><?=implode('</li><li>', $errors); ?></li></ul>
		</div>
	<?php } ?>

	<?php if (!empty($success)) { ?>
		<div class="msg positive">
			<p><?=$success; ?></p>
		</div>
	<?php } ?>

	<form action="?submit" method="post" enctype="multipart/form-data">

		<fieldset>

			<h4>Listing Information</h4>
			<p>Fields left blank will be omitted.</p>

			<div class="field x12">
				<label>Business Name <small class="required">*</small></label>
				<input name="business_name" value="<?=Format::htmlspecialchars($_POST['business_name']); ?>" required>
			</div>

			<div class="field x12">
				<label>Street Address</label>
				<input name="address" value="<?=Format::htmlspecialchars($_POST['address']); ?>">
			</div>

			<div class="field x6">
				<label>City</label>
				<input name="city" value="<?=Format::htmlspecialchars($_POST['city']); ?>">
			</div>

			<div class="field x6 last">
				<label><?=Locale::spell('State'); ?></label>
				<input name="state" value="<?=Format::htmlspecialchars($_POST['state']); ?>">
			</div>

			<div class="field x6">
				<label><?=Locale::spell('Zip Code'); ?></label>
				<input name="zip" value="<?=Format::htmlspecialchars($_POST['zip']); ?>">
			</div>

			<div class="field x6 last">
				<label>Website</label>
				<input type="url" name="website" value="<?=Format::htmlspecialchars($_POST['website']); ?>" placeholder="http://www.mywebsite.com/">
				</div>

			</fieldset>

			<fieldset>

				<h4>Phone Numbers</h4>
				<p>All numbers must use the format: ###-###-#### (Ext. ###)</p>

				<div class="field x6">
					<label>Primary Phone</label>
					<input type="tel" name="phone" value="<?=Format::htmlspecialchars($_POST['phone']); ?>">
				</div>

			<div class="field x6 last">
				<label>Secondary Phone</label>
				<input type="tel" name="alt_phone" value="<?=Format::htmlspecialchars($_POST['alt_phone']); ?>">
			</div>

			<div class="field x6">
				<label>Toll Free</label>
				<input type="tel" name="toll_free" value="<?=Format::htmlspecialchars($_POST['toll_free']); ?>">
			</div>

			<div class="field x6 last">
				<label>Fax</label>
				<input type="tel" name="fax" value="<?=Format::htmlspecialchars($_POST['fax']); ?>">
			</div>

		</fieldset>

		<fieldset>

			<div class="field x12">
				<label>Categories <small class="required">*</small></label>
				<div class="toggleset">
				<?php

					// Display Categories
					if (!empty($categories)) {
						$_POST['listing_category'] = is_array($_POST['listing_category']) ? $_POST['listing_category'] : array();
						$lists = array_chunk($categories, ceil(count($categories) / 3));
						foreach ($lists as $i => $categories) {
							echo '<ul class="directory-categories' . ($i == count($lists) - 1 ? ' last' : '') . '" style="float: left">';
							foreach ($categories as $category) {
								echo '<li class="directory-category">';
								echo '<label><input' . (in_array($category['link'], $_POST['listing_category']) ? ' checked' : '') . ' type="checkbox" name="listing_category[]" id="cat-' . $category['link'] . '" value="' . $category['link'] . '"> ' . Format::htmlspecialchars($category['title']) . '</label>';
								if (!empty($category['subcategories'])) {
									echo '<ul class="sub-category">';
									foreach( $category['subcategories'] as $subcategory) {
										echo '<li><label><input' . (in_array($subcategory['link'], $_POST['listing_category']) ? ' checked' : '') . ' type="checkbox" name="listing_category[]" id="cat-' . $subcategory['link'] . '" value="' . $subcategory['link'] . '"> ' . Format::htmlspecialchars($subcategory['title']) . '</label>';
										if (!empty($subcategory['subcategories'])) {
											echo '<ul class="tet-category">';
											foreach ($subcategory['subcategories'] as $tetcategory) {
												echo '<li><label><input' . (in_array($tetcategory['link'], $_POST['listing_category']) ? ' checked' : '') . ' type="checkbox" name="listing_category[]" id="cat-' . $tetcategory['link'] . '" value="' . $tetcategory['link'] . '"> ' . Format::htmlspecialchars($tetcategory['title']) . '</label></li>';
											}
											echo '</ul>';
										}
										echo '</li>';
									}
									echo '</ul>';
								}
								echo '</li>';
							}
							echo '</ul>';
						}
					}

				?>
				</div>
			</div>

		</fieldset>

		<fieldset>

			<div class="field x12">
				<label>Other Category</label>
				<input name="other_category" value="<?=Format::htmlspecialchars($_POST['other_category']); ?>">
				<small>If a category you want is not listed, please suggest a new category</small>
			</div>

			<div class="field x12">
				<label>Description</label>
				<textarea name="description" cols="40" rows="10"><?=Format::htmlspecialchars($_POST['description']); ?></textarea>
				<small><?=rew_snippet('business-directory-add-listing-formatting'); ?></small>
			</div>

		</fieldset>

		<fieldset>
			<h4>Listing's Images &amp; Logo</h4>

			<div class="field x12">
				<p>Uploads must be in gif, jpeg, or png format and under 4 MB in size. Drag and drop listing photos to re-arrange.</p>
			</div>

			<div class="field x12">
				<label>Logo</label>
				<small>You should submit a logo or headshot, or some other image meant to appear small, here. This image will appear in the category pages and search results, as well as on the listing details page.</small>
				<div id="logo-uploader">
					<?php if (!empty($logo_uploads)) { ?>
						<div class="file-manager">
							<ul>
								<?php foreach ($logo_uploads as $upload) { ?>
									<li>
										<div class="wrap">
											<img src="/thumbs/95x95/uploads/<?=$upload['file']; ?>" border="0">
											<input type="hidden" name="uploads[]" value="<?=$upload['id']; ?>">
										</div>
									</li>
								<?php } ?>
							</ul>
						</div>
					<?php } ?>
				</div>
			</div>

			<div class="field x12">
				<label>Images</label>
				<small>You can submit up to 3 images for your listing (storefront, staff, product, menu, whatever). It is recommended that they be in a medium-to-large format (270px x 200px+ ). All of these images will show at the bottom of your listing's detail page, in the order shown below.</small>
				<div id="uploader">
					<?php if (!empty($uploads)) { ?>
						<div class="file-manager">
							<ul>
								<?php foreach ($uploads as $upload) { ?>
									<li>
										<div class="wrap">
											<img src="/thumbs/95x95/uploads/<?=$upload['file']; ?>" border="0">
											<input type="hidden" name="uploads[]" value="<?=$upload['id']; ?>">
										</div>
									</li>
								<?php } ?>
							</ul>
						</div>
					<?php } ?>
				</div>
			</div>

		</fieldset>

		<fieldset>
			<h4>Contact Information (won't be shown)</h4>

			<div class="field x12">
				<label>Name <small class="required">*</small></label>
				<input name="contact_name" value="<?=Format::htmlspecialchars($_POST['contact_name']); ?>" required>
			</div>

			<div class="field x12">
				<label>Phone <small class="required">*</small></label>
				<input type="tel" name="contact_phone" value="<?=Format::htmlspecialchars($_POST['contact_phone']); ?>">
			</div>

			<div class="field x12">
				<label>Email <small class="required">*</small></label>
				<input type="email" name="contact_email" value="<?=Format::htmlspecialchars($_POST['contact_email']); ?>">
			</div>

		</fieldset>

		<div class="field x12">
			<label class="toggle">
				<input type="checkbox" name="preview" value="Y"<?=($_POST['preview'] == 'Y' ? ' checked' : ''); ?>>
				<strong>Preview Listing Before Submission</strong>
			</label>
		</div>

		<div class="btnset">
			<button type="submit" class="strong">Submit Form</button>
		</div>

	</form>

	<?php ob_start(); ?>
	/* <script> */

		// File Uploader
		$('#uploader').rew_uploader({
			'extraParams' : {
				'type' : 'directory'
			},
			'url_upload' : '/directory/inc/php/ajax/upload.php?upload',
			'url_delete' : '/directory/inc/php/ajax/upload.php?delete',
			'url_sort'   : '/directory/inc/php/ajax/upload.php?sort'
		});

		// Logo Uploader
		$('#logo-uploader').rew_uploader({
			'multiple' : false,
			'extraParams' : {
				'type' : 'directory_logo'
			},
			'url_upload' : '/directory/inc/php/ajax/upload.php?upload',
			'url_delete' : '/directory/inc/php/ajax/upload.php?delete',
			'url_sort'   : '/directory/inc/php/ajax/upload.php?sort'
		});

	/* </script> */
	<?php $page->writeJS(ob_get_clean()); ?>

</div>