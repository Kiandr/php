<?php

// Render lead summary header with link to create search
echo $this->view->render('inc/tpl/partials/lead/summary.tpl.php', [
    'title' => 'Lead Searches',
    'actions' => array_filter([
        ($can_create ? [
            'href' => sprintf('%s?create_search=true&lead_id=%s', Settings::getInstance()->SETTINGS['URL_IDX_SEARCH'], $lead['id']),
            'icon' => 'add'
        ] : NULL)
    ]),
    'lead' => $lead,
    'leadAuth' => $leadAuth
]);

?>

<div class="block">
    <div class="divider">
    	<span class="divider__label divider__label--left">Saved Searches</span>
    </div>
</div>

<?php if (!empty($searches['saved'])) : ?>
<div class="nodes">
<ul class="nodes__list">
	<?php foreach ($searches['saved'] as $search) : ?>
	<li class="nodes__branch">
        <div class="nodes__wrap">

    		<div class="article">

    			<div class="row">

    				<span class="ttl">
    					<?php if (!empty($search['source_app_id'])) { ?>
    					<span class="api-icon" title="Created via another REW site using the '<?=htmlspecialchars(Backend_Lead::apiSource($search['source_app_id'], 'name'));?>' API Application. Alerts for this search will be sent by the source REW site."></span>
    					<?php } ?>

    					<?php if ($can_edit) { ?>

    					<a class="edit omit" href="<?=$search['url_edit']; ?>"><?=Format::htmlspecialchars($search['title']); ?></a>
    					<?php } else { ?>
    					<?=Format::htmlspecialchars($search['title']); ?>
    					<?php } ?>
    				</span>

    				<div class="btns R">
    					<?php if ($can_delete) { ?>
    					<a class="btn btn--ghost delete" href="?id=<?=$lead['id']; ?>&delete=<?=$search['id']; ?>" onclick="return confirm('Are you sure you want to delete this saved search?');">
    						<svg class="icon icon-trash mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use></svg>
    					</a>
    					<?php } ?>
    				</div>

    			</div>


    			<div class="row">
    				<div class="v">
    					<?=$search['criteria']; ?>
    				</div>
    			</div>

    			<div class="row">
    				<div class="groups">
    					<?php if (!empty($search['agent'])) { ?>
    					<span class="token">
    						<span class="token__thumb thumb thumb--tiny"><img width="24" height="24" src="/thumbs/312x312/uploads/agents/na.png" alt=""></span>
    						<span class="token__label"><?=Format::htmlspecialchars($search['agent']['name']); ?></span>
    					</span>
    					<?php } else if (!empty($search['associate'])) { ?>
    					<span class="token">
    						<span class="token__thumb thumb thumb--tiny"><img width="24" height="24" src="/thumbs/312x312/uploads/agents/na.png" alt=""></span>
    						<span class="token__label"><?=Format::htmlspecialchars($search['associate']['name']); ?></span>
    					</span>
    					<?php } ?>
    				</div>
    			</div>

    			<div class="row">
    				<div class="v">
    					<?=ucwords($search['frequency']); ?>,
    					<?php if (!empty($search['timestamp_sent'])) : ?>Sent <?=number_format($search['sent']); ?> Times<br />Last Sent
    					<time datetime="<?=date('c', $search['timestamp_sent']); ?>" title="<?=date('l, F jS Y \@ g:ia', $search['timestamp_sent']); ?>"><?=Format::dateRelative($search['timestamp_sent']); ?></time><br />
    					<?php endif; ?>
    					Created <time datetime="<?=date('c', $search['timestamp_created']); ?>" title="<?=date('l, F jS Y \@ g:ia', $search['timestamp_created']); ?>"><?=Format::dateRelative($search['timestamp_created']); ?></time>
    				</div>
    			</div>

    		</div>
    	</li>
    <?php endforeach; ?>
    </ul>
    </div>
    <?php else : ?>
    <div class="block">
    	<p>This lead currently has no saved searches.</p>
    </div>
    <?php endif; ?>




    <?php if (!empty($searches['suggested'])) { ?>
    <div class="block">
        <div class="divider">
            <span class="divider__label divider__label--left">Suggested Searches</span>
        </div>
    </div>
    <div class="nodes">
    <ul class="nodes__list">
    	<?php foreach ($searches['suggested'] as $search) : ?>
    	<li class="nodes__branch">
    	    <div class="nodes__wrap">
        		<div class="article">
                    <div class="row">
                				<span class="ttl"><a class="view" href="<?=$search['url_view']; ?>" target="_blank"><?=Format::htmlspecialchars($search['title']); ?></a></span>
                				<div class="btns R">
                					<?php if ($can_delete) { ?>
                					<a class="btn btn--ghost delete" href="?id=<?=$lead['id']; ?>&unsuggest=<?=$search['id']; ?>" onclick="return confirm('Are you sure you want to remove this suggested search?');"><svg class="icon icon-trash mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use></svg></a>
                					<?php } ?>
                				</div>
                    </div>
                    <div class="row">
                        <div class="v"><?=$search['criteria']; ?></div>
                    </div>
            				<div class="groups">
            					<?php if (!empty($search['agent'])) { ?>
            					<span class="token">
            						<span class="token__thumb thumb thumb--tiny"><img width="24" height="24" src="/thumbs/312x312/uploads/agents/na.png" alt=""></span>
            						<span class="token__label"><?=Format::htmlspecialchars($search['agent']['name']); ?></span>
            					</span>
            					<?php } else if (!empty($search['associate'])) { ?>
            					<span class="token">
            						<span class="token__thumb thumb thumb--tiny"><img width="24" height="24" src="/thumbs/312x312/uploads/agents/na.png" alt=""></span>
            						<span class="token__label"><?=Format::htmlspecialchars($search['associate']['name']); ?></span>
            					</span>
            					<?php } ?>
            				</div>
                    <div class="v">
                      <?=number_format($search['views']); ?> Views,
                      <time datetime="<?=date('c', $search['timestamp']); ?>" title="<?=date('l, F jS Y \@ g:ia', $search['timestamp']); ?>"><?=Format::dateRelative($search['timestamp']); ?></time>
                    </div>
                </div>
    		</div>
    	</li>
    	<?php endforeach; ?>
    </ul>
    </div>
<?php } ?>

<div class="block">
    <div class="divider">
    	<span class="divider__label divider__label--left">Recent Searches</span>
    </div>
</div>

<?php if (!empty($searches['viewed'])) { ?>
    <div class="nodes">
        <ul class="nodes__list">
            <?php foreach ($searches['viewed'] as $search) { ?>
                <li class="nodes__branch">
                    <div class="nodes__wrap">
                        <div class="article multi-row">
                            <div class="row">
                                <span class="ttl">
                                    <a class="view" href="<?=$search['url_view']; ?>" target="_blank"><?=Format::htmlspecialchars($search['title']); ?></a>
                                </span>
                                <div class="btns compact">
                                    <?php if (!empty($can_suggest)) { ?>
                                        <a class="btn btn--ghost suggest" href="?id=<?=$lead['id']; ?>&suggest=<?=$search['id']; ?>">Suggest</a>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="v"><?=$search['criteria']; ?></div>
                            </div>
                        </div>
                    </div>
                </li>
            <?php } ?>
        </ul>
    </div>
<?php } else { ?>
    <div class="block">
        <p>This lead currently has no recent searches.</p>
    </div>
<?php } ?>