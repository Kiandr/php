<div class="bar">
	<div class="bar__title">Directory Categories</div>
	<div class="bar__actions">
		<a class="bar__action" href="/backend/directory/categories/add/"><svg class="icon "><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use></svg></a>
	</div>
</div>

<?php if (!empty($manage_categories)) : ?>
<div class="nodes dd" id="categories">
	<ul class="nodes__list">
		<?php foreach ($manage_categories as $manage_category) : ?>
		<li class="nodes__branch" id="categories-<?=$manage_category['id']; ?>">
            <div class="nodes__wrap">
    			<div class="article">
        			<div class="article__body">
            			<div class="article__thumb thumb thumb--medium -bg-rew2">
                			<svg class="icon icon--invert"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-groups"></use></svg>
            			</div>
            			<div class="article__content">
                			<a class="text text--strong" href="edit/?id=<?=$manage_category['id']; ?>"><?=$manage_category['title']; ?></a>
            			</div>
    			    </div>
    			</div>
				<div class="nodes__actions">
					<?php if (!empty($can_delete)) : ?>
					<a class="btn btn--ghost btn--ico" href="?delete=<?=$manage_category['id']; ?>" onclick="return confirm('Are you sure you would like to delete this directory category?');"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-trash"></use></svg></a>
					<?php endif; ?>
				</div>
            </div>

    			<?php if (!empty($manage_category['sub_cats'])) : ?>
    			<ul class="dd-list">
    				<?php foreach ($manage_category['sub_cats'] as $sindex => $sub_category) : ?>
    				<li class="nodes__branch" id="categories-<?=$sub_category['id']; ?>">
                        <div class="nodes__wrap">
        					<div class="article">
                                <div class="article__body">
                        			<div class="article__thumb thumb thumb--medium -bg-rew2">
                            			<svg class="icon icon--invert"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-groups"></use></svg>
                        			</div>
                        			<div class="article__content">
                            			<a class="text text--strong" href="edit/?id=<?=$sub_category['id']; ?>"><?=$sub_category['title']; ?></a>
                        			</div>
                                </div>
        					</div>
            				<div class="nodes__actions">
            					<?php if (!empty($can_delete)) : ?>
            					<a class="btn btn--ghost btn--ico" href="?delete=<?=$sub_category['id']; ?>" onclick="return confirm('Are you sure you would like to delete this directory category?');"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-trash"></use></svg></a>
            					<?php endif; ?>
            				</div>
                        </div>
        					<?php if (!empty($sub_category['tert_cats'])) : ?>
        					<ul class="dd-list">
        						<?php foreach ($sub_category['tert_cats'] as $tindex => $tert_category) : ?>
        						<li class="nodes__branch" id="categories-<?=$tert_category['id']; ?>">
                                    <div class="nodes__wrap">
            							<div class="article">
                                			<div class="article__thumb thumb thumb--medium -bg-rew2">
                                    			<svg class="icon icon--invert"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-groups"></use></svg>
                                			</div>
                                			<div class="article__content">
                                    			<a class="text text--strong" href="edit/?id=<?=$tert_category['id']; ?>"><?=$tert_category['title']; ?></a>
                                			</div>
            							</div>
                        				<div class="nodes__actions">
                        					<?php if (!empty($can_delete)) : ?>
                        					<a class="btn btn--ghost btn--ico" href="?delete=<?=$tert_category['id']; ?>" onclick="return confirm('Are you sure you would like to delete this directory category?');"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-trash"></use></svg></a>
                        					<?php endif; ?>
                        				</div>
                                    </div>
        						</li>
        						<?php endforeach; ?>
        					</ul>
        					<?php endif; ?>

    				</li>
    				<?php endforeach; ?>
    			</ul>
    			<?php endif; ?>

		</li>
		<?php endforeach; ?>
	</ul>
</div>
<?php else : ?>
<p class="block">There are currently no directory categories.</p>
<?php endif; ?>
