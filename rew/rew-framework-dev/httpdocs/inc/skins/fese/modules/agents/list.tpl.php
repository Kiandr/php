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

    } else {
        echo "<h2>All Our Agents</h2>";
    }

    // Show Alpha Bar
    if (!empty($letters)) {
        echo '<div class="navbar marB">';
        echo '<a rel="nofollow" href="' . Http_Uri::getUri() . '"' . (empty($_GET['letter']) ? ' class="current"' : '') . '>All</a>';
        foreach ($letters as $letter) {
            echo '<a rel="nofollow" href="?letter=' . $letter . '"' . ($letter == $_GET['letter'] ? ' class="current"' : '') . '>' . $letter . '</a>';
        }
        echo '</div>';
    }

?>
    <div class="cols">
        <?php foreach ($agents as $agent) { ?>
            <div class="col img img--cover w1/4 w1/2-md w1/1-sm h1/1">
                <?php if (!empty($agent['link'])) { ?><a href="<?=$agent['link']; ?>"><?php } ?>
                    <img data-src="<?=$agent['image']; ?>" src="<?=$placeholder; ?>" alt="">
                <?php if (!empty($agent['link'])) { ?></a><?php } ?>
                <div class="cpt"><?=Format::htmlspecialchars($agent['name']); ?></div>
            </div>
        <?php } ?>
    </div>
<?php

}