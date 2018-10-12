<div class="bar">
    <div class="bar__title"><?= __('IDX Searches'); ?></div>
    <?php if($settings->searches['custom']) {?>
        <div class="bar__actions">
            <a class="bar__action" href="/backend/idx/searches/add/"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use></svg></a>
        </div>
    <?php } ?>
</div>

<div class="nodes">
    <ul class="nodes__list">
    	<li class="nodes__branch">
    	    <div class="nodes__wrap">
        		<div class="article">
            		<div class="article__body">
            		    <div class="article__body">
            		        <a class="text text--strong" href="/backend/idx/default-search/"><?= __('Default Search'); ?></a>
            		    </div>
            		</div>
                </div>
    	    </div>
    	</li>
    	<?php if (!empty($searches)) : ?>
    	<?php foreach ($searches as $search) : ?>
    	<li class="nodes__list">
    	    <div class="nodes__wrap">
        		<div class="article">
            		<div class="article__body">
                		<a class="text text--strong" href="searches/edit/?search_id=<?=$search['id'];?>"><?=Format::htmlspecialchars($search['title']); ?></a>
            		</div>
        		</div>
    			<div class="nodes__actions">
        			<a class="btn btn--ghost btn--ico" title="Delete" href="?delete=<?=$search['id']; ?>" onclick="return confirm('<?= __('Are you sure you would like to delete this page?'); ?>');">
    				    <svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-trash"/></svg>
    				</a>
                </div>
    	    </div>
    	</li>
    	<?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>

