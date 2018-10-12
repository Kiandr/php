<?php if (empty($agent)) { ?>

    <h1>Agent Not Found</h1>
    <p>We're sorry, but the agent you were looking for could not be found.</p>
    <p><a class="uk-button uk-button-large" href="/agents.php">Return to Agents</a></p>

<?php } else { ?>
    
<article class="uk-article">
    <h1 class="uk-article-title"><?=Format::htmlspecialchars($agent['name']) . (!empty($agent['title']) ? ' - <small>' . Format::htmlspecialchars($agent['title']) . '</small>' : ''); ?></h1>

    <div class="uk-grid">
        <div class="uk-width-small-1-1 uk-width-medium-1-4">
            <?php if (!empty($agent['link'])) { ?><a href="<?=$agent['link']; ?>" class="uk-thumbnail"><?php } ?>
            <img data-resize='{ "ratio" : "1:1" }' data-src="<?=$agent['image']; ?>" src="<?=$placeholder; ?>" alt="">
            <?php if (!empty($agent['link'])) { ?></a><?php } ?>
        </div>
        <div class="uk-width-3-4">
            <p class="uk-article-lead"><?=$agent['remarks']; ?></p>
                                
            <?php if (!empty($agent['office'])) { ?>
                <p>
                    <?php $office = $agent['office']; ?>
                    <a href="/offices.php?oid=<?=$office['id']; ?>" class="uk-text-bold"><?=Format::htmlspecialchars($office['title']); ?></a><br>
                    <?php if (!empty($office['location'])) { ?>
                        <span><?=Format::htmlspecialchars($office['location']); ?></span>
                    <?php } ?>
                </p>
            <?php } ?>

            <ul class="uk-list uk-list-line uk-list-space">
                <?php if (!empty($agent['office_phone'])) { ?>
                    <li><strong>Office</strong>:	<a href="tel:<?=Format::htmlspecialchars($agent['office_phone']); ?>"><?=Format::htmlspecialchars($agent['office_phone']); ?></a></li>
                <?php } ?>
                <?php if (!empty($agent['cell_phone'])) { ?>
                    <li><strong>Cell</strong>: <a href="tel:<?=Format::htmlspecialchars($agent['cell_phone']); ?>"><?=Format::htmlspecialchars($agent['cell_phone']); ?></a></li>
                <?php } ?>
                <?php if (!empty($agent['home_phone'])) { ?>
                    <li><strong>Home</strong>: <?=Format::htmlspecialchars($agent['home_phone']); ?></li>
                <?php } ?>
                <?php if (!empty($agent['fax'])) { ?>
                    <li><strong>Fax</strong>: <?=Format::htmlspecialchars($agent['fax']); ?></li>
                <?php } ?>
                <?php if (!empty($agent['email'])) { ?>
                    <li><strong>Email</strong>: <a href="mailto:<?=$agent['email']; ?>"><?=$agent['email']; ?></a></li>
                <?php } ?>
                <?php if (!empty($agent['website'])) { ?>
                    <li><strong>Website</strong>: <a href="<?=Format::htmlspecialchars($agent['website']); ?>" target="_blank"><?=Format::htmlspecialchars($agent['website']); ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <?php if (!empty($agent['link'])) { ?>
        <p><a class="uk-button uk-margin-bottom" href="/agents.php"><i class="uk-icon uk-icon-chevron-left"></i> Return to Agents</a></p>
    <?php } ?>
    </article>
    
<?php

    // Agent's Listings
    if (!empty($listings)) {
        echo '<div class="agents-listings">';
        echo '<h2>' . Format::htmlspecialchars($agent['name']) . '\'s ' . Lang::write('MLS') . ' Listings</h2>';
        echo $listings;
        echo '</div>';
    }

}

?>
