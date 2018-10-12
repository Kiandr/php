<div class="bar">
    <div class="bar__title"><?= __('Categories'); ?></div>
    <div class="bar__actions">
        <a class="bar__action" href="/backend/blog/categories/add/"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use></svg></a>
    </div>
</div>

<?php if (!empty($categories)) { ?>
<div class="nodes dd" id="blog-categories">
	<ul class="nodes__list">
		<?php foreach ($categories as $category) { ?>
		<li class="nodes__branch dd-item" id="categories-<?=$category['id']; ?>">
		    <div class="nodes__wrap">
                <div class="nodes__handle"></div>
    			<div class="article">
    			    <div class="article__body">
        			    <div class="article__thumb thumb thumb--medium -bg-rew2">
            				<svg class="icon icon--invert">
            					<use xlink:href="/backend/img/icos.svg#icon-groups"/>
            				</svg>
        			    </div>
        			    <div class="article__content">
        			        <a class="text text--strong" href="edit/?id=<?=$category['id']; ?>" style="display: block; max-width: 80%;"><?=Format::htmlspecialchars($category['title']); ?></a>
                            <div class="text text--mute" style="white-space: nowrap; text-overflow: ellipsis; overflow: hidden;"><?=Format::htmlspecialchars(strip_tags($category['description'])); ?></div>
        			    </div>
                    </div>
    			</div>
    			<div class="nodes__actions">
                    <form method="post">
                        <input type="hidden" name="delete" value="<?=$category['id']; ?>" />
                        <button onclick="return confirm('<?= __('Are you sure you would like to delete this  blog category?'); ?>');" title="<?= __('Delete'); ?>" class="btn btn--ghost btn--ico">
                            <icon name="icon--trash--row"></icon>
                        </button>
                    </form>
    			</div>
		    </div>

			<?php if (!empty($category['subcategories'])) { ?>
			<ul class="nodes__list dd-list">
				<?php foreach ($category['subcategories'] as $sindex => $subcategory) { ?>
				<li class="nodes__branch dd-item" id="categories-<?=$subcategory['id']; ?>">
				    <div class="nodes__wrap">
                        <div class="nodes__handle"></div>
    					<div class="article">
    					    <div class="article__body">
                			    <div class="article__thumb thumb thumb--medium -bg-rew2">
                    				<svg class="icon icon--invert">
                    					<use xlink:href="/backend/img/icos.svg#icon-groups"/>
                    				</svg>
                			    </div>
                			    <div class="article__content">
                			        <a class="text text--strong" href="edit/?id=<?=$subcategory['id']; ?>" style="display: block; max-width: 80%;"><?=Format::htmlspecialchars($subcategory['title']); ?></a>
                			        <div class="text text--mute" style="white-space: nowrap; text-overflow: ellipsis; overflow: hidden; max-width: 85%;"><?=Format::htmlspecialchars(strip_tags($subcategory['description'])); ?></div>
                			    </div>
    						</div>
						</div>
						<div class="nodes__actions" style="position: relative; left: -25px;">
                            <form method="post">
                                <input type="hidden" name="delete" value="<?=$subcategory['id']; ?>" />
                                <button onclick="return confirm('<?= __('Are you sure you would like to delete this  blog category?'); ?>');" title="<?= __('Delete'); ?>" class="btn btn--ghost btn--ico">
                                    <icon name="icon--trash--row"></icon>
                                </button>
                            </form>
						</div>
					</div>
				</li>
				<?php } ?>
			</ul>
			<?php } ?>
		</li>
		<?php } ?>
	</ul>
</div>
<?php } else { ?>
<p class="block"><?= __('There are currently no blog categories.'); ?></p>
<?php } ?>