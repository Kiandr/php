<?php if (empty(Settings::getInstance()->IDX_FEEDS) || empty($feeds)) return; ?>

<?php if ($this->config['mode'] === 'inline') { ?>
	<div class="pagination">
		<?php
			$links = array();
			foreach ($feeds as $feed) {
				if ($feed['active']) {
					$links[] = '<a class="current" href="' . $feed['link'] . '">' . $feed['title'] . '</a>';
				} else {
					$links[] = '<a href="' . $feed['link'] . '">' . $feed['title'] . '</a>';
				}
			}
			echo implode(' ', $links);
		?>
	</div>
<?php } else { ?>
	<div class="module nav">
		<header><h4><?=$this->config['heading']; ?></h4></header>
		<ul class="nav">
			<?php foreach ($feeds as $feed) { ?>
				<li class="<?=$feed['active'] ? 'current' : '';?>">
					<a href="<?=$feed['link'];?>"><?=$feed['title'];?></a>
				</li>
			<?php } ?>
		</ul>
	</div>
<?php } ?>