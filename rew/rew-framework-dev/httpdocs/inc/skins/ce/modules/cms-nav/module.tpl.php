<?php foreach ($navigation as $nav) { ?>
<?php $current = (strstr($category['link'], Http_Uri::getUri()) == Http_Uri::getUri()); ?>
<li class="nav__item -text-upper<?=(!empty($current) ? ' -is-current' : ''); ?>"><a class="nav__link" href="<?=$category['link'];?>"><?=$category['title'];?></a></li>
<?php if (empty($nav['pages'])) continue; ?>
<?php foreach ($nav['pages'] as $page) { ?>
	<?php $current = (strstr($page['link'], Http_Uri::getUri()) == Http_Uri::getUri()) || ($page['link'] != '/' && strstr(Http_Uri::getUri(), str_replace('.php', '/', $page['link'])) !== false); ?>
	<li class="nav__item -text-upper<?=(!empty($current) ? ' -is-current' : ''); ?>"><a class="nav__link" href="<?=$page['link']; ?>"<?=!empty($page['target']) ? ' target="' . $page['target'] . '"' : ''; ?>><?=$page['title']; ?></a>
		<?php if (!empty($page['subpages'])) { ?>
		<ul class="dropdown">
			<?php foreach ($page['subpages'] as $subpage) { ?>
				<?php $current = (strstr($subpage['link'], Http_Uri::getUri()) == Http_Uri::getUri()) || ($page['link'] != '/' && strstr(Http_Uri::getUri(), str_replace('.php', '/', $subpage['link'])) !== false); ?>
				<li class="dropdown__item -text-upper<?=(!empty($current) ? ' -is-current' : ''); ?>"><a class="dropdown__link" href="<?=$subpage['link']; ?>"<?=!empty($subpage['target']) ? ' target="' . $subpage['target'] . '"' : ''; ?>><?=$subpage['title']; ?></a></li>
			<?php } ?>
		</ul>
		<?php } ?>
	</li>
<?php } ?>
<?php } ?>