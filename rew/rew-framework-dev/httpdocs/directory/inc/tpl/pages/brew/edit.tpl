<?php

// Require Listing
if (empty($details_entry)) {
	echo '<h1>Directory Error</h1>';
	echo '<div class="msg negative"><p>The selected listing could not be found.</p></div>';
	return;
}

?>
<div id="directory-edit" class="rew-directory">

	<h1>Edit Listing</h1>

	<?php if (!empty($errors)) { ?>
		<div class="msg negative">
			<ul><li><?=implode('</li><li>', $errors); ?></li></ul>
		</div>
	<?php } ?>

	<?php if (!empty($success)) { ?>

		<div class="msg positive">
			<p><?=$success; ?></p>
		</div>

	<?php } else { ?>

		<ul id="view-as">
			<li><a href="javascript:void(0);" data-panel="#preview-result">Show Result View</a></li>
			<li><a href="javascript:void(0);" data-panel="#preview-details">Show Details View</a></li>
		</ul>

		<div id="preview-result" class="hidden">
			<?php

				// Include Result
				$entry = $result_entry;
				echo '<div class="articleset">';
				include $page->locateTemplate('directory', 'misc' ,'result');
				echo '</div>';

			?>
		</div>

		<div id="preview-details" class="hidden">
			<?php

				// Include Details
				$entry = $details_entry;
				include $page->locateTemplate('directory', 'misc' ,'details');

			?>
		</div>

		<form action="?submit" method="post" enctype="multipart/form-data">

			<fieldset>
				<h4>Listing Information</h4>

				<div class="field x12">
					<label>Business Name <small class="required">*</small></label>
					<input name="business_name" value="<?=Format::htmlspecialchars($edit_listing['business_name']); ?>" required>
				</div>

				<div class="field x12">
					<label>Street Address</label>
					<input name="address" value="<?=Format::htmlspecialchars($edit_listing['address']); ?>">
				</div>

			</fieldset>

			<fieldset>

				<div class="field x6">
					<label>City</label>
					<input name="city" value="<?=Format::htmlspecialchars($edit_listing['city']); ?>">
				</div>

				<div class="field x6 last">
					<label><?=Locale::spell('State'); ?></label>
					<input name="state" value="<?=Format::htmlspecialchars($edit_listing['state']); ?>">
				</div>

				<div class="field x6">
					<label><?=Locale::spell('Zip Code'); ?></label>
					<input name="zip" value="<?=Format::htmlspecialchars($edit_listing['zip']); ?>">
				</div>

				<div class="field x6 last">
					<label>Website</label>
					<input type="url" name="website" value="<?=Format::htmlspecialchars($edit_listing['website']); ?>" placeholder="http://www.mywebsite.com/">
				</div>

				<div class="field x12">
					<label>Phone Numbers</label>
					<small>All phone numbers must use the following format: ###-###-#### (Ext. ###).</small>
				</div>

				<div class="field x6">
					<label>Primary Phone</label>
					<input type="tel" name="phone" value="<?=Format::htmlspecialchars($edit_listing['phone']); ?>">
				</div>

				<div class="field x6 last">
					<label>Secondary Phone</label>
					<input type="tel" name="alt_phone" value="<?=Format::htmlspecialchars($edit_listing['alt_phone']); ?>">
				</div>

				<div class="field x6">
					<label>Toll Free</label>
					<input type="tel" name="toll_free" value="<?=Format::htmlspecialchars($edit_listing['toll_free']); ?>">
				</div>

				<div class="field x6 last">
					<label>Fax</label>
					<input type="tel" name="fax" value="<?=Format::htmlspecialchars($edit_listing['fax']); ?>">
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

				<div class="field x12">
					<label>Other Category</label>
					<input name="other_category" value="<?=Format::htmlspecialchars($edit_listing['other_category']); ?>">
					<small>If a category you want is not listed, please suggest a new category</small>
				</div>

				<div class="field x12">
					<label>Description</label>
					<textarea name="description" cols="40" rows="5"><?=Format::htmlspecialchars($edit_listing['description']); ?></textarea>
				</div>

			</fieldset>

			<fieldset>

				<h4>Listings Images &amp; Logo</h4>
				<p>Uploads must be in gif, jpeg, or png format and under 4 MB in size. Drag and drop listing photos to re-arrange.</p>

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
					<input name="contact_name" value="<?=$edit_listing['contact_name']; ?>" required>
				</div>

				<div class="field x12">
					<label>Phone <small class="required">*</small></label>
					<input type="tel" name="contact_phone" value="<?=$edit_listing['contact_phone']; ?>">
				</div>

				<div class="field x12">
					<label>Email <small class="required">*</small></label>
					<input type="email" name="contact_email" value="<?=$edit_listing['contact_email']; ?>">
				</div>

			</fieldset>

			<div class="field x12">
				<label class="toggle">
					<input type="checkbox" name="preview" value="N"<?=($_POST['preview'] == 'Y' ? ' checked' : ''); ?>>
					<strong>Submit Listing for Approval</strong>
				</label>
				 <small>(If you don't check this box, the listing will remain in "pending" status. You should check this box unless you are submitting several listings and wish to review them all before sending.)</small>
			</div>

			<div class="btnset">
				<button type="submit" class="strong">Save Changes</button>
			</div>

		</form>

		<?php ob_start(); ?>
		/* <script> */

			// File Uploader
			$('#uploader').rew_uploader({
				'extraParams' : {
					'type' : 'directory',
					'row' : '<?=$edit_listing['id']; ?>'
				},
				'url_upload' : '/directory/inc/php/ajax/upload.php?upload',
				'url_delete' : '/directory/inc/php/ajax/upload.php?delete',
				'url_sort'   : '/directory/inc/php/ajax/upload.php?sort'
			});

			// Logo Uploader
			$('#logo-uploader').rew_uploader({
				'multiple' : false,
				'extraParams' : {
					'logo' : 'true',
					'type' : 'directory_logo',
					'row' : '<?=$edit_listing['id']; ?>'
				},
				'url_upload' : '/directory/inc/php/ajax/upload.php?upload',
				'url_delete' : '/directory/inc/php/ajax/upload.php?delete',
				'url_sort'   : '/directory/inc/php/ajax/upload.php?sort'
			});

			// Toggle Previews
			var $toggle = $('#view-as').on('click', 'a', function () {
				var $this = $(this), panel = $this.data('panel'), $panel = $(panel), text = $this.text();
				if ($panel.length > 0) {
					if ($panel.hasClass('hidden')) {
						$this.text(text.replace('Show', 'Hide'));
						$panel.hide().removeClass('hidden').slideDown();
						$toggle.find('a[data-panel]').not($this).each(function () {
							var $this = $(this), panel = $this.data('panel'), $panel = $(panel), text = $this.text();
							$this.text(text.replace('Hide', 'Show'));
							$panel.slideUp(function () {
								$panel.addClass('hidden');
							});
						});
					} else {
						$this.text(text.replace('Hide', 'Show'));
						$panel.slideUp(function () {
							$panel.addClass('hidden');
						});
					}
				}
			});

		/* </script> */
		<?php $page->writeJS(ob_get_clean()); ?>

	<?php } ?>

</div>