<?php

// Edit Featured
if (!empty($edit_row)) {

?>

<div class="block">

	<form action="?submit" method="post" enctype="multipart/form-data" class="rew_check">

		<input type="hidden" name="edit" value="<?=$edit_row['id']; ?>">

			<h2><a href="<?=$edit_row['listing']['url_details']; ?>" target="_blank"><?=$edit_row['listing']['Address']; ?> (MLS&reg; #<?=Format::htmlspecialchars($edit_row['listing']['ListingMLS']); ?>)</a></h2>

			<div class="btns btns--stickyB">
				<span class="R">
					<button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg></button>
				</span>
			</div>

			<p>Changing this information will only change the display of the featured listings module, it does not alter the information on the details page.</p>

			<label>City </label>
			<input class="search_input" name="featured_city" value="<?=htmlspecialchars($edit_row['city']); ?>">

			<label>Price</label>
			<input class="search_input" name="featured_price" value="<?=htmlspecialchars($edit_row['price']); ?>">

			<label>Bedrooms</label>
			<input class="search_input" name="featured_bedrooms" value="<?=htmlspecialchars($edit_row['bedrooms']); ?>">

			<label>Bathrooms</label>
			<input class="search_input" name="featured_bathrooms" value="<?=htmlspecialchars($edit_row['bathrooms']); ?>">

			<label>Remarks</label>
			<textarea class="search_input" name="featured_remarks" rows="6"><?=htmlspecialchars($edit_row['remarks']); ?></textarea>


			<label>Image</label>
			<input type="file" class="search_input" name="featured_image" value="">
			<p class="tip">If this listing has no image associated with it in the feed, or if you would like to use a different image than the one provided, you can use this feature to upload your own photo.</p>

		<?php if (!empty($edit_row['image'])) { ?>
			<label><input class="checkbox" type="checkbox" name="delete_image" value="true"> Delete Image</label>
			<img src="<?=URL_FEATURED_IMAGES . $edit_row['image']; ?>" width="100">
			<input type="hidden" name="featured_image" value="<?=$edit_row['image']; ?>">
		<?php } ?>

	</form>

</div>

<?php

// Manage Featured
} else {

?>

	<form action="?submit" method="post" class="rew_check">

		<input type="hidden" name="add" value="true">

            <div class="bar">
                <div class="bar__title">Featured Listings</div>
            </div>

			<div class="btns btns--stickyB">
				<span class="R">
					<button type="submit" class="btn btn--positive"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> Save</button>
				</span>
			</div>

            <div class="block">
                <div class="cols">
                <div class="field col w1/4">
                    <label class="field__label">Pick a Feed:</label>
                    <select class="w1/1" name="feed">
                        <option value="">-- Choose an IDX Feed --</option>
                    <?php
                        // Multi-IDX
                        foreach ($feeds as $feed => $settings) {
                            echo '<option ' . (Settings::getInstance()->IDX_FEED === $feed ? 'selected' : '') . ' value="' . $feed . '">' . $settings['title'] . '</option>';
                        }
                    ?>
                    </select>
                </div>
                <div class="field col w3/4">
    				<label class="field__label">Find a Listing:</label>
    				<input class="w1/1 autocomplete listing" name="featured_mls" value="<?=htmlspecialchars($_POST['featured_mls']); ?>" placeholder="Enter MLS&reg; Number or Street Address" required>
                </div>
			<?php if (!empty(Settings::getInstance()->MODULES['REW_FEATURED_LISTINGS_OVERRIDE'])) { ?>
				<p>Once added, you may customize the display information for your featured listings. Otherwise, the information will pull automatically from the IDX.</p>
			<?php } ?>
                </div>
            </div>

				<?php

					// No Featured Listings
					if (empty($featured_listings)) {
						echo '<p class="block">There are currently no featured listings.</p>';

					} else {

				?>

                <div class="nodes">
				<ul class="nodes__list">

				<?php foreach ($featured_listings as $listing) { ?>

					<?php if (!empty($listing['ListingMLS'])) { ?>

					<li class="nodes__branch">
                        <div class="nodes__wrap">
							<div class="article">
                                <div class="article__body">
                                    <div class="article__thumb thumb thumb--medium">
										<?php if(!empty($listing['ListingImage'])) { ?>
										<img src="<?=Format::thumbUrl($listing['ListingImage'], '24x24'); ?>" alt="">
										<?php } else { ?>
										<img src="/thumbs/60x60/uploads/listings/na.png" alt="">
										<?php } ?>
                                    </div>
                                    <div class="article__content">
                                        <?php if (!empty(Settings::getInstance()->MODULES['REW_FEATURED_LISTINGS_OVERRIDE'])) { ?>
    									<a class="text text--strong" href="<?=$listing['url_details']; ?>">
    									<?php } ?>
    										<?=Format::htmlspecialchars($listing['Address']); ?></a>
    									<?php if (!empty(Settings::getInstance()->MODULES['REW_FEATURED_LISTINGS_OVERRIDE'])) { ?>
    									</a>
    									<?php } ?>
    									<div class="text text--mute">$<?=Format::number($listing['ListingPrice']); ?></div>
                                    </div>
                                </div>
                        	</div>
                        	<div class="nodes__actions">
								<a class="btn btn--ghost btn--ico delete" href="?delete=<?=$listing['id']; ?>" onclick="return confirm('Are you sure you want to delete this listing?');"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-trash"/></svg></a>
							</div>
                        </div>
					</li>

			<?php } else { ?>

				<li class="nodes__branch">
                    <div class="nodes__wrap">
						<div class="article">

                            <div class="article__body">
                                <div class="article__thumb thumb thumb--medium">
									<?php if(!empty($listing['ListingImage'])) { ?>
									<img src="<?=Format::thumbUrl($listing['ListingImage'], '24x24'); ?>" alt="">
									<?php } else { ?>
									<img src="/thumbs/24x24/uploads/listings/na.png" alt="">
									<?php } ?>
                                </div>
                                <div class="article__content">
                                    <div class="text text--strong">MLS# <?=Format::htmlspecialchars($listing['mls_number']); ?></div>
                                    <em>This listing is no longer Available</em>
                                </div>
                            </div>

                        </div>

                    	<div class="nodes__actions">
						    <a class="btn btn--ghost btn--ico delete" href="?delete=<?=$listing['id']; ?>" onclick="return confirm('Are you sure you want to delete this listing?');"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-trash"/></svg></a>
						</div>

                    </div>
				</li>

				<?php } ?>
			<?php } ?>
            </ul>
            </div>
		<?php } ?>
	</form>

<?php

}

?>