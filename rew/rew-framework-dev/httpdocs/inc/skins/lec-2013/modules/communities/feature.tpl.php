<?php if (!empty($communities)) { ?>
	<div class="communities">
		<h2>Communities</h2>
		<div class="nav">
			<ul>
				<?php foreach ($communities as $community) { ?>
					<li>
						<?php if (!empty($community['url'])) echo '<a href="' . $community['url'] . '">'; ?>
							<div class="photo"><img src="<?=$community['image']; ?>" alt=""></div>
							<h4><?=Format::htmlspecialchars($community['title']); ?></h4>
							<span><?=Format::htmlspecialchars($community['subtitle']); ?></span>
						<?php if (!empty($community['url'])) echo '</a>'; ?>
					</li>
				<?php } ?>
			</ul>
			<a href="/communities.php">Browse All Communities</a>
		</div>
	</div>
<?php } ?>