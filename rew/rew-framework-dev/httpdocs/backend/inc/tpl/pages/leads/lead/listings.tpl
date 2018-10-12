<?php

// Render lead summary header (menu/title/preview)
echo $this->view->render('inc/tpl/partials/lead/summary.tpl.php', [
    'title' => 'Lead Listings',
    'lead' => $lead,
    'leadAuth' => $leadAuth
]);

?>

<?php if (isset($_GET['popup'])) { ?>
<div class="app_actions"> <a class="btn close" href="javascript:void(0);">Close</a> </div>
<?php } ?>

<div class="block">
<div class="tabs">
	<ul>
		<?php foreach ($types as $type => $name) { ?>
		<li<?=($type === $filter ? ' class="current"' : ''); ?>> <a href="?id=<?=$lead['id']; ?>&type=<?=$type; ?>">
			<?=$name . ' (' . Format::number($counts[$type]) . ')'; ?>
			</a> </li>
		<?php } ?>
	</ul>
</div>
</div>

<section class="nodes tabset-content">

	<ul class="nodes__list">

		<?php if (!empty($listings)) { ?>
		<?php foreach ($listings as $listing) { ?>
		<li class="nodes__branch">
		    <div class="nodes__wrap">
    			<div class="article">
                    <div class="article__body">

        				<?php if (!empty($listing['error'])) { ?>

        				<!-- Old, missing listing -->
                        <div class="article__thumb thumb thumb--medium">
                             <img src="/thumbs/60x60/uploads/listings/na.png" alt="">
                        </div>
        				<div class="article__content">
        					<div class="text text--strong">MLS# <?=Format::htmlspecialchars($listing['mls_number']); ?></div>
        					<div class="text text--mute">This listing is no longer available.</div>


        				<?php } else { ?>


        				<!-- Active listing -->

                        <div class="article__thumb thumb thumb--medium">
                            <img src="<?=Format::thumbUrl($listing['ListingImage'], '60x60'); ?>" alt="">
                        </div>

        				<div class="article__content">

        				    <a class="text text--strong" href="<?=$listing['url_details']; ?>" target="_blank"><?=Format::htmlspecialchars($listing['Address']); ?></a> (MLS&reg; #<?=Format::htmlspecialchars($listing['ListingMLS']); ?>)</a>

                            <div class="text text--mute">
                                $<?=Format::number($listing['ListingPrice']); ?> -
        						<?=Format::htmlspecialchars(implode(', ', array_filter(array($listing['AddressSubdivision'], $listing['AddressCity'], $listing['AddressState'], $listing['AddressZipCode'])))); ?>
                            </div>

                            <?php

                            // # of Views
                            if ($filter === 'viewed') {
                                $viewCount = Format::number($listing['views']);
                                echo 'Viewed ' . Format::plural($viewCount, $viewCount . ' times', 'once');
                                echo '</br>';
                            }

                            ?>

                            <time datetime="<?=date('c', $listing['timestamp']); ?>" title="<?=date('l, F jS Y \@ g:ia', $listing['timestamp']); ?>">
                                <?=Format::dateRelative($listing['timestamp']); ?>
                            </time>

                            <?php } ?>
                                <?php

                                // Listing recommendation
                                if ($filter === 'recommended') {

                                    // Recommended By Agent
                                    if (!empty($listing['agent'])) {

                                        echo '<div class="v groups">';
                                        if ($canViewAgent) {
                                            echo '<span class="token">';
                                            echo '<span class="token__thumb thumb thumb--tiny"><img width="24" height="24" src="/thumbs/312x312/uploads/agents/na.png" alt=""></span>';
                                            echo '<a class="token__label" href="' . URL_BACKEND . 'agents/agent/summary/?id=' . $listing['agent']['id'] . '"><span class="ttl">' . Format::htmlspecialchars($listing['agent']['name']) . '</span></a>';
                                            echo '</span>';
                                        } else {
                                            echo '<span class="token">';
                                            echo '<span class="token__thumb thumb thumb--tiny"><img width="24" height="24" src="/thumbs/312x312/uploads/agents/na.png" alt=""></span>';
                                            echo '<span class="token__label ttl">' . Format::htmlspecialchars($listing['agent']['name']) . '</span>';
                                            echo '</span>';
                                        }
                                        echo '</div>';

                                    // Recommended by ISA
                                    } else if (!empty($listing['associate'])) {

                                        echo '<div class="v groups">';
                                        if ($authuser->isSuperAdmin()) {
                                            echo '<span class="token">';
                                            echo '<span class="token__thumb thumb thumb--tiny"><img width="24" height="24" src="/thumbs/312x312/uploads/agents/na.png" alt=""></span>';
                                            echo '<a class="token__label" href="' . URL_BACKEND . 'associates/associate/summary/?id=' . $listing['associate']['id'] . '"><span class="ttl">' . Format::htmlspecialchars($listing['associate']['name']) . '</span></a>';
                                            echo '</span>';
                                        } else {
                                            echo '<span class="token">';
                                            echo '<span class="token__thumb thumb thumb--tiny"><img width="24" height="24" src="/thumbs/312x312/uploads/agents/na.png" alt=""></span>';
                                            echo '<span class="token__label ttl">' . Format::htmlspecialchars($listing['associate']['name']) . '</span>';
                                            echo '</span>';
                                        }
                                        echo '</div>';

                                    }
                                }

                                ?>
        				</div>

                        <div class="btns R">
                            <?php if ($leadAuth->canManageLead() && $filter !== 'viewed') { ?>

                            <a class="btn btn--ghost delete" href="?id=<?=$lead['id']; ?>&delete=<?=$listing['id']; ?>&type=<?=$filter; ?>" onclick="return confirm('Are you sure you want to remove this listing?');"><svg class="icon icon-trash mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use></svg></a>
                            <?php } ?>
                        </div>

				</div>
            </div>
		</li>
		<?php } ?>
		<?php } else { ?>
            <div class="block">
    			<?php if ($filter === 'dismissed') { ?>
    			No saved <?=Locale::spell('favorites'); ?> available.
    			<?php } else if ($filter === 'recommended') { ?>
    			No recommended listings available.
    			<?php } else if ($filter === 'dismissed') { ?>
    			No dismissed listings available.</div>
    			<?php } else if ($filter === 'viewed') { ?>
    			No recently viewed listings available.
    			<?php } ?>
			</div>

		<?php } ?>

	</ul>
    <?php

        // Display pagination links
        if (!empty($pagination['links'])) {
            echo '<div class="rewui nav_pagination">';
            if (!empty($pagination['prev'])) echo '<a href="' . $pagination['prev']['url'] . '" class="prev">&lt;&lt;</a>';
            if (!empty($pagination['links'])) {
                foreach ($pagination['links'] as $link) {
                    echo '<a href="' . $link['url'] . '"' . (!empty($link['active']) ? ' class="current"' : '') . '>' . $link['link'] . '</a>';
                }
            }
            if (!empty($pagination['next'])) echo '<a href="' . $pagination['next']['url'] . '" class="next">&gt;&gt;</a>';
            echo '</div>';
        }

    ?>
</section>