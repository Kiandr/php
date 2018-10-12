<?php
	/* Manage Listings */
	if (empty($section) || in_array($section, array('add', 'edit', 'import'))) {

	    /* Listing Filter */
	    $sql_where = (!$listingAuth->canManageListings($authuser)) ? "(`l`.`agent` = '" . $authuser->info('id') . "' OR `l`.`agent` IS NULL)" : '';

	    /* Count Listings */
	    $result = mysql_query("SELECT  COUNT(`id`) AS `total` FROM `" . TABLE_LISTINGS . "` `l`" . (!empty($sql_where) ? ' WHERE ' . $sql_where : '') . ";");
	    $count = mysql_fetch_assoc($result);

	    /* All Listings */
	    $filters[] = array('href' => $url . 'listings/?filter=all',  'text' => 'All', 'count' => $count['total'], 'current' => ($_GET['filter'] == 'all'));

	    /* Listing Filters (By Status, Get Listing Count) */
	    $query = "SELECT COUNT(`l`.`id`) AS `total`, `lf`.`value`, IF(`lf`.`user` = 'false', 1, 0) AS `required` FROM `" . TABLE_LISTING_FIELDS . "` `lf` LEFT JOIN `" . TABLE_LISTINGS. "` `l` ON `lf`.`value` = `l`.`status` WHERE `lf`.`field` = 'status'" . (!empty($sql_where) ? ' AND ' . $sql_where : '') . " GROUP BY `lf`.`value` ORDER BY `required` DESC, `lf`.`value` ASC;";
	    if ($result = mysql_query($query)) {
	        while ($filter = mysql_fetch_assoc($result)) {
	            $filters[] = array('href' => $url . 'listings/?filter=' . urlencode($filter['value']), 'text' => $filter['value'], 'count' => $filter['total'], 'current' => ($_GET['filter'] == $filter['value']));
	        }
	    }

	}
?>

<div class="menu menu--drop hidden" id="menu--filters">
    <ul class="menu__list">
		<?php foreach($filters as $filter) { ?>
		<li class="menu__item<?php if($filter['current']) echo ' is-current';?>"><a class="menu__link" href="/backend/<?=$filter['href'];?>"><?=$filter['text'];?></a></li>
		<?php } ?>
    </ul>
</div>


<div class="bar">
    <a href="#" class="bar__title" data-drop="#menu--filters">Listings<?php if($_GET['filter'] != 'all') echo ', ' . Format::htmlspecialchars(ucwords($_GET['filter'])); ?> <svg class="icon icon-drop"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-drop"></use></svg></a>
    <div class="bar__actions">
        <a class="bar__action" href="/backend/listings/add/?status=all"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use></svg></a>
    </div>
</div>


<?php if (!empty($listings)) { ?>
<div class="nodes">
    <ul class="nodes__list">
    	<?php foreach ($listings as $listing) { ?>
    	<li class="nodes__branch">

            <div class="nodes__wrap">
            	<div class="article">
                    <div class="article__body">
                        <div class="article__thumb thumb thumb--medium">
							<?php if (!empty($listing['image'])) { ?>
							<img src="/thumbs/60x60/uploads/<?=$listing['image']; ?>" alt="">
							<?php } else { ?>
							<img src="/thumbs/60x60/uploads/listings/na.png" alt="">
							<?php } // endif ?>
                        </div>
                        <div class="article__content">
                            <a class="text text--strong" href="edit/?id=<?=$listing['id']; ?>"><?=Format::htmlspecialchars($listing['title']); ?></a>
                            <div class="text text--mute"><?=Format::number($listing['price']); ?> - <?=Format::htmlspecialchars($listing['address']); ?>, <?=Format::htmlspecialchars($listing['city']); ?>, <?=$listing['state']; ?></div>
                        </div>
                    </div>
            	</div>
            	<div class="nodes__actions">
					<?php if (!empty($can_delete) || $listing['can_delete'] === true) : ?>
					<a onclick="return confirm('Are you sure you would like to delete this page?');" href="?delete=<?=$listing['id']; ?>&p=<?=$_GET['p']; ?>" title="Delete" class="btn btn--ghost btn--ico"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use></svg></a>
					<?php endif; ?>
            	</div>
            </div>

    	</li>
    	<?php } // endforeach ?>
    </ul>
</div>
<?php
	// Pagination Links
	if (!empty($pagination['links'])) {
		echo '<div class="rewui nav_pagination">';
		if (!empty($pagination['prev'])) echo '<a href="' . $pagination['prev']['url'] . '" class="prev">&lt;&lt;</a>';
		if (!empty($pagination['links'])) {
			foreach ($pagination['links'] as $link) {
				echo '<a href="' . $link['url'] . '" ' . (!empty($link['active']) ? ' class="current"' : '') . '>' . $link['link'] . '</a>';
			}
		}
		if (!empty($pagination['next'])) echo '<a href="' . $pagination['next']['url'] . '" class="next">&gt;&gt;</a>';
		echo '</div>';
	}

	           ?>
<?php } else { ?>
    <p class="block">No Listings Found</p>
<?php } ?>