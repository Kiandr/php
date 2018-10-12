<div class="menu menu--drop hidden" id="menu--filters">
    <ul class="menu__list">
    	<li class="menu__item"><a class="menu__link" href="/backend/blog/comments/?filter=all"><?= __('All'); ?></a></li>
    	<li class="menu__item"><a class="menu__link" href="/backend/blog/comments/?filter=pending"><?= __('Pending'); ?></a></li>
    	<li class="menu__item"><a class="menu__link" href="/backend/blog/comments/?filter=published"><?= __('Published'); ?></a></li>
    	<li class="menu__item current"><a class="menu__link current" href="/backend/blog/pings/"><?= __('Pingbacks'); ?></a></li>
    </ul>
</div>

<div class="bar">
    <a href="#" data-drop="#menu--filters" class="bar__title"><?= __('Pingbacks'); ?> <svg class="icon icon-drop"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-drop"></use></svg></a>
    <div class="bar__actions">
		<button type="button" class="bar__action" id="btn-publish" disabled><?= __('Publish'); ?></button>
		<button type="button" class="bar__action" id="btn-delete" disabled><svg class="icon icon-trash mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use></svg></button>
    </div>
</div>

<div class="block">

<ul class="tabs">
	<li class="<?=($_GET['filter'] == 'all')       ? ' current' : ''; ?>"><a href="?filter=all"><?= __('All'); ?></a></li>
	<li class="<?=($_GET['filter'] == 'pending')   ? ' current' : ''; ?>"><a href="?filter=pending"><?= __('Pending'); ?></a></li>
	<li class="<?=($_GET['filter'] == 'published') ? ' current' : ''; ?>"><a href="?filter=published"><?= __('Published'); ?></a></li>
</ul>


<form action="?submit" method="post">
	<input type="hidden" name="filter" value="<?=htmlspecialchars($_GET['filter']); ?>">
	<input type="hidden" name="action" value="">
	<?php if (!empty($pings)) : ?>
	<table class="item_content_summaries">
		<thead>
			<tr>
				<th width="10" class="control"><input type="checkbox" id="check_all"></th>
				<th><?= __('Blog Entry'); ?></th>
				<?php if ($blogAuth->canManagePings($authuser)) : ?>
				<th><?= __('Blog Author'); ?></th>
				<?php endif; ?>
				<th><?= __('Pingback Website'); ?></th>
				<th><?= __('Pingback Details'); ?></th>
				<th><?= __('Date Received'); ?></th>
				<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($pings as $ping) : ?>
			<tr>
				<td nowrap="nowrap" class="control"><input type="checkbox" name="pings[]" value="<?=$ping['id']; ?>" class="check"></td>
				<td><a href="<?=sprintf(URL_BLOG_ENTRY, $ping['entry']['link']); ?>" title="<?=substr(trim(strip_tags($ping['entry']['body'])), 0, 100) . '... '; ?>">
					<?=htmlspecialchars(substr($ping['entry']['title'], 0, 45)) . '...'; ?>
					</a></td>
				<?php if ($blogAuth->canManagePings($authuser)) : ?>
				<td><a href="<?=URL_BACKEND; ?>agents/summary/?id=<?=$ping['author']['id']; ?>">
					<?=htmlspecialchars($ping['author']['name']); ?>
					</a></td>
				<?php endif; ?>
				<td><a href="<?=htmlspecialchars($ping['website']); ?>" target="_blank">
					<?=htmlspecialchars($ping['website']); ?>
					</a></td>
				<td><?='[...] ' . htmlspecialchars($ping['excerpt']) . '[...]'; ?></td>
				<td><time datetime="<?=date('c', $ping['date']); ?>" title="<?=date('l, F jS Y \@ g:ia', $ping['date']); ?>">
						<?=Format::dateRelative($ping['date']); ?>
					</time></td>
				<td class="actions compact"><?php if ($ping['published'] == 'false') : ?>
					<a class="btn publish" href="?filter=<?=$_GET['filter']; ?>&publish=<?=$ping['id']; ?>" onclick="return confirm('Are you sure you want to approve this blog pingback?');"><?= __('Publish'); ?></a>
					<?php endif; ?>
					<a class="btn delete" href="?filter=<?=$_GET['filter']; ?>&delete=<?=$ping['id']; ?>" onclick="return confirm('Are you sure you want to delete this blog pingback?');"><?= __('Delete'); ?></a></td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php if (!empty($pagination['links'])) : ?>
	<div class="rewui nav_pagination">
		<?php if (!empty($pagination['prev'])) : ?>
		<a href="<?=$pagination['prev']['url']; ?>" class="prev">&lt;&lt;</a>
		<?php endif; ?>
		<?php if (!empty($pagination['links'])) : ?>
		<?php foreach ($pagination['links'] as $link) : ?>
		<a href="<?=$link['url']; ?>"<?=!empty($link['active']) ? ' class="current"' : ''; ?>>
		<?=$link['link']; ?>
		</a>
		<?php endforeach; ?>
		<?php endif; ?>
		<?php if (!empty($pagination['next'])) : ?>
		<a href="<?=$pagination['next']['url']; ?>" class="next">&gt;&gt;</a>
		<?php endif; ?>
	</div>
	<?php endif; ?>
	<?php else: ?>
	<p class="block"><?= __('There are currently no %s blog pingbacks.',($_GET['filter'] !== 'all' ? htmlspecialchars($_GET['filter']) : '')); ?></p>
	<?php endif; ?>

    </div>

</form>