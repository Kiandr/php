<?php

// Display vars
$nothing = '';

// Filter by Name
if (!empty($_GET['search_aname'])) {
	$nothing = '<div class="msg negative"><p>We\'re sorry, but there are no agents found matching your search.</p></div>';

// Filter by Letter
} elseif (!empty($_GET['letter'])) {
	$nothing = '<div class="msg negative"><p>We\'re sorry, but there are no agents found matching your search.</p></div>';

// Filter by Office
} elseif (!empty($_GET['office']) && !empty($office)) {
	$nothing = '<div class="msg negative"><p>We\'re sorry, but there are currently no agents available at our ' . Format::htmlspecialchars($office['title']) . ' office.</p></div>';

}

// DB connection
$db = DB::get();

// Load office list
$offices = $db->fetchAll("SELECT `id`, `title` FROM `featured_offices` WHERE `display` = 'Y' ORDER BY `title` ASC;");

// Path to Scheme Folder
$schemeUrl = $this->getContainer()->getPage()->getSkin()->getSchemeUrl();

// Search by agent name
$search_keywords = array_filter(Format::trim(explode(',', $_GET['search_aname'])));

// Replace search keywords
$replace_keywords = !empty($search_keywords) ? '#' . implode('|', array_map(function ($search_keyword) {
	return preg_quote($search_keyword);
}, $search_keywords)) . '#i' : false;

?>
<div id="sub-feature">
	<div id="sub-quicksearch">
		<form id="agent-form" method="get">
			<div class="s-inputs">
				<div class="ac-input">
					<input name="search_aname" placeholder="Search by Agent's Name...">
				</div>
				<nav class="nav-dropdowns" role="navigation">
					<ul class="options">
						<?php if (!empty($offices) && count($offices) > 1) { ?>
							<li>
								<span class="dropdown-title">by Office <i class="icon-chevron-down"></i></span>
								<fieldset class="dropdown">
									<label><input id="office-all" type="radio" name="office" value="" checked> All</label>
									<?php foreach ($offices as $option) { ?>
										<label><input type="radio" name="office" value="<?=$option['id']; ?>"> <?=Format::htmlspecialchars($option['title']); ?></label>
									<?php } ?>
								</fieldset>
							</li>
						<?php } ?>
						<li>
							<span class="dropdown-title">by Last Name <i class="icon-chevron-down"></i></span>
							<fieldset class="dropdown">
								<?php

									// Show Alpha Bar
									if (!empty($letters)) {
										echo '<a rel="nofollow" href="' . Http_Uri::getUri() . '"' . (empty($_GET['letter']) ? ' class="current"' : '') . '>All</a>';
										foreach ($letters as $letter) {
											echo '<a rel="nofollow" href="?letter=' . $letter . '"' . ($letter == $_GET['letter'] ? ' class="current"' : '') . '>' . $letter . '</a>';
										}
									}

								?>
							</fieldset>
						</li>
						<li class="search-submit">
							<button class="search-button" type="submit">
								<i class="icon-searchGlass"></i>
							</button>
						</li>
					</ul>
				</nav>
			</div>
			<div class="search-criteria">
				<?php if (!empty($search_keywords)) { ?>
					<?php foreach ($search_keywords as $search_keyword) { ?>
						<a href="<?=Http_Uri::getUri() . '?search_aname=' . implode(',', array_diff($search_keywords, array($search_keyword))); ?>" class="buttonstyle mini icon-close">Agent Name: "<?=Format::htmlspecialchars($search_keyword); ?>"</a>
					<?php } ?>
				<?php } ?>
				<?php if (!empty($_GET['letter'])) { ?>
					<a href="<?=Http_Uri::getUri(); ?>" class="buttonstyle mini icon-close">By Letter: <?=Format::htmlspecialchars($_GET['letter']); ?></a>
				<?php } ?>
				<?php if (!empty($_GET['office']) && !empty($office)) { ?>
					<a href="<?=Http_Uri::getUri(); ?>" class="buttonstyle mini icon-close">By Office: <?=Format::htmlspecialchars($office['title']); ?></a>
				<?php } ?>
			</div>
		</form>
	</div>
</div>

<?php if (!empty($agents)) { ?>
	<div class="colset colset-1-sm colset-1-md colset-3-lg colset-3-xl agents<?=(!empty($class) ? ' ' . $class : ''); ?>">
		<?php foreach ($agents as $agent) { ?>
			<article class="agent col">
			    <div class="body">
					<div class="photo ratio-4/3">
						<?php if (!empty($agent['link'])) { ?><a href="<?=$agent['link']; ?>"><?php } ?>
						<img data-src="<?=$agent['image']; ?>" src="<?=$placeholder; ?>" alt="">
						<?php if (!empty($agent['link'])) { ?></a><?php } ?>
					</div>
					<div class="details">
						<h4>
							<?=$replace_keywords ? preg_replace($replace_keywords, '<mark>\\0</mark>', Format::htmlspecialchars($agent['name'])) : Format::htmlspecialchars($agent['name']); ?>
							<?=!empty($agent['title']) ? '<small>' . Format::htmlspecialchars($agent['title']) . '</small>' : ''; ?>
						</h4>
						<?php if (!empty($agent['link'])) { ?>
							<div class="btnset">
								<a class="btn strong" href="<?=$agent['link']; ?>">Read More <i class="icon-chevron-right"></i></a>
							</div>
						<?php } ?>
					</div>
			    </div>
			</article>
		<?php } ?>
	</div>
<?php } else { ?>
	<?=$nothing; ?>
<?php } ?>