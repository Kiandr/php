<?php

// Require Agents
if (!empty($agents)) {
    // Filter by Name
    if (!empty($_POST['search_fname']) || !empty($_POST['search_lname'])) {
        echo '<h2>All Agents whose name is like "' . Format::htmlspecialchars($_POST['search_fname'] . ' ' . $_POST['search_lname']) . '".</h2>';

    // Filter by Letter
    } elseif (!empty($_GET['letter'])) {
        echo '<h2>Agents that start with the letter "' . Format::htmlspecialchars($_GET['letter']) . '".</h2>';

    // Filter by Office
    } elseif (!empty($_GET['office']) && !empty($office)) {
        echo '<h2>Agents at our ' . Format::htmlspecialchars($office['title']) . ' office</h2>';
    }
?>

<div class="uk-margin-large-top uk-margin-large-bottom">
    <ul class="uk-pagination uk-pagination-left">
        <?php
            // Show Alpha Bar
            if (!empty($letters)) {
                echo '<li class="uk-text-uppercase uk-margin-right uk-text-large">Search by name</li>';
                echo '<li><a rel="nofollow" href="' . Http_Uri::getUri() . '"' . (empty($_GET['letter']) ? ' class="current"' : '') . '>All</a></li>';
                echo '<li><span>...</span></li>';
                foreach ($letters as $letter) {
                    echo '<li><a rel="nofollow" href="?letter=' . $letter . '"' . ($letter == $_GET['letter'] ? ' class="uk-active"' : '') . '>' . $letter . '</a></li>';
                }
            }
        ?>
    </ul>
</div>

<?php foreach ($agents as $agent) { ?>
    <article class="uk-article">
        <div class="uk-grid">
            <div class="uk-width-xsmall-1-1 uk-width-small-1-4">
                <?php if (!empty($agent['link'])) { ?><a href="<?= Format::htmlspecialchars($agent['link']); ?>" class="uk-thumbnail"><?php } ?>
                <img data-resize='{ "ratio" : "1:1" }' data-src="<?= Format::htmlspecialchars($agent['image']); ?>" src="<?= Format::htmlspecialchars($placeholder); ?>" alt="">
                <?php if (!empty($agent['link'])) { ?></a><?php } ?>
            </div>
            <div class="uk-width-3-4">
                <h1 class="uk-article-title"><?=Format::htmlspecialchars($agent['name']) . (!empty($agent['title']) ? ' - <small>' . Format::htmlspecialchars($agent['title']) . '</small>' : ''); ?></h1>
                <p class="uk-article-lead"><?= $agent['remarks']; ?></p>
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
                
                <?php if (!empty($agent['link'])) { ?>
                <a class="uk-button" href="<?=$agent['link']; ?>">Read More <i class="uk-icon uk-icon-chevron-right"></i></a>
                <?php } ?>
            </div>
        </div>
        <hr class="uk-article-divider">
    </article>
<?php } ?>
<?php

}
