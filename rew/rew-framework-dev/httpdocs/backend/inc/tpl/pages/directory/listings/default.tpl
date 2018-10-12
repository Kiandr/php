<div class="bar">
	<div class="bar__title">Directory Listings</div>
	<div class="bar__actions">
		<a class="bar__action" href="/backend/directory/listings/add/"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use></svg></a>
	</div>
</div>

<?php if (!empty($manage_listings)) : ?>

<div class="nodes">
	<ul class="nodes__list">
		<?php foreach ($manage_listings as $listing) : ?>
		<li class="nodes__branch">
		    <div class="nodes__wrap">
    			<div class="article">
    				<div class="article__body">
        				<div class="article__thumb thumb thumb--medium">
    						<?php if (!empty($listing['logo'])) : ?>
    						<img src="/thumbs/60x60/<?=$listing['logo']; ?>" alt="">
    						<?php else:?>
    						<img src="/thumbs/60x60/uploads/listings/na.png" alt="">
    						<?php endif;?>
        				</div>
        				<div class="article__content">
            				<a class="text text--strong" href="edit/?id=<?=$listing['id']; ?>"><?=$listing['business_name']; ?></a>
            				<?php if($listing['pending'] == 'Y') { ?>
            				<div class="text text--mute">Pending</div>
            				<?php } ?>
        				</div>
    				</div>
			    </div>
			</div>
			<div class="nodes__actions">
				<?php if (!empty($can_delete)) : ?>
				<a class="btn btn--ghost delete" href="?delete=<?=$listing['id']; ?>" onclick="return confirm('Are you sure you would like to delete this directory listing?');"><svg class="icon"><use xlink:href="#icon-trash"/></svg></a>
				<?php endif; ?>
			</div>
		</li>
		<?php endforeach; ?>
	</ul>
</div>

<?php if (!empty($pagination['links'])) : ?>
<div class="rewui nav_pagination">
	<?php if (!empty($pagination['prev'])) : ?>
	<a href="<?=$pagination['prev']['url']; ?>" class="prev">&lt;&lt;</a>
	<?php endif; ?>
	<?php if (!empty($pagination['links'])) : ?>
	<?php foreach ($pagination['links'] as $link) : ?>
	<a href="<?=$link['url']; ?>"<?=!empty($link['active']) ? ' class="current"' : ''; ?>>
	<?=$link['link']; ?>
	</a>
	<?php endforeach; ?>
	<?php endif; ?>
	<?php if (!empty($pagination['next'])) : ?>
	<a href="<?=$pagination['next']['url']; ?>" class="next">&gt;&gt;</a>
	<?php endif; ?>
</div>
<?php endif; ?>
<?php else : ?>
<p class="block none">There are currently no directory listings.</p>
<?php endif; ?>
