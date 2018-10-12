<div class="block">
    <div class="bar -padL0 -padR0">
        <h1 class="bar__title mar0 -padL0 -padR0" style="font-weight: normal;">
            <?php if (!empty($application)) {
                echo htmlspecialchars($application['name']);
                echo __('API Requests');
            } else {
                echo __('API Request Log');
            } ?>
        </h1>
        <div class="bar__actions">
            <a href="/backend/settings/api/?back" class="bar__action">
                <svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg>
            </a>
        </div>
    </div>
<div class="btns btns--stickyB"> <span class="R">
	<?php if (!empty($count_requests['total'])) { ?>
	<a class="btn delete" href="?id=<?=htmlspecialchars($application['id']);?>&clear" onclick="return confirm('<?= __('Are you sure you want to clear the request log for this application?'); ?>');"><?= __('Clear Log'); ?></a>
	<?php } ?>
	</span> </div>
<?php if (!empty($requests)) { ?>
<p><?= __('Showing requests from the last 30 days'); ?></p>
<div class="table__wrap">
<table class="item_content_summaries table">
	<thead>
		<tr>
			<th align="left" class="sort_col"> <strong><?= __('Sort By:'); ?></strong> <a href="?<?=http_build_query(array_merge($query_string, array('order' => 'method', 'sort' => $url_sort))); ?>"> <?= __('URI'); ?>
				<?=($_GET['order'] == 'method' && $_GET['sort'] == 'DESC') ? '<span class="ico mini ico_sort_desc"></span>' : ''; ?>
				<?=($_GET['order'] == 'method' && $_GET['sort'] == 'ASC')  ? '<span class="ico mini ico_sort_asc"></span>'  : ''; ?>
				</a>, <a href="?<?=http_build_query(array_merge($query_string, array('order' => 'status', 'sort' => $url_sort))); ?>"> <?= __('Status'); ?>
				<?=($_GET['order'] == 'status' && $_GET['sort'] == 'DESC') ? '<span class="ico mini ico_sort_desc"></span>' : ''; ?>
				<?=($_GET['order'] == 'status' && $_GET['sort'] == 'ASC')  ? '<span class="ico mini ico_sort_asc"></span>'  : ''; ?>
				</a>, <a href="?<?=http_build_query(array_merge($query_string, array('order' => 'user_agent', 'sort' => $url_sort))); ?>"> <?= __('User Agent'); ?>
				<?=($_GET['order'] == 'user_agent' && $_GET['sort'] == 'DESC') ? '<span class="ico mini ico_sort_desc"></span>' : ''; ?>
				<?=($_GET['order'] == 'user_agent' && $_GET['sort'] == 'ASC')  ? '<span class="ico mini ico_sort_asc"></span>'  : ''; ?>
				</a>, <a href="?<?=http_build_query(array_merge($query_string, array('order' => 'ip', 'sort' => $url_sort))); ?>"> <?= __('IP'); ?>
				<?=($_GET['order'] == 'ip' && $_GET['sort'] == 'DESC') ? '<span class="ico mini ico_sort_desc"></span>' : ''; ?>
				<?=($_GET['order'] == 'ip' && $_GET['sort'] == 'ASC')  ? '<span class="ico mini ico_sort_asc"></span>'  : ''; ?>
				</a>, <a href="?<?=http_build_query(array_merge($query_string, array('order' => 'duration', 'sort' => $url_sort))); ?>"> <?= __('Duration'); ?>
				<?=($_GET['order'] == 'duration' && $_GET['sort'] == 'DESC') ? '<span class="ico mini ico_sort_desc"></span>' : ''; ?>
				<?=($_GET['order'] == 'duration' && $_GET['sort'] == 'ASC')  ? '<span class="ico mini ico_sort_asc"></span>'  : ''; ?>
				</a>, <a href="?<?=http_build_query(array_merge($query_string, array('order' => 'created', 'sort' => $url_sort))); ?>"> <?= __('Created'); ?>
				<?=($_GET['order'] == 'created' && $_GET['sort'] == 'DESC') ? '<span class="ico mini ico_sort_desc"></span>' : ''; ?>
				<?=($_GET['order'] == 'created' && $_GET['sort'] == 'ASC')  ? '<span class="ico mini ico_sort_asc"></span>'  : ''; ?>
				</a> </th>
			<th><?= __('Status'); ?></th>
			<th><?= __('User Agent'); ?></th>
			<th><?= __('IP'); ?></th>
			<th><?= __('Duration'); ?></th>
			<th><?= __('Created'); ?></th>
			<th style="width: 60px;"></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($requests as $request) { ?>
		<tr>
			<td><h4 class="item_content_title"> <a href="request/?id=<?=$request['id'];?>&app_id=<?=$application['id'];?>">
					<?=htmlspecialchars($request['method']);?>
					<?=htmlspecialchars(Format::truncate($request['uri'], 170));?>
					</a> </h4></td>
			<td class="groups"><?php if ($request['status'] === 'ok') { ?>
				<label class="group group_i"><?= __('OK'); ?></label>
				<?php } else { ?>
				<label class="group group_a">
					<?=htmlspecialchars(ucwordS($request['status']));?>
				</label>
				<?php } ?></td>
			<td><?=htmlspecialchars($request['user_agent']);?></td>
			<td><?=htmlspecialchars($request['ip']);?></td>
			<td><?=htmlspecialchars($request['duration']);?></td>
			<td><time datetime="<?=date('c', $request['timestamp_created']); ?>" title="<?=date('l, F jS Y \@ g:ia', $request['timestamp_created']); ?>">
					<?=Format::dateRelative($request['timestamp_created']); ?>
				</time></td>
			<td><div class="actions compact"> <a class="btn log" href="request/?id=<?=$request['id'];?>&app_id=<?=$application['id'];?>"><?= __('View'); ?></a> </div></td>
		</tr>
		<?php } ?>
	</tbody>
</table>
</div>
    <?php if (!empty($pagination['links'])) { ?>
        <div class="rewui nav_pagination">
            <?php if (!empty($pagination['prev'])) { ?>
                <a href="<?= $pagination['prev']['url']; ?>" class="prev">&lt;&lt;</a>
            <?php } ?>
            <?php if (!empty($pagination['links'])) { ?>
                <?php foreach ($pagination['links'] as $link) { ?>
                    <a href="<?= $link['url']; ?>"<?= !empty($link['active']) ? ' class="current"' : ''; ?>>
                        <?= $link['link']; ?>
                    </a>
                <?php } ?>
            <?php } ?>
            <?php if (!empty($pagination['next'])) { ?>
                <a href="<?= $pagination['next']['url']; ?>" class="next">&gt;&gt;</a>
            <?php } ?>
        </div>
    <?php } ?>
<?php } else { ?>
    <p> <?= __('There are currently no logged requests for this application.'); ?> </p>
<?php } ?>
</div>
