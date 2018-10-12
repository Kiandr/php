<?php if (empty($office)) { ?>
	<div class="msg negative">
		<p>We're sorry, but the office you were looking for could not be found.</p>
	</div>

<?php } else { ?>

	<div class="office detailed<?=(!empty($class) ? ' ' . $class : ''); ?>">

		<h1><?=$office['title']; ?></h1>

		<div class="body">

			<div class="photo">
				<span><img data-src="<?=$office['image']; ?>" src="<?=$office_placeholder; ?>" alt=""></span>
			</div>

			<div class="details">

				<p class="description"><?=$office['description']; ?></p>

				<ul class="keyvalset">
					<?php if (!empty($office['location'])) { ?>
						<li class="keyval location"><strong>Address</strong> <span> <?=$office['location']; ?></span></li>
					<?php } ?>
					<?php if (!empty($office['phone'])) { ?>
						<li class="keyval phone"><strong>Phone #</strong> <span><?=$office['phone']; ?></span></li>
					<?php } ?>
					<?php if (!empty($office['fax'])) { ?>
						<li class="keyval fax"><strong>Fax #</strong> <span><?=$office['fax']; ?></span></li>
					<?php } ?>
					<?php if (!empty($office['email'])) { ?>
						<li class="keyval email"><strong>Email</strong> <span><a href="mailto:<?=$office['email']; ?>"><?=$office['email']; ?></a></span></li>
					<?php } ?>
				</ul>

			</div>

		</div>

	</div>

	<?php if (!empty($office['agents'])) { ?>
		<div class="articleset agents">
			<h2>Agents in this Office</h2>

			<?php foreach ($office['agents'] as $agent) { ?>
				<article>

					<header>
						<h4><?=$agent['name']; ?></h4>
					</header>

					<div class="body">

						<div class="photo">
							<a href="/agents/<?=$agent['link']; ?>/">
								<img data-resize='{ "ratio" : "1:1" }' data-src="<?=$agent['image']; ?>" src="<?=$agent_placeholder; ?>" alt="">
							</a>
						</div>

						<div class="details">

							<p class="description"><?=Format::truncate($agent['remarks'], 100); ?></p>

							<ul class="keyvalset">
								<?php if (!empty($agent['title'])) { ?>
									<li class="keyval title"><strong>Title</strong> <span> <?=$agent['title']; ?></span></li>
								<?php } ?>
								<?php if (!empty($agent['email'])) { ?>
									<li class="keyval email"><strong>Email</strong> <span><a href="mailto:<?=$agent['email']; ?>"><?=$agent['email']; ?></a></span></li>
								<?php } ?>
							</ul>
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