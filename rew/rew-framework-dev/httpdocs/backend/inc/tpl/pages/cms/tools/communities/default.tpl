<div class="bar -ghost">
	<div class="bar__title"><?= __('Communities'); ?></div>
	<div class="bar__actions">
		<a class="bar__action" href="add/"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-add"/></svg></a>
	</div>
</div>

<?php if (empty($manage_communities)) { ?>
<div class="block">
    <p class="block"><?= __('There are currently no featured communities.'); ?></p>
</div>
<?php } else { ?>



<div id="communities" class="nodes">
	<ul class="nodes__list">

		<?php foreach ($manage_communities as $community) { ?>

		<li class="nodes__branch" id="items-<?=$community['id']; ?>">
			<div class="nodes__wrap">
                <div class="nodes__handle"></div>
				<div class="article">

					<div class="article__body">

						<div class="article__thumb thumb thumb--medium">
							<?php if (!empty($community['image'])) { ?>
							<img src="/thumbs/60x60/uploads/<?=$community['image']; ?>" alt="">
							<?php } else { ?>
							<img src="/thumbs/60x60/uploads/listings/na.png" alt="">
							<?php }?>
						</div>

						<div class="article__content">
							<a href="edit/?id=<?=$community['id']; ?>" class="text text--strong"><?=Format::htmlspecialchars($community['title']); ?></a>

							<div class="text text--mute">

								<?php
								// Display enabled/featured status
								if ($community['is_enabled'] != 'Y') {
									echo '<label>' . __('Disabled') . '</label>';
								} else {
									echo '<label>' . __('Enabled') . '</label>';
								}
								?>

								#<?=Format::htmlspecialchars($community['snippet']); ?>#

							</div>
						</div>
					</div>
				</div>

				<div class="nodes__actions">
                    <?php if ($toolsAuth->canDeleteCommunities($authuser)) { ?>
					<a class="btn btn--ghost btn--ico delete" href="?delete=<?=$community['id']; ?>" onclick="return confirm('<?= __('Are you sure you want to delete this featured community?'); ?>');">
						<svg class="icon icon-trash mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use></svg>
					</a>
                    <?php } ?>
				</div>

			</div>
		</li>
		<?php } ?>
    </ul>
</div>
<?php } ?>