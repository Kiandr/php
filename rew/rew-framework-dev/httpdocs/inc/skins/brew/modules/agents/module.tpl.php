<?php if (!empty($agents)) { ?>

	<div class="module articleset agents<?=(!empty($class) ? ' ' . $class : ''); ?>">
		<?php foreach ($agents as $agent) { ?>
			<article>

				<header>
					<h4><?=Format::htmlspecialchars($agent['name']) . (!empty($agent['title']) ? '<span class="tween">,</span> <em>' . Format::htmlspecialchars($agent['title']) . '</em>' : ''); ?></h4>
				</header>

				<div class="body">

					<div class="photo">
						<?php if (!empty($agent['link'])) { ?><a href="<?=$agent['link']; ?>"><?php } ?>
						<img data-src="<?=$agent['image']; ?>" src="<?=$placeholder; ?>" alt="">
						<?php if (!empty($agent['link'])) { ?></a><?php } ?>
					</div>

					<div class="details">

						<p class="description"><?=$agent['remarks']; ?></p>

						<ul class="keyvalset">
							<?php $office = $agent['office']; ?>
							<?php if (!empty($office['id'])) { ?>
								<li class="keyval office"><strong>Office</strong> <span><a href="/offices.php?oid=<?=$office['id']; ?>"><?=Format::htmlspecialchars($office['title']); ?></a></span></li>
								<?php if (!empty($office['location'])) { ?>
									<li class="keyval location"><strong>Office Location</strong> <span><?=Format::htmlspecialchars($office['location']); ?></span></li>
								<?php } ?>
							<?php } else{ ?>
								<li class="keyval office empty"><span>&nbsp;</span></li>
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
		<?php if (!empty($link)) { ?>
			<div class="btnset">
				<a class="btn" href="/agents.php"><?=$link; ?></a>
			</div>
		<?php } ?>
	</div>
<?php } ?>