<div id="directory-add" class="rew-directory">

    <h1>Add Listing</h1>

    <?=rew_snippet('business-directory-add-listing-page'); ?>

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

        <form action="?submit" method="post" enctype="multipart/form-data">
            <fieldset>
                <h3>Listing Information</h3>
                <p>Fields left blank will be omitted.</p>
                <div class="cols">
                    <div class="col fld w1/1">
                        <label>Business Name <small class="required">*</small></label>
                        <input name="business_name" value="<?=Format::htmlspecialchars($_POST['business_name']); ?>" required>
                    </div>
                    <div class="col fld w1/1">
                        <label>Street Address</label>
                        <input name="address" value="<?=Format::htmlspecialchars($_POST['address']); ?>">
                    </div>
                    <div class="col fld w1/2 w1/2-sm">
                        <label>City</label>
                        <input name="city" value="<?=Format::htmlspecialchars($_POST['city']); ?>">
                    </div>
                    <div class="col fld w1/2 w1/2-sm">
                        <label><?=Locale::spell('State'); ?></label>
                        <input name="state" value="<?=Format::htmlspecialchars($_POST['state']); ?>">
                    </div>
                    <div class="col fld w1/2 w1/2-sm">
                        <label><?=Locale::spell('Zip Code'); ?></label>
                        <input name="zip" value="<?=Format::htmlspecialchars($_POST['zip']); ?>">
                    </div>
                    <div class="col fld w1/2 w1/2-sm">
                        <label>Website</label>
                        <input type="url" name="website" value="<?=Format::htmlspecialchars($_POST['website']); ?>" placeholder="http://www.mywebsite.com/">
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <h3>Phone Numbers</h3>
                <p>All numbers must use the format: ###-###-#### (Ext. ###)</p>
                <div class="cols">
                    <div class="col fld w1/2 w1/2-sm">
                        <label>Primary Phone</label>
                        <input type="tel" name="phone" value="<?=Format::htmlspecialchars($_POST['phone']); ?>">
                    </div>

                    <div class="col fld w1/2 w1/2-sm">
                        <label>Secondary Phone</label>
                        <input type="tel" name="alt_phone" value="<?=Format::htmlspecialchars($_POST['alt_phone']); ?>">
                    </div>

                    <div class="col fld w1/2 w1/2-sm">
                        <label>Toll Free</label>
                        <input type="tel" name="toll_free" value="<?=Format::htmlspecialchars($_POST['toll_free']); ?>">
                    </div>

                    <div class="col fld w1/2 w1/2-sm">
                        <label>Fax</label>
                        <input type="tel" name="fax" value="<?=Format::htmlspecialchars($_POST['fax']); ?>">
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <div class="cols">
                    <div class="col fld w1/1">
                    <h3>Categories <small class="required">*</small></h3>
                    <div class="toggleset cols">
                        <?php

                            // Display Categories
                            if (!empty($categories)) {
                                $_POST['listing_category'] = is_array($_POST['listing_category']) ? $_POST['listing_category'] : array();
                                $lists = array_chunk($categories, ceil(count($categories) / 3));
                                foreach ($lists as $i => $categories) {
                                    echo '<ul class="directory-categories col w1/3 w1/2-md w1/1-sm' . ($i == count($lists) - 1 ? ' last' : '') . '" style="float: left">';
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
                        <input name="other_category" value="<?=Format::htmlspecialchars($_POST['other_category']); ?>">
                        <small>If a category you want is not listed, please suggest a new category</small>
                    </div>
                    <div class="col fld w1/1">
                        <label>Description</label>
                        <textarea name="description" cols="40" rows="10"><?=Format::htmlspecialchars($_POST['description']); ?></textarea>
                        <small><?=rew_snippet('business-directory-add-listing-formatting'); ?></small>
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <h3>Logo &amp; Images</h3>
                <p>Uploads must be in gif, jpeg, or png format and under 4 MB in size. Drag and drop listing photos to re-arrange.</p>
                <div class="cols">
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
                </div>
            </fieldset>

            <fieldset>
                <h3>Contact Information (kept private)</h3>
                <div class="cols">
                    <div class="col fld w1/1">
                        <label>Name <small class="required">*</small></label>
                        <input name="contact_name" value="<?=Format::htmlspecialchars($_POST['contact_name']); ?>" required>
                    </div>
                    <div class="col fld w1/1">
                        <label>Phone <small class="required">*</small></label>
                        <input type="tel" name="contact_phone" value="<?=Format::htmlspecialchars($_POST['contact_phone']); ?>">
                    </div>
                    <div class="col fld w1/1">
                        <label>Email <small class="required">*</small></label>
                        <input type="email" name="contact_email" value="<?=Format::htmlspecialchars($_POST['contact_email']); ?>">
                    </div>
                </div>
            </fieldset>

            <div class="btns">
                <button type="submit" class="btn btn--primary">Submit Form</button>
                <label class="toggle">
                    <input type="checkbox" name="preview" value="Y"<?=($_POST['preview'] == 'Y' ? ' checked' : ''); ?>>
                    <strong>Preview Listing Before Submission</strong>
                </label>
            </div>

        </form>
        <?php

            // Include skin's javascript file for this page
            $page->addJavascript('js/directory/add.js', 'page');


        ?>
    <?php } ?>

</div>