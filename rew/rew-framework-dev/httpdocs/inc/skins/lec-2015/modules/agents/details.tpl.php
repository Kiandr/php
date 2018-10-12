<?php if (empty($agent)) { ?>

	<div class="msg negative">
		<p>We're sorry, but the agent you were looking for could not be found.</p>
	</div>

	<hr class="spacer clear">

	<p><a class="btn" href="/agents.php">View All Agents</a></p>

<?php } else { ?>

	<h1>
		<?=Format::htmlspecialchars($agent['name']); ?>
		<?=(!empty($agent['title']) ? '<small>' . Format::htmlspecialchars($agent['title']) . '</small>' : ''); ?>
	</h1>

	<div class="colset">
		<div class="photo col width-1/1-sm width-1/2-md width-1/3-lg width-1/3-xl">
			<img data-src="<?=$agent['image']; ?>" src="<?=$placeholder; ?>" alt="">
		</div>
		<div class="details col width-1/1-sm width-1/2-md width-2/3-lg width-2/3-xl">
			<section class="description-cut description">
				<?php if (!empty($agent['remarks'])) { ?>
					<?=$agent['remarks']; ?>
				<?php } ?>
			</section>
			<div class="keyvalset tableStyle">
				<h4>Contact Information</h4>
				<ul>
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

	<div class="tabset tabset--behaviour">
		<ul>
			<?php if (!empty($listings)) { ?>
				<li><a href="#pane--listings">Current Listings</a></li>
			<?php } ?>
			<?php if (!empty($agent['testimonials'])) { ?>
				<li><a href="#pane--testimonials">Testimonials</a></li>
			<?php } ?>
		</ul>
	</div>

	<br>

	<div>
		<?php if (!empty($listings)) { ?>
			<div id="pane--listings" class="hidden">
				<div class="agents-listings">
					<?=$listings; ?>
				</div>
			</div>
		<?php } ?>
		<?php if (!empty($agent['testimonials'])) { ?>
			<div id="pane--testimonials" class="hidden">
				<div class="agent-testimonials">
					<?php foreach ($agent['testimonials'] as $testimonial) { ?>
						<blockquote>
							<p class="quote"><?=$testimonial['testimonial']; ?></p>
							<?php if (!empty($testimonial['client'])) { ?>
								<small><?=Format::stripTags($testimonial['client']); ?></small>
							<?php } ?>
						</blockquote>
					<?php } ?>
				</div>
			</div>
		<?php } ?>
	</div>

	<a class="btn" href="/agents.php">View All Agents</a>
	<hr class="spacer clear">

<?php } ?>