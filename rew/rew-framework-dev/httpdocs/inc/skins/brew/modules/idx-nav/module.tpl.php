<?php foreach ($navigation as $nav) { ?>
	<div class="module nav">
		<header><h4><?=$nav['title']; ?></h4></header>
		<ul class="nav">
			<?php foreach ($nav['pages'] as $page) { ?>
				<li<?=(strstr($page['link'], Http_Uri::getUri()) == Http_Uri::getUri() ? ' class="current"' : ''); ?>>
					<a href="<?=$page['link']; ?>" title="<?=$page['title']; ?>"
						<?=!empty($page['class']) ? ' class="' . $page['class'] . '"' : ''; ?>
						<?=!empty($page['data-popup']) ? " data-popup='" . $page['data-popup'] . "'" : ''; ?>
						<?=!empty($page['target']) ? ' target="' . $page['target'] . '"' : ''; ?>><?=$page['title']; ?></a>
				</li>
			<?php } ?>
		</ul>
	</div>
<?php } ?>