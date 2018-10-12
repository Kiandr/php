<?php

// Require Listing
if (empty($details_entry)) {
    echo '<h1>Directory Error</h1>';
    echo '<div class="msg negative"><p>The selected listing could not be found.</p></div>';
    return;
}

?>
<div id="directory-edit" data-id="<?=$edit_listing['id']; ?>" class="rew-directory">

    <h1>Edit Listing</h1>

    <?php if (!empty($errors)) { ?>
        <div class="msg msg--neg marT-md">
            <ul><li><?=implode('</li><li>', $errors); ?></li></ul>
        </div>
    <?php } ?>

    <?php if (!empty($success)) { ?>

        <div class="msg msg--pos marT-md">
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
                <h3>Listing Information</h3>
                <div class="cols">
                    <div class="col fld w1/1">
                        <label>Business Name <small class="required">*</small></label>
                        <input name="business_name" value="<?=Format::htmlspecialchars($edit_listing['business_name']); ?>" required>
                    </div>
                    <div class="col fld w1/1">
                        <label>Street Address</label>
                        <input name="address" value="<?=Format::htmlspecialchars($edit_listing['address']); ?>">
                    </div>
                    <div class="col fld w1/2 w1/2-sm">
                        <label>City</label>
                        <input name="city" value="<?=Format::htmlspecialchars($edit_listing['city']); ?>">
                    </div>
                    <div class="col fld w1/2 w1/2-sm">
                        <label><?=Locale::spell('State'); ?></label>
                        <input name="state" value="<?=Format::htmlspecialchars($edit_listing['state']); ?>">
                    </div>
                    <div class="col fld w1/2 w1/2-sm">
                        <label><?=Locale::spell('Zip Code'); ?></label>
                        <input name="zip" value="<?=Format::htmlspecialchars($edit_listing['zip']); ?>">
                    </div>
                    <div class="col fld w1/2 w1/2-sm">
                        <label>Website</label>
                        <input type="url" name="website" value="<?=Format::htmlspecialchars($edit_listing['website']); ?>" placeholder="http://www.mywebsite.com/">
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <h3>Phone Numbers</h3>
                <p>All phone numbers must use the following format: ###-###-#### (Ext. ###).</p>
                <div class="cols">
                    <div class="col fld w1/2 w1/2-sm">
                        <label>Primary Phone</label>
                        <input type="tel" name="phone" value="<?=Format::htmlspecialchars($edit_listing['phone']); ?>">
                    </div>
                    <div class="col fld w1/2 w1/2-sm">
                        <label>Secondary Phone</label>
                        <input type="tel" name="alt_phone" value="<?=Format::htmlspecialchars($edit_listing['alt_phone']); ?>">
                    </div>
                    <div class="col fld w1/2 w1/2-sm">
                        <label>Toll Free</label>
                        <input type="tel" name="toll_free" value="<?=Format::htmlspecialchars($edit_listing['toll_free']); ?>">
                    </div>
                    <div class="col fld w1/2 w1/2-sm">
                        <label>Fax</label>
                        <input type="tel" name="fax" value="<?=Format::htmlspecialchars($edit_listing['fax']); ?>">
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <div class="cols">
                    <div class="col fld w1/1">
                        <h3>Categories <small class="required">*</small></h3>
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
                </div>
            </fieldset>

            <fieldset>
                <div class="cols">
                    <div class="col fld w1/1">
                        <label>Other Category</label>
                        <input name="other_category" value="<?=Format::htmlspecialchars($edit_listing['other_category']); ?>">
                        <small>If a category you want is not listed, please suggest a new category</small>
                    </div>
                    <div class="col fld w1/1">
                        <label>Description</label>
                        <textarea name="description" cols="40" rows="5"><?=Format::htmlspecialchars($edit_listing['description']); ?></textarea>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <h3>Logo &amp; Images</h3>
                <p>Uploads must be in gif, jpeg, or png format and under 4 MB in size. Drag and drop listing photos to re-arrange.</p>
                <div class="col fld w1/1">
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
                <div class="col fld w1/1">
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
                <h3>Contact Information (won't be shown)</h3>
                <div class="cols">
                    <div class="col fld w1/1">
                        <label>Name <small class="required">*</small></label>
                        <input name="contact_name" value="<?=$edit_listing['contact_name']; ?>" required>
                    </div>
                    <div class="col fld w1/1">
                        <label>Phone <small class="required">*</small></label>
                        <input type="tel" name="contact_phone" value="<?=$edit_listing['contact_phone']; ?>">
                    </div>
                    <div class="col fld w1/1">
                        <label>Email <small class="required">*</small></label>
                        <input type="email" name="contact_email" value="<?=$edit_listing['contact_email']; ?>">
                    </div>
                    <div class="col fld w1/1">
                        <label class="toggle">
                            <input type="checkbox" name="preview" value="N"<?=($_POST['preview'] == 'Y' ? ' checked' : ''); ?>>
                            Submit Listing for Approval
                        </label>
                        <small>(If you don't check this box, the listing will remain in "pending" status. You should check this box unless you are submitting several listings and wish to review them all before sending.)</small>
                    </div>
                </div>
            </fieldset>

            <div class="btns">
                <button type="submit" class="btn btn--primary">Save Changes</button>
            </div>

        </form>
        <?php

            // Include skin's javascript file for this page
            $page->addJavascript('js/directory/edit.js', 'page');

        ?>
    <?php } ?>
</div>