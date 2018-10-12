<?php if (empty($office)) { ?>
	<div class="msg negative">
		<p>We're sorry, but the office you were looking for could not be found.</p>
	</div>

<?php } else { ?>

	<div class="office <?=(!empty($class) ? ' ' . $class : ''); ?>">

		<h1>
			<?=$office['title']; ?>
			<small><?=$office['location']; ?></small>
		</h1>

		<div class="body colset">
			<div class="photo col width-1/1-sm width-1/2-md width-2/3-lg width-2/3-xl">
				<span><img data-src="<?=$office['image']; ?>" src="<?=$office_placeholder; ?>" alt=""></span>
			</div>
			<div class="details col width-1/1-sm width-1/2-md width-1/3-lg width-1/3-xl">
				<p class="description"><?=$office['description']; ?></p>
				<?php if (!empty($office['phone']) || !empty($office['fax']) || !empty($office['email'])) { ?>
					<div class="keyvalset tableStyle">
						<h4>Contact Information</h4>
						<ul>
							<?php if (!empty($office['phone'])) { ?>
								<li class="keyval phone"><strong class="key">Phone #</strong> <span class="val"><?=$office['phone']; ?></span></li>
							<?php } ?>
							<?php if (!empty($office['fax'])) { ?>
								<li class="keyval fax"><strong>Fax #</strong> <span><?=$office['fax']; ?></span></li>
							<?php } ?>
							<?php if (!empty($office['email'])) { ?>
								<li class="keyval email"><strong>Email</strong> <span><a href="mailto:<?=$office['email']; ?>"><?=$office['email']; ?></a></span></li>
							<?php } ?>
						</ul>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>

	<?php if (!empty($office['agents'])) { ?>
			<hr><h2>Agents in this Office</h2>
			<div class="colset colset-1-sm colset-1-md colset-3-lg colset-3-xl agents<?=(!empty($class) ? ' ' . $class : ''); ?>">
			<?php foreach ($office['agents'] as $agent) { ?>
				<article class="agent col">
				    <div class="body">
						<div class="photo ratio-4/3">
							<?php if (!empty($agent['link'])) { ?><a href="/agents/<?=$agent['link']; ?>/"><?php } ?>
							<img data-src="<?=$agent['image']; ?>" src="<?=$placeholder; ?>" alt="">
							<?php if (!empty($agent['link'])) { ?></a><?php } ?>
						</div>
						<div class="details">
							<h4><?=Format::htmlspecialchars($agent['name']) . (!empty($agent['title']) ? '<span class="tween">,</span> <small>' . Format::htmlspecialchars($agent['title']) . '</small>' : ''); ?></h4>
							<?php if (!empty($agent['link'])) { ?>
								<div class="btnset">
									<a class="btn strong" href="/agents/<?=$agent['link']; ?>/">Read More <i class="icon-chevron-right"></i></a>
								</div>
							<?php } ?>
						</div>
				    </div>
				</article>
			<?php } ?>
		</div>
	<?php } ?>

<?php } ?>

<div class="btnset">
	<a class="btn" href="/offices.php">View All Offices</a>
</div>