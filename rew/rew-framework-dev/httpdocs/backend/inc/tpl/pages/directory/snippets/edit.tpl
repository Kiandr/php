<form id="directory-builder-form" action="?submit" method="post">
	<input type="hidden" name="id" value="<?=$snippet['name']; ?>">

    <div class="bar">
	    <div class="bar__title"><?=$snippet['name']; ?></div>
    </div>

	<div class="btns btns--stickyB">
    	<span class="R">
		    <a class="btn copy" href="<?=URL_BACKEND; ?>cms/snippets/copy/?id=<?=$snippet['name']; ?>">Copy</a>
            <button class="btn btn--positive" type="submit"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"/></svg> Save</button>
        </span>
    </div>

    <div class="block">
    
    	<div class="field">
    		<label class="field__label"> <?=tpl_lang('LBL_FORM_CMS_SNIP_NAME'); ?> <em class="required">*</em></label>
    		<input type="text" class="search_input w1/1" name="snippet_id" value="<?=htmlspecialchars($snippet['name']); ?>" data-slugify required>
    		<p class="tip">
    			<?=tpl_lang('DESC_FORM_CMS_SNIP_NAME'); ?>
    		</p>
    	</div>
	<div class="field">
		<label class="field__label">Snippet Title</label>
		<input type="text" class="w1/1 search_input" name="snippet_title" id="snippet_title" value="<?=htmlspecialchars($_POST['snippet_title']); ?>" required>
		<p class="tip">The snippet title will be included in the page's heading.</p>
	</div>
	<div class="field">
		<label class="field__label">Results per Page <em class="required">*</em></label>
		<input class="w1/1" type="number" name="page_limit" value="<?=$_POST['page_limit']; ?>" min="1" max="48">
	</div>
	<div class="field">
		<label class="field__label">Sort Results By</label>
		<select class="w1/1" name="sort_by">
			<?php foreach ($options['sort'] as $option) : ?>
			<option value="<?=$option['value']; ?>"<?=($_POST['sort_by'] == $option['value']) ? ' selected' : ''; ?>>
			<?=$option['title']; ?>
			</option>
			<?php endforeach; ?>
		</select>
	</div>

    	<h2>Pages Used On</h2>
    	<?php if (!empty($snippet['pages'])) : ?>
    	<ul class="checklist">
    		<?php foreach ($snippet['pages'] as $pg) : ?>
    		<li>
    			<div class="item_content_ico ico ico-page"></div>
    			<a href="<?=$pg['href']; ?>">
    			<?=$pg['text']; ?>
    			</a> </li>
    		<?php endforeach; ?>
    	</ul>
    	<?php else : ?>
    	<div class="field">
    		<p>This snippet is currently not being used on any pages.</p>
    	</div>
    	<?php endif; ?>

    	<h2>Keyword</h2>
    	<div class="field">
    		<input class="w1/1" type="text" name="search_keyword" value="<?=htmlspecialchars($criteria['search_keyword']); ?>">
    	</div>
    	<h2>Categories</h2>
    
    		<?php foreach ($options['categories'] as $category) : ?>
    
    		<label class="field__label">
    			<input type="checkbox" name="search_category[]" value="<?=$category['link'];?>"<?=(is_array($criteria['search_category']) && in_array($category['link'], $criteria['search_category'])) ? ' checked' : ''; ?>>
    			<?=$category['title']; ?>
    		</label>
    		<?php if (!empty($category['sub_cats'])) : ?>
    		<ul class="checklist">
    			<?php foreach ($category['sub_cats'] as $sub_category) : ?>
    			<li>
    				<label class="field__label">
    					<input type="checkbox" name="search_category[]" value="<?=$sub_category['link'];?>"<?=(is_array($criteria['search_category']) && in_array($sub_category['link'], $criteria['search_category'])) ? ' checked' : ''; ?>>
    					<?=$sub_category['title']; ?>
    				</label>
    				<?php if (!empty($sub_category['tert_cats'])) : ?>
    				<ul class="checklist">
    					<?php foreach ($sub_category['tert_cats'] as $tert_category) : ?>
    					<li>
    						<label class="field__label">
    							<input type="checkbox" name="search_category[]" value="<?=$tert_category['link'];?>"<?=(is_array($criteria['search_category']) && in_array($tert_category['link'], $criteria['search_category'])) ? ' checked' : ''; ?>>
    							<?=$tert_category['title']; ?>
    						</label>
    					</li>
    					<?php endforeach; ?>
    				</ul>
    				<?php endif; ?>
    			</li>
    			<?php endforeach; ?>
    		</ul>
    		<?php endif; ?>
    
    		<?php endforeach; ?>
    	</div>
    
    </div>

</form>