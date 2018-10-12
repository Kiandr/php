<div class="bar">
	<div class="bar__title">Manage Developments</div>
	<div class="bar__actions">
		<a class="bar__action" href="add/"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-add"/></svg></a>
	</div>
</div>

<section id="developments-list">
	<?php if (empty($developments)) { ?>
		<p class="block none">There are currently no developments.</p>
	<?php } else { ?>

    <div class="nodes">
        <ul class="nodes__list">
			<?php foreach ($developments as $development) { ?>
			<li class="nodes__branch" id="order-<?=$development['id']; ?>">
			    <div class="nodes__wrap">
    				<div class="article">
    					<div class="article__body">
                            <div class="article__thumb thumb thumb--medium">
                                <?php if (!empty($development['image'])) { ?>
                                <img src="/thumbs/60x60/uploads/<?=$development['image']; ?>" alt="">
    							<?php } else { ?>
                                <img src="/thumbs/60x60/uploads/listings/na.png" alt="">
    							<?php }?>
    						</div>
                            <div class="article__content">
    							<a class="text text--strong" href="edit/?id=<?=$development['id']; ?>"><?=Format::htmlspecialchars($development['title']); ?></a>
    							<div class="text text--mute" style="text-overflow: ellipsis;"><?=Format::htmlspecialchars(Format::truncate($development['subtitle'], 150)); ?> <?=Format::htmlspecialchars(Format::truncate($development['description'], 150)); ?></div>
    						</div>
    					</div>
                        <div class="article__foot">
    					    <?php
    						// Display enabled/featured status
    						if ($development['is_enabled'] != 'Y') {
    							echo '<div class="text text--mute">Disabled</div>';
    						} else if ($development['is_featured'] === 'Y') {
    							echo '<div class="text text--mute">Featured</div>';
    						} else {
    							echo '<div class="text text--mute">Enabled</div>';
    						}
                            ?>
    					</div>
    				</div>
    				<div class="nodes__actions">
                        <?php if (!empty($can_delete)) { ?>
    					    <a class="btn btn--ico btn--ghost" href="delete/?id=<?=$development['id']; ?>"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-trash"/></svg></a>
                        <?php } ?>
    				</div>
				</div>
			</li>
			<?php } ?>
        </ul>
    </div>

	<?php } ?>
</section>
