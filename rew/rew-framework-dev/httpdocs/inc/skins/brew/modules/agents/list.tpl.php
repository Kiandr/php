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

	// Show Alpha Bar
	if (!empty($letters)) {
		echo '<div class="alpha">';
		echo '<a rel="nofollow" href="' . Http_Uri::getUri() . '"' . (empty($_GET['letter']) ? ' class="current"' : '') . '>All</a>';
		foreach ($letters as $letter) {
			echo '<a rel="nofollow" href="?letter=' . $letter . '"' . ($letter == $_GET['letter'] ? ' class="current"' : '') . '>' . $letter . '</a>';
		}
		echo '</div>';
	}

?>

	<div class="module articleset agents<?=(!empty($class) ? ' ' . $class : ''); ?>">
		<?php foreach ($agents as $agent) { ?>
			<article>

				<header>
					<h4><?=Format::htmlspecialchars($agent['name']) . (!empty($agent['title']) ? '<span class="tween">,</span> <em>' . Format::htmlspecialchars($agent['title']) . '</em>' : ''); ?></h4>
				</header>

				<div class="body">

					<div class="photo">
						<?php if (!empty($agent['link'])) { ?><a href="<?=$agent['link']; ?>"><?php } ?>
						<img data-resize='{ "ratio" : "1:1" }' data-src="<?=$agent['image']; ?>" src="<?=$placeholder; ?>" alt="">
						<?php if (!empty($agent['link'])) { ?></a><?php } ?>
					</div>

					<div class="details">

						<p class="description"><?=$agent['remarks']; ?></p>

						<ul class="keyvalset">
							<?php if (!empty($agent['office'])) { ?>
								<?php $office = $agent['office']; ?>
								<li class="keyval office"><strong>Office</strong> <span><a href="/offices.php?oid=<?=$office['id']; ?>"><?=Format::htmlspecialchars($office['title']); ?></a></span></li>
								<?php if (!empty($office['location'])) { ?>
									<li class="keyval location"><strong>Office Location</strong> <span><?=Format::htmlspecialchars($office['location']); ?></span></li>
								<?php } ?>
							<?php } ?>
							<?php if (!empty($agent['office_phone'])) { ?>
								<li class="keyval officephone"><strong>Office #</strong> <span><?=Format::htmlspecialchars($agent['office_phone']); ?></span></li>
							<?php } ?>
							<?php if (!empty($agent['cell_phone'])) { ?>
								<li class="keyval cellphone"><strong>Cell #</strong> <span><?=Format::htmlspecialchars($agent['cell_phone']); ?></span></li>
							<?php } ?>
							<?php if (!empty($agent['home_phone'])) { ?>
								<li class="keyval homephone"><strong>Home #</strong> <span><?=Format::htmlspecialchars($agent['home_phone']); ?></span></li>
							<?php } ?>
							<?php if (!empty($agent['fax'])) { ?>
								<li class="keyval fax"><strong>Fax #</strong> <span><?=Format::htmlspecialchars($agent['fax']); ?></span></li>
							<?php } ?>
							<?php if (!empty($agent['email'])) { ?>
								<li class="keyval email"><strong>Email</strong> <span><a href="mailto:<?=$agent['email']; ?>"><?=$agent['email']; ?></a></span></li>
							<?php } ?>
							<?php if (!empty($agent['website'])) { ?>
								<li class="keyval website"><strong>Website</strong> <span><a href="<?=Format::htmlspecialchars($agent['website']); ?>" target="_blank"><?=Format::htmlspecialchars($agent['website']); ?></a></span></li>
							<?php } ?>
						</ul>

					</div>

				</div>

				<?php if (!empty($agent['link'])) { ?>
					<div class="btnset">
						<a class="btn strong" href="<?=$agent['link']; ?>">Read More <i class="icon-chevron-right"></i></a>
					</div>
				<?php } ?>

			</article>
		<?php } ?>
	</div>
<?php

}