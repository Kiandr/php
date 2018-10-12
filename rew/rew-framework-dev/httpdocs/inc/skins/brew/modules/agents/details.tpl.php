<?php if (empty($agent)) { ?>

	<div class="msg negative">
		<p>We're sorry, but the agent you were looking for could not be found.</p>
	</div>

<?php } else { ?>

	<div class="agent detailed<?=(!empty($class) ? ' ' . $class : ''); ?>">

		<h1><?=Format::htmlspecialchars($agent['name']) . (!empty($agent['title']) ? '<span class="tween">,</span> <small>' . Format::htmlspecialchars($agent['title']) . '</small>' : ''); ?></h1>

		<div class="body">

			<div class="photo">
				<span><img data-resize='{ "ratio" : "1:1" }' data-src="<?=$agent['image']; ?>" src="<?=$placeholder; ?>" alt=""></span>
			</div>

			<div class="details">

				<?php if (!empty($agent['remarks'])) { ?>
					<p class="description"><?=$agent['remarks']; ?></p>
				<?php } ?>

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

	</div>

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
<p><a class="btn" href="/agents.php">View All Agents</a></p>