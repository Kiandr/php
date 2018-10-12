<filter-blog-posts name="Filter"></filter-blog-posts>
<div class="bar">
    <div class="bar__title"><?= __('Blog Posts'); ?></div>
    <div class="bar__actions">
        <a class="bar__action" href="/backend/blog/entries/add/"><svg class="icon icon-add mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use></svg></a>
		<btn-filter></btn-filter>
    </div>
</div>

<?php if (!empty($entries)) { ?>
<div class="nodes">
    <ul class="nodes__list">
    	<?php foreach($entries as $entry) { ?>
    	<li class="nodes__wrap">
    		<div class="article">
    		    <div class="article__body">
    		        <div class="article__thumb thumb thumb--medium -bg-rew2"><svg class="icon icon--invert"><use xlink:href="/backend/img/icos.svg#icon-page"/></svg></div>
        			<div class="article__content">
        			    <a class="text text--strong" href="edit/?id=<?=$entry['id']; ?>" title="<?=htmlspecialchars(Format::truncate(strip_tags($entry['body']), 100)); ?>"><?=Format::htmlspecialchars($entry['title']); ?></a>
            			<div class="text text--mute">
                            <?php if ($entry['published'] === 'true') {
                                __('Published %s by %s', date('M j, Y', $entry['date']),
                                    Format::htmlspecialchars($entry['author_name']));
                            } else {
                                echo __('Unpublished');
                            } ?>
            			</div>
        			</div>
    			</div>
    		</div>
			<div class="nodes__actions">
				<!--<a class="btn btn--ghost" href="<?=sprintf(URL_BLOG_ENTRY, $entry['link']); ?>">View / Preview</a>-->
                <form method="post">
                    <input type="hidden" name="delete" value="<?=$entry['id']; ?>" />
                    <button onclick="return confirm('<?= __('Are you sure you would like to delete this blog entry?'); ?>');" title="<?= __('Delete'); ?>" class="btn btn--ghost btn--ico">
                        <icon name="icon--trash--row"></icon>
                    </button>
                </form>
			</div>
    	</li>
    	<?php } ?>
    </ul>
</div>
<?php } else { ?>
<p class="block"><?= __('You currently have no blog entries.'); ?></p>
<?php } ?>

<?php if (!empty($paginationLinks)) { ?>
<div class="nav_pagination">
    <?php if (!empty($paginationLinks['prevLink'])) { ?>
    <a class="prev marR" href="<?=$paginationLinks['prevLink']; ?>">
        <svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg>
    </a>
    <?php } ?>
    <?php if (!empty($paginationLinks['nextLink'])) { ?>
    <a class="next" href="<?=$paginationLinks['nextLink']; ?>">
        <svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-right-a"></use></svg>
    </a>
    <?php } ?>
</div>
<?php } ?>