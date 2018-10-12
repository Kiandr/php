<?php foreach ($navigation as $nav) { ?>
<div class="nav nav--stacked">
	<h4 class="nav__heading -pad-horizontal -text-upper"><?=$nav['title']; ?></h4>
	<ul class="nav__list -mar-bottom">
		<?php foreach ($nav['pages'] as $page) { ?>
			<li class="nav__item -text-upper<?=(strstr($page['link'], Http_Uri::getUri()) == Http_Uri::getUri() ? ' -is-current' : ''); ?>"><a class="nav__link" href="<?=$page['link']; ?>"<?=!empty($page['target']) ? ' target="' . $page['target'] . '"' : ''; ?>><?=Format::htmlspecialchars($page['title']); ?></a>
				<?php if (!empty($page['subpages'])) { ?>
					<ul class="nav__list -mar-left">
						<?php foreach ($page['subpages'] as $subpage) { ?>
							<li class="nav__item -text-upper <?=(strstr($subpage['link'], Http_Uri::getUri()) == Http_Uri::getUri() ? ' -is-current' : ''); ?>""><a class="nav__link" href="<?=$subpage['link']; ?>"<?=!empty($subpage['target']) ? ' target="' . $subpage['target'] . '"' : ''; ?>><?=Format::htmlspecialchars($subpage['title']); ?></a></li>
						<?php } ?>
					</ul>
				<?php } ?>
			</li>
		<?php } ?>
	</ul>
</div>
<div class="divider -mar-bottom"></div>
<?php } ?>