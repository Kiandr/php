<div class="bar">
    <div class="bar__title"><?= __('Blog Links'); ?></div>
    <div class="bar__actions">
        <a class="bar__action" href="/backend/cms/navs/blog-links/add/"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use></svg></a>
    </div>
</div>

<?php if (!empty($links)) { ?>
<div class="nodes">
    <ul id="blog-links" class="nodes__list">
    	<?php foreach ($links as $link) { ?>
    	<li class="nodes__branch" id="links-<?=$link['id']; ?>">
    	    <div class="nodes__wrap">
    	        <div class="nodes__handle"></div>
        		<div class="article">
        		    <div class="article__body">
        		        <div class="article__thumb thumb thumb--medium -bg-rew2">
                			<svg class="icon icon--invert">
                				<use xlink:href="/backend/img/icos.svg#icon-link"/>
                			</svg>
        		        </div>
        		        <div class="article__content">
        		            <a class="text text--strong" href="edit/?id=<?=$link['id']; ?>"><?=Format::htmlspecialchars($link['title']); ?></a>
        		        </div>
        		    </div>
        		</div>
        		<div class="nodes__actions">
        			<a class="btn btn--ico btn--ghost" href="?delete=<?=$link['id']; ?>" onclick="return  confirm('<?= __('Are you sure you want to delete this blog link?'); ?>');">
        				<svg class="icon">
        					<use xlink:href="/backend/img/icos.svg#icon-trash"></use>
        				</svg>
                    </a>
        		</div>
    		</div>
    	</li>
    	<?php } ?>
    </ul>
</div>
<?php } else { ?>
<div class="block">
    <p class="block"><?= __('There are currently no blog links.'); ?></p>
</div>
<?php } ?>