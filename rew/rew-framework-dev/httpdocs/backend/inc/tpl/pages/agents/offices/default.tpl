<div class="bar">
	<div class="bar__title"><?= __('Offices'); ?></div>
	<div class="bar__actions">
		<a class="bar__action" href="/backend/agents/offices/add/"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use></svg></a>
	</div>
</div>

<?php if (!empty($manage_offices)) : ?>
<div class="nodes">
    <ul id="offices" class="nodes__list">
    	<?php foreach ($manage_offices as $manage_office) : ?>
    	<li class="nodes__branch" id="offices-<?=$manage_office['id']; ?>">
    	    <div class="nodes__wrap">
                <div class="nodes__handle"></div>
        		<div class="article">
                    <div class="article__body">
                        <div class="article__thumb thumb thumb--medium">
        		            <?php if (!empty($manage_office['image'])):?>
                            <img src="/thumbs/60x60/uploads/offices/<?=$manage_office['image']; ?>" alt="">
                            <?php else : ?>
                            <img src="/thumbs/60x60/uploads/offices/na.png" alt="">
                            <?php endif;?>
                        </div>
                        <div class="article__content">
                            <a class="text text--strong" href="edit/?id=<?=$manage_office['id']; ?>"><?=Format::htmlspecialchars($manage_office['title']); ?></a>
                            <div class="text text--mute">
                                <?php if (!empty($manage_office['city'])) : ?>
                                <?=Format::htmlspecialchars($manage_office['city']);?>, <?=Format::htmlspecialchars($manage_office['state']);?>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>
        		</div>
        		<div class="nodes__actions">
                    <form method="post">
                        <input type="hidden" name="delete" value="<?=$manage_office['id']; ?>" />
                        <button onclick="return confirm('<?= __('Are you sure you would like to delete this office?'); ?>');" title="<?= __('Delete'); ?>" class="btn btn--ghost btn--ico">
                            <icon name="icon--trash--row"></icon>
                        </button>
                    </form>
                </div>
    	    </div>
    	</li>
        <?php endforeach; ?>
    </ul>
</div>
<?php else : ?>
<div class="block">
    <p class="block"><?= __('There are currently no office locations.'); ?></p>
</div>
<?php endif; ?>


