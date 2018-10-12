<?php if (!empty($offices)) { ?>
	<div class="colset equal-heights colset-1-sm colset-2-md colset-3-lg colset-3-xl offices<?=(!empty($class) ? ' ' . $class : ''); ?>">
		<?php foreach ($offices as $office) { ?>
			<article class="col office">
				<div class="body">
					<div class="photo ratio-3/4">
						<a href="/offices.php?oid=<?=$office['id']; ?>">
							<img data-src="<?=$office['image']; ?>" src="<?=$office_placeholder; ?>" alt="">
						</a>
					</div>
					<div class="details">
						<h4><?=Format::htmlspecialchars($office['title']); ?></h4>
						<p><?php if (!empty($office['location'])) { ?>
							<?=Format::htmlspecialchars($office['location']); ?><br>
						<?php } ?>
						<?php if (!empty($office['phone'])) { ?>
							<?=Format::htmlspecialchars($office['phone']); ?><br>
						<?php } ?></p>
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