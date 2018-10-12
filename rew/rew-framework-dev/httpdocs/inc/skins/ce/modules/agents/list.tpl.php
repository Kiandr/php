<?php

// Require Agents
if (!empty($agents)) {

    // override thumb size


    // Display title bar
    if ($this->config['title'] !== false) {

        // Filter by Name
        if (!empty($_POST['search_fname']) || !empty($_POST['search_lname'])) {
            echo '<h2>All Agents whose name is like "' . Format::htmlspecialchars($_POST['search_fname'] . ' ' . $_POST['search_lname']) . '".</h2>';

            // Filter by Letter
        } elseif (!empty($_GET['letter'])) {
            echo '<div class="divider -pad-vertical"><span class="divider__label -left -text-upper -text-xs">Agents that start with the letter "' . Format::htmlspecialchars($_GET['letter']) . '".</span></div>';

            // Filter by Office
        } elseif (!empty($_GET['office']) && !empty($office)) {
            echo '<div class="divider -pad-vertical"><span class="divider__label -left -text-upper -text-xs">Agents at our ' . Format::htmlspecialchars($office['title']) . ' office</span></div>';

        } else {
            echo "<div class='divider -pad-vertical'><span class='divider__label -left -text-upper -text-xs'>All Our Agents</span></div>";
        }

        // Show Alpha Bar
        if (!empty($letters) && count($agents) > 12) {
            echo '<div class="pagination agent--pagination -mar-bottom">';
            echo '<a rel="nofollow" href="' . Http_Uri::getUri() . '"' . (empty($_GET['letter']) ? ' class="current"' : '') . '>All</a>';
            foreach ($letters as $letter) {
                echo '<a rel="nofollow" href="?letter=' . $letter . '"' . ($letter == $_GET['letter'] ? ' class="current"' : '') . '>' . $letter . '</a>';
            }
            echo '</div>';
        }

    }

?>
    <div class="columns">
        <?php foreach ($agents as $agent) { ?>
            <?=sprintf('<%s class="hero hero--portrait column -width-1/4 -width-1/2@md -width-1/1@sm -width-1/1@xs">', (!empty($agent['link']) ? 'a href="' . htmlspecialchars($agent['link']) . '"' : 'div')); ?>
			<div class="hero__fg">
				<?php if (!empty($agent['title'])) { ?>
				<div class="hero__head">
					<div class="divider">
						<span class="divider__label -left -text-upper -text-xs"><?=Format::htmlspecialchars($agent['title']); ?></span>
					</div>
				</div>
				<?php } ?>
				<div class="hero__body -flex">
					<div class="-bottom">
					    <h2 class="-text-upper"><?=Format::htmlspecialchars($agent['name']); ?></h2>
                        <p class="-text-xs -text-upper"><?= $agent['remarks']; ?></p>
					</div>
				</div>
			</div>
			<div class="hero__bg">
				<div class="cloak cloak--dusk"></div>
				<img class="agent__img hero__bg-content" data-src="<?=str_replace('thumbs/275x275/r/','thumbs/428x428/r/', $agent['image']);?>" data-srcset="<?=str_replace('thumbs/275x275/r/','thumbs/856x856/r/', $agent['image']);?> 2x"
				    src="<?=$placeholder; ?>"
				    alt="Photo of <?=Format::htmlspecialchars($agent['name']); ?>">
			</div>
            <?=(!empty($agent['link']) ? '</a>' : '</div>'); ?>
        <?php } ?>
    </div>
<?php

}