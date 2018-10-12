<?php if (!empty($offices)) { ?>
	<div class="module articleset offices<?=(!empty($class) ? ' ' . $class : ''); ?>">
		<?php foreach ($offices as $office) { ?>
			<article>

				<header>
					<h4><?=$office['title']; ?></h4>
				</header>

				<div class="body">

					<div class="photo">
						<a href="/offices.php?oid=<?=$office['id']; ?>">
							<img data-src="<?=$office['image']; ?>" src="<?=$office_placeholder; ?>" alt="">
						</a>
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

				<div class="btnset">
					<a class="btn strong" href="/offices.php?oid=<?=$office['id']; ?>">Read More <i class="icon-chevron-right"></i></a>
					<?php if (!empty($link)) { ?>
						<a class="btn" href="/offices.php"><?=$link; ?> <i class="icon-chevron-right"></i></a>
					<?php } ?>
				</div>

			</article>
		<?php } ?>
	</div>
<?php } ?>