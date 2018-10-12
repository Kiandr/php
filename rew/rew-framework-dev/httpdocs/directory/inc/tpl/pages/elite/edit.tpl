<?php

// Require Listing
if (empty($details_entry)) {
	echo '<h1>Directory Error</h1>';
	echo '<div class="msg negative"><p>The selected listing could not be found.</p></div>';
	return;
}

?>
<div id="directory-add" class="rew-directory">

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

		<ul id="view-as" class="uk-list">
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

	<form action="?submit" method="post" enctype="multipart/form-data" class="uk-form uk-form-stacked uk-margin-large-top">
		<div class="uk-grid">
			<div class="uk-width-1-1 uk-margin-bottom" data-uk-margin>

				<header class="uk-width-1-1 uk-margin-bottom">
					<h2>Listing Information</h2>
					<p>Fields left blank will be omitted.</p>
				</header>

				<div class="uk-width-1-1 uk-margin-bottom uk-margin-small-top">
					<label for="business-name" class="uk-form-label"> Business Name <small class="required">*</small></label>
					<input name="business_name" class=".uk-width-* uk-form-large" value="<?=Format::htmlspecialchars($edit_listing['business_name']); ?>" required>
				</div>

				<div class="uk-width-1-1 uk-margin-bottom uk-margin-small-top">
					<label for="business-address" class="uk-form-label"> Street Address </label>
					<input id="business-address" class=".uk-width-* uk-form-large" name="address" value="<?=Format::htmlspecialchars($edit_listing['address']); ?>">
				</div>

				<div class="uk-width-1-1 uk-width-medium-1-2 uk-margin-bottom uk-margin-small-top">
					<label for="business-city" class="uk-form-label"> City </label>
					<input id="business-city" class=".uk-width-* uk-form-large" name="city" value="<?=Format::htmlspecialchars($edit_listing['city']); ?>">
				</div>

				<div class="uk-width-1-1 uk-width-medium-1-2 uk-margin-bottom uk-margin-small-top">
					<label for="business-state" class="uk-form-label"> <?=Locale::spell('State'); ?> </label>
					<input id="business-state" class=".uk-width-* uk-form-large" name="state" value="<?=Format::htmlspecialchars($edit_listing['state']); ?>">
				</div>

				<div class="uk-width-1-1 uk-width-medium-1-2 uk-margin-bottom uk-margin-small-top">
					<label for="business-zipcode" class="uk-form-label"> <?=Locale::spell('Zip Code'); ?> </label>
					<input id="business-zipcode" class=".uk-width-* uk-form-large" name="zip" value="<?=Format::htmlspecialchars($edit_listing['zip']); ?>">
				</div>

				<div class="uk-width-1-1 uk-width-medium-1-2 uk-margin-bottom uk-margin-small-top">
					<label for="business-website" class="uk-form-label"> Website </label>
					<input id="business-website" class=".uk-width-* uk-form-large" type="url" name="website" value="<?=Format::htmlspecialchars($edit_listing['website']); ?>" placeholder="http://www.mywebsite.com/">
				</div>

			</div>

			<div class="uk-width-1-1 uk-margin-bottom" data-uk-margin>

				<header class="uk-width-1-1 uk-margin-bottom">
					<h2>Phone Numbers</h2>
					<p>All numbers must use the format: ###-###-#### (Ext. ###)</p>
				</header>

				<div class="uk-width-1-1 uk-width-medium-1-2 uk-margin-bottom uk-margin-small-top">
					<label for="business-primary-phone" class="uk-form-label"> Primary Phone </label>
					<input id="business-primary-phone" class=".uk-width-* uk-form-large" type="tel" name="phone" value="<?=Format::htmlspecialchars($edit_listing['phone']); ?>" placeholder="123-456-7890">
				</div>

				<div class="uk-width-1-1 uk-width-medium-1-2 uk-margin-bottom uk-margin-small-top">
					<label for="business-secondary-phone" class="uk-form-label"> Secondary Phone </label>
					<input id="business-secondary-phone" class=".uk-width-* uk-form-large" type="tel" name="alt_phone" value="<?=Format::htmlspecialchars($edit_listing['alt_phone']); ?>" placeholder="123-456-7890">
				</div>

				<div class="uuk-width-1-1 uk-width-medium-1-2 uk-margin-bottom uk-margin-small-top">
					<label for="business-tollfree" class="uk-form-label"> Toll Free </label>
					<input id="business-tollfree" class=".uk-width-* uk-form-large" type="tel" name="toll_free" value="<?=Format::htmlspecialchars($edit_listing['toll_free']); ?>" placeholder="123-456-7890">
				</div>

				<div class="uk-width-1-1 uk-width-medium-1-2 uk-margin-bottom uk-margin-small-top">
					<label for="business-fax" class="uk-form-label"> Fax </label>
					<input id="business-fax" class=".uk-width-* uk-form-large" type="tel" name="fax" value="<?=Format::htmlspecialchars($edit_listing['fax']); ?>" placeholder="123-456-7890">
				</div>

			</div>

			<div class="uk-width-1-1 uk-margin-bottom" data-uk-margin>

				<div class="uk-width-small-1-1">
					<header class="uk-width-1-1 uk-margin-bottom">
						<h2> Categories <small class="required">*</small> </h2>
					</header>
					<div class="uk-width-1-1 uk-margin-bottom uk-grid uk-margin-remove uk-margin-small-top">
					<?php

						// Display Categories
						if (!empty($categories)) {
							$_POST['listing_category'] = is_array($_POST['listing_category']) ? $_POST['listing_category'] : array();
							$lists = array_chunk($categories, ceil(count($categories) / 3));
							foreach ($lists as $i => $categories) {
								echo '<ul class="directory-categories uk-width-small-1-2 uk-width-medium-1-3 uk-list">';
								foreach ($categories as $category) {
									echo '<li class="directory-category uk-margin-large-bottom">';
									echo '<label class="uk-form-label"><input' . (in_array($category['link'], $_POST['listing_category']) ? ' checked' : '') . ' type="checkbox" name="listing_category[]" id="cat-' . $category['link'] . '" value="' . $category['link'] . '"> <h3 class="uk-margin-top-remove uk-margin-bottom-remove uk-h4">' . Format::htmlspecialchars($category['title']) . '</h3></label>';
									if (!empty($category['subcategories'])) {
										echo '<ul class="sub-category uk-padding-remove">';
										foreach( $category['subcategories'] as $subcategory) {
											echo '<li class="uk-margin-small-bottom"><label><input' . (in_array($subcategory['link'], $_POST['listing_category']) ? ' checked' : '') . ' type="checkbox" name="listing_category[]" id="cat-' . $subcategory['link'] . '" value="' . $subcategory['link'] . '"> ' . Format::htmlspecialchars($subcategory['title']) . '</label>';
											if (!empty($subcategory['subcategories'])) {
												echo '<ul class="tet-category uk-padding-remove">';
												foreach ($subcategory['subcategories'] as $tetcategory) {
													echo '<li class="uk-margin-small-bottom"><label><input' . (in_array($tetcategory['link'], $_POST['listing_category']) ? ' checked' : '') . ' type="checkbox" name="listing_category[]" id="cat-' . $tetcategory['link'] . '" value="' . $tetcategory['link'] . '"> ' . Format::htmlspecialchars($tetcategory['title']) . '</label></li>';
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

			</div>

			<div class="uk-grid uk-width-1-1 uk-margin-bottom" data-uk-margin="">

				<div class="uk-width-1-1 uk-margin-bottom">
					<label for="business-category-other" class="uk-form-label"> Other Category </label>
					<input id="business-category-other" class=".uk-width-* uk-form-large" name="other_category" value="<?=Format::htmlspecialchars($edit_listing['other_category']); ?>">
					<p class="uk-form-help-block uk-text-muted"> If a category you want is not listed, please suggest a new category </p>
				</div>

				<div class="uk-width-1-1 uk-margin-bottom uk-margin-small-top">
					<label for="listing-description" class="uk-form-label"> Description </label>
					<textarea id="listing-description" class="uk-width-1-1 uk-form-large" name="description" cols="32" rows="6"><?=Format::htmlspecialchars($edit_listing['description']); ?></textarea>
					<p class="uk-form-help-block uk-text-muted"><?=rew_snippet('business-directory-add-listing-formatting'); ?></p>
				</div>

			</div>

			<div class="uk-grid uk-width-1-1 uk-margin-bottom" data-uk-margin="">
				<header class="uk-width-1-1 uk-margin-bottom">
					<h2>Listing's Images &amp; Logo</h2>
					<p>Uploads must be in gif, jpeg, or png format and under 4 MB in size. Drag and drop listing photos to re-arrange.</p>
				</header>

				<div class="uk-width-1-1 uk-margin-bottom uk-margin-small-top">
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

				<div class="uk-width-1-1 uk-margin-bottom uk-margin-small-top">
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

			</div>

			<div class="uk-grid uk-width-1-1 uk-margin-bottom" data-uk-margin>
				<header class="uk-width-1-1 uk-margin-bottom">
					<h2>Contact Information (won't be shown)</h2>
				</header>

				<div class="uk-width-1-1 uk-margin-bottom uk-margin-small-top">
					<label for="business-contact-name" class="uk-form-label"> Name <small class="required">*</small></label>
					<input id="business-contact-name" class=".uk-width-* uk-form-large" name="contact_name" value="<?=Format::htmlspecialchars($edit_listing['contact_name']); ?>" required="">
				</div>

				<div class="uk-width-1-1 uk-margin-bottom uk-margin-small-top">
					<label for="business-contact-phone" class="uk-form-label"> Phone <small class="required">*</small></label>
					<input id="business-contact-phone" class=".uk-width-* uk-form-large" type="tel" name="contact_phone" value="<?=Format::htmlspecialchars($edit_listing['contact_phone']); ?>">
				</div>

				<div class="uk-width-1-1 uk-margin-bottom uk-margin-small-top">
					<label for="business-contact-email" class="uk-form-label"> Email <small class="required">*</small></label>
					<input id="business-contact-email" class=".uk-width-* uk-form-large" type="email" name="contact_email" value="<?=Format::htmlspecialchars($edit_listing['contact_email']); ?>">
				</div>

			</div>

			<div class="uk-width-1-1 uk-margin-bottom uk-form-controls uk-form-controls-text">
				<label>
					<input type="checkbox" name="preview" value="N"<?=($_POST['preview'] == 'Y' ? ' checked' : ''); ?>>
					<strong>Submit Listing for Approval</strong>
				</label>
				<small>(If you don't check this box, the listing will remain in "pending" status. You should check this box unless you are submitting several listings and wish to review them all before sending.)</small>
			</div>

			<div class="uk-width-1-1">
				<button type="submit" class="uk-button uk-button-medium" role="button"> Save Changes </button>
			</div>
		</div>
	</form>
	<?php } ?>
</div>