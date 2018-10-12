<form action="?submit" method="post" enctype="multipart/form-data">

<div class="bar">
	<div class="bar__title">New Directory Listing</div>
	<div class="bar__actions">
		<a class="bar__action" href="/backend/directory/listings/"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
	</div>
</div>


<div class="block">
    
    <div class="btns btns--stickyB">
        <span class="R">
    	<button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> Save</button>
    	</span>
    </div>
    
    <div class="field">
    	<label class="field__label">Business Name <em class="required">*</em></label>
    	<input class="w1/1" type="text" name="business_name" value="<?=htmlspecialchars($_POST['business_name']); ?>" required>
    </div>
    <div class="field">
    	<label class="field__label">Primary Category <em class="required">*</em></label>
    	<select class="w1/1" name="primary_category">
    		<option value="">Select Category</option>
    		<?php foreach ($all_categories as $category) : ?>
    		<option value="<?=$category['link'];?>"<?=($_POST['primary_category'] == $category['link']) ? ' selected' : ''; ?>>
    		<?=$category['title'];?>
    		</option>
    		<?php endforeach; ?>
    	</select>
    </div>
    <div class="field">
    	<label class="field__label">Description</label>
    	<textarea class="w1/1 tinymce simple" id="description" name="description" cols="24" rows="15"><?=htmlspecialchars($_POST['description']); ?>
    </textarea>
    </div>
    <div class="field">
    	<label class="field__label">Page Title</label>
    	<input class="w1/1" type="text" name="page_title" value="<?=htmlspecialchars($_POST['page_title']); ?>">
    </div>
    <h3>Business Location</h3>
    <div class="field">
    	<label class="field__label">Street Address</label>
    	<input class="w1/1" type="text" name="address" value="<?=htmlspecialchars($_POST['address']); ?>">
    </div>
    <div class="field">
    	<label class="field__label">City</label>
    	<input class="w1/1" type="text" name="city" value="<?=htmlspecialchars($_POST['city']); ?>">
    </div>
    <div class="field">
    	<label class="field__label">
    		<?=Locale::spell('State'); ?>
    	</label>
    	<input class="w1/1" type="text" name="state" value="<?=htmlspecialchars($_POST['state']); ?>">
    </div>
    <div class="field">
    	<label class="field__label">
    		<?=Locale::spell('Zip Code'); ?>
    	</label>
    	<input class="w1/1" type="text" name="zip" value="<?=htmlspecialchars($_POST['zip']); ?>">
    </div>
    <div class="field">
    	<label class="field__label">Latitude</label>
    	<input class="w1/1" type="text" name="latitude" value="<?=htmlspecialchars($_POST['latitude']); ?>" pattern="-?\d{1,3}\.\d+">
    </div>
    <div class="field">
    	<label class="field__label">Longitude</label>
    	<input class="w1/1" type="text" name="longitude" value="<?=htmlspecialchars($_POST['longitude']); ?>" pattern="-?\d{1,3}\.\d+">
    </div>
    <h3>Business Information</h3>
    <div class="field">
    	<label class="field__label">Phone</label>
    	<input class="w1/1" type="tel" name="phone" value="<?=htmlspecialchars($_POST['phone']); ?>">
    </div>
    <div class="field">
    	<label class="field__label">Alt Phone</label>
    	<input class="w1/1" type="tel" name="alt_phone" value="<?=htmlspecialchars($_POST['alt_phone']); ?>">
    </div>
    <div class="field">
    	<label class="field__label">Toll Free</label>
    	<input class="w1/1" type="tel" name="toll_free" value="<?=htmlspecialchars($_POST['toll_free']); ?>">
    </div>
    <div class="field">
    	<label class="field__label">Fax</label>
    	<input class="w1/1" type="tel" name="fax" value="<?=htmlspecialchars($_POST['fax']); ?>">
    </div>
    <div class="field">
    	<label class="field__label">Website</label>
    	<input class="w1/1" type="text" name="website" value="<?=htmlspecialchars($_POST['website']); ?>">
    </div>
    <div class="field">
    	<label class="field__label">Make Link</label>
    	<label class="w1/1" class="boolean">
    	<input type="checkbox" name="website_link" value="Y"<?=($_POST['website_link'] == 'Y') ? ' checked="checked"' : ''; ?>>
    	</label>
    </div>
    <h3>Photo Manager</h3>
    <div class="field">
    	<p>Uploads must be in gif, jpeg, or png format and under 4 MB in size. Drag and drop listing photos to re-arrange.</p>
    </div>
    <div class="field">
    	<label class="field__label">Logo</label>
    	<div data-uploader='<?=json_encode(['extraParams' => ['type' => 'directory_logo']]); ?>'>
    		<?php if (!empty($logo_uploads)) : ?>
    		<div class="file-manager">
    			<ul>
    				<?php foreach ($logo_uploads as $upload) : ?>
    				<li>
    					<div class="wrap"> <img src="/thumbs/95x95/uploads/<?=$upload['file']; ?>" border="0">
    						<input type="hidden" name="uploads[]" value="<?=$upload['id']; ?>">
    					</div>
    				</li>
    				<?php endforeach; ?>
    			</ul>
    		</div>
    		<?php endif; ?>
    	</div>
    </div>
    <div class="field">
    	<label class="field__label">Images</label>
    	<div data-uploader='<?=json_encode(['extraParams' => ['type' => 'directory']]); ?>'>
    		<?php if (!empty($uploads)) : ?>
    		<div class="file-manager">
    			<ul>
    				<?php foreach ($uploads as $upload) : ?>
    				<li>
    					<div class="wrap"> <img src="/thumbs/95x95/uploads/<?=$upload['file']; ?>" border="0">
    						<input type="hidden" name="uploads[]" value="<?=$upload['id']; ?>">
    					</div>
    				</li>
    				<?php endforeach; ?>
    			</ul>
    		</div>
    		<?php endif; ?>
    	</div>
    </div>
    
    
    	<h3>Contact Information</h3>
    	<div class="field">
    		<label class="field__label">Contact Name</label>
    		<input class="w1/1" type="text" name="contact_name" value="<?=htmlspecialchars($_POST['contact_name']); ?>">
    	</div>
    	<div class="field">
    		<label class="field__label">Contact Phone</label>
    		<input class="w1/1" type="tel" name="contact_phone" value="<?=htmlspecialchars($_POST['contact_phone']); ?>">
    	</div>
    	<div class="field">
    		<label class="field__label">Contact Email</label>
    		<input class="w1/1" type="email" name="contact_email" value="<?=htmlspecialchars($_POST['contact_email']); ?>">
    	</div>
    
    
    
    	<div class="field">
    		<h3>Settings</h3>
    		<ul class="checklist">
    			<li>
    				<label class="field__label">
    					<input type="checkbox" class="checkbox" name="pending" value="N"<?=!empty($_POST['pending']) ? ' checked="checked"' : ''; ?>>
    					Approve for Display</label>
    			</li>
    			<li>
    				<label class="field__label">
    					<input type="checkbox" class="checkbox" name="no_follow" value="Y"<?=!empty($_POST['no_follow']) ? ' checked="checked"' : ''; ?>>
    					Set Links to No-Follow</label>
    			</li>
    			<li>
    				<label class="field__label">
    					<input type="checkbox" class="checkbox" name="featured" value="Y"<?=!empty($_POST['featured']) ? ' checked="checked"' : ''; ?>>
    					Featured Listing</label>
    			</li>
    		</ul>
    		<h3>Categories <em class="required">*</em></h3>
    		<div>
    			<?php if (!empty($categories)) : ?>

    				<?php foreach ($categories as $category) : ?>
    				<?php $checked = (is_array($_POST['categories']) && in_array($category['link'], $_POST['categories'])) ? ' checked' : ''; ?>

    					<label class="field__label">
    						<input type="checkbox" name="categories[]" value="<?=$category['link']; ?>"<?=$checked; ?>>
    						<?=$category['title']; ?>
    					</label>
    					<?php if(!empty($category['sub_categories'])) : ?>
    					<ul class="checklist">
    						<?php foreach ($category['sub_categories'] as $sub_category) : ?>
    						<?php $checked = (is_array($_POST['categories']) && in_array($sub_category['link'], $_POST['categories'])) ? ' checked' : ''; ?>
    						<li>
    							<label class="field__label">
    								<input type="checkbox" name="categories[]" value="<?=$sub_category['link']; ?>"<?=$checked; ?>>
    								<?=$sub_category['title']; ?>
    							</label>
    							<?php if(!empty($sub_category['tert_categories'])) : ?>
    							<ul class="checklist">
    								<?php foreach ($sub_category['tert_categories'] as $tert_category) : ?>
    								<?php $checked = (is_array($_POST['categories']) && in_array($tert_category['link'], $_POST['categories'])) ? ' checked' : ''; ?>
    								<label class="field__label">
    									<input type="checkbox" name="categories[]" value="<?=$tert_category['link']; ?>"<?=$checked; ?>>
    									<?=$tert_category['title']; ?>
    								</label>
    								<?php endforeach; ?>
    							</ul>
    							<?php endif; ?>
    						</li>
    						<?php endforeach; ?>
    					</ul>
    					<?php endif; ?>

    				<?php endforeach; ?>

    			<?php endif; ?>
    		</div>
    	</div>
    </div>
    </div>
</form>
