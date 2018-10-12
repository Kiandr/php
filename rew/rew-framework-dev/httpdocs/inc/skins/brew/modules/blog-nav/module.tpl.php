<?php foreach ($navigation as $nav) { ?>
	<div class="module nav">
		<header><h4><?=$nav['title']; ?></h4></header>
		<ul class="nav">
			<?php foreach ($nav['pages'] as $page) { ?>
				<li<?=(strstr($page['link'], Http_Uri::getUri()) == Http_Uri::getUri() ? ' class="current"' : ''); ?>><a href="<?=$page['link']; ?>"<?=!empty($page['target']) ? ' target="' . $page['target'] . '"' : ''; ?> title="<?=Format::htmlspecialchars($page['title']); ?>"><?=Format::htmlspecialchars($page['title']); ?></a>
					<?php if (!empty($page['subpages'])) { ?>
						<ul>
							<?php foreach ($page['subpages'] as $subpage) { ?>
								<li<?=(strstr($subpage['link'], Http_Uri::getUri()) == Http_Uri::getUri() ? ' class="current"' : ''); ?>><a href="<?=$subpage['link']; ?>"<?=!empty($subpage['target']) ? ' target="' . $subpage['target'] . '"' : ''; ?> title="<?=Format::htmlspecialchars($subpage['title']); ?>"><?=Format::htmlspecialchars($subpage['title']); ?></a></li>
							<?php } ?>
						</ul>
					<?php } ?>
				</li>
			<?php } ?>
		</ul>
	</div>
<?php } ?>