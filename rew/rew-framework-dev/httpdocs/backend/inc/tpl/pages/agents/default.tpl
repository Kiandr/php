<filter-agents name="Filter"></filter-agents>
<div class="bar">
    <div class="bar__title"><?= __('Agents'); ?><?=(!empty($office) ? ' - ' . Format::htmlspecialchars($office['title']) : ''); ?></div>
        <div class="bar__actions">
            <?php if ($canAdd) {?>
                <a href="/backend/agents/add/" class="bar__action"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use></svg></a>
            <?php }?>
            <?php if (in_array($_GET['filter'], ['auto_assign', 'auto_rotate', 'auto_optout'])) { ?>
                <a href="<?=URL_BACKEND ?>settings/" class="bar__action"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
            <?php } ?>
			<btn-filter></btn-filter>
        </div>
</div>

<?php if (!empty($search_criteria)) { ?>
<div class="block">
	<?=implode(', ', $search_criteria); ?>
</div>
<?php } else { ?>
<div class="block">
    <div class="tabs">
    	<ul>
            <li<?=(empty($_GET['letter']) ? ' class="current"' : ''); ?>><a href="?letter=">All</a></li>
    		<?php foreach ($letters as $letter) { ?>
            <?php $url = '?' . http_build_query(array('letter' => $letter == '#' ? 'num' : $letter)); ?>
    		<li<?=($_GET['letter'] == $letter ? ' class="current"' : ''); ?>><a href="<?=$url; ?>">
    			<?=$letter; ?>
    			</a></li>
    		<?php } ?>
            <?php if (!empty($agents) && !empty($can_email)) { ?>
                <li class="btns R group_actions">
                    <button id="agents-email" class="btn btn--ico btn--ghost" disabled>
                        <svg class="icon">
                            <use xlink:href="/backend/img/icos.svg#icon-email"></use>
                        </svg>
                    </button>
                </li>
            <?php } ?>
    	</ul>
    </div>
</div>
<?php } ?>
<form action="<?=URL_BACKEND; ?>email/?type=agents" method="post">
	<?php if (!empty($agents)) { ?>
	<div class="nodes">
    	<ul class="nodes__list">
    		<?php foreach ($agents as $agent) { ?>
    		<li class="nodes__branch">
    		    <div class="nodes__wrap">
    		        <div class="nodes__toggle">
    					<?php if (!empty($can_email)) { ?>
        					<?php if ($authuser->info('id') != $agent['id']) { ?>
        						<input type="checkbox" class="manage_agents" name="agents[]" value="<?=$agent['id']; ?>">
        					<?php } ?>
    					<?php } ?>
                    </div>
        			<div class="article">
        			    <div class="article__body">
                            <?php if (!empty($agent['image'])) { ?>
                                <div class="article__thumb thumb thumb--medium">
                                    <img src="/thumbs/60x60/uploads/agents/<?=urlencode($agent['image']) ?: 'na.png'; ?>">
                                </div>
                            <?php } else { ?>
                                <div class="article__thumb thumb thumb--medium -bg-<?=strtolower($agent['last_name'][0]); ?>">
                                    <span class="thumb__label"><?=$agent['first_name'][0] . $agent['last_name'][0]; ?></span>
                                </div>
                            <?php } ?>
                            <div class="article__content">
                                <a class="text text--strong" href="agent/summary/?id=<?=$agent['id']; ?>" class="ttl"> <span class="thb"></span><?=Format::htmlspecialchars($agent['first_name']); ?> <?=Format::htmlspecialchars($agent['last_name']); ?></a>
                                <div class="text text--mute"><?=!empty($agent['title']) ? Format::htmlspecialchars($agent['title']) : ''; ?></div>
                            </div>
        				</div>
        			</div>
                    <?php if ($agentsAuth->canDeleteAgents($authuser) && !in_array($agent['id'], [1, $authuser->info('id')])) {?>
                        <div class="nodes__actions">
                            <a class="btn btn--ico btn--ghost" title="<?= __('Delete'); ?>" href="agent/delete/?id=<?=$agent['id']; ?>">
                                <svg class="icon icon-trash mar0">
                                    <use xlink:href="/backend/img/icos.svg#icon-trash"></use>
                                </svg>
                            </a>
                        </div>
                    <?php } ?>
    			</div>
    		</li>
    		<?php } ?>
    	</ul>
	</div>
	<?php
		// Pagination
		if (!empty($pagination['links'])) {
			echo '<div class="rewui nav_pagination">';
			if (!empty($pagination['prev'])) echo '<a href="' . $pagination['prev']['url'] . '" class="prev">&lt;&lt;</a>';
			if (!empty($pagination['links'])) {
				foreach ($pagination['links'] as $link) {
					echo '<a href="' . $link['url'] . '"' . (!empty($link['active']) ? ' class="current"' : '') . '>' . $link['link'] . '</a>';
				}
			}
			if (!empty($pagination['next'])) echo '<a href="' . $pagination['next']['url'] . '" class="next">&gt;&gt;</a>';
			echo '</div>';
		}

		?>
	<?php } else { ?>

	<p class="block"><?= __('There are currently no available agents.'); ?></p>

	<?php } ?>
</form>
