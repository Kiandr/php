<copy-campaigns name="Agents" title="Copy to Agents" placeholder="Choose Agents..." cta-text="Copy" :multiple="false"></copy-campaigns>

<?php if (!empty($agents)) : ?>
<div class="menu menu--drop hidden" id="menu--filters">
    <ul class="menu__list">
        <?php if ($leadsAuth->canManageCampaigns($authuser)) { ?>
            <li class="menu__item -marB8"><a href="?agent=all" class="menu__link token"><span><?= __('All Campaigns'); ?></span></a></li>
        <?php } ?>
        <li class="menu__item -marB8"><a href="?personal" class="menu__link token"><span><?= __('My Campaigns'); ?></span></a></li>

        <?php if ($leadsAuth->canManageCampaigns($authuser)) { ?>
            <?php foreach ($agents as $agent) : ?>
            <li class="menu__item">
                <a class="menu__link token" href="?agent=<?=$agent['id']; ?>">
                    <?php if (!empty($agent['image'])) { ?>
                        <span class="token__thumb thumb thumb--tiny">
                            <img width="32" height="32" src="/thumbs/200x200/uploads/agents/<?=$agent['image']; ?>">
                        </span>
                    <?php } else { ?>
                        <div class="thumb-text-center token thumb thumb--tiny -bg-<?=strtolower(substr($agent['initials'], 0, 1)); ?>">
                            <span class="thumb__label"><?=strtoupper($agent['initials']); ?></span>
                        </div>
                    <?php } ?>
                    <span class="token__label"><?=$agent['first_name']; ?> <?=$agent['last_name']; ?></span>
                </a>
            </li>
            <?php endforeach; ?>
        <?php } ?>
    </ul>
</div>
<?php endif;?>

<div class="bar">
	<div class="bar__title" data-drop="#menu--filters">
        <?=$filter; ?>
        <svg class="icon icon-drop">
            <use xlink:href="/backend/img/icos.svg#icon-drop" xmlns:xlink="http://www.w3.org/1999/xlink" />
        </svg>
    </div>
	<div class="bar__actions">
        <?php if (!empty($can_copy) && !empty($agents)) : ?>
            <btn-campaign-copy id="btn-copy" disabled></btn-campaign-copy>
        <?php endif;?>
		<a class="bar__action" href="/backend/leads/campaigns/add/"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use></svg></a>
	</div>
</div>

<?php if (!empty($campaigns)) : ?>
<div class="nodes">
    <ul class="nodes__list">
		<?php foreach ($campaigns as $campaign) : ?>
		<li class="nodes__branch">
			<div class="article">

                <div class="nodes__wrap">
                    <div class="nodes__toggle">
    					<?php if (!empty($can_copy)) : ?>
    					<span class="control"><input type="checkbox" class="manage_campaigns" name="campaigns[]" value="<?=$campaign['id']; ?>"></span>
    					<?php endif; ?>
                    </div>
                	<div class="article">
                        <div class="article__body">
                            <div class="article__thumb thumb thumb--medium -bg-rew2"><svg class="icon icon--invert"><use xlink:href="/backend/img/icos.svg#icon-campaign"/></svg></div>
                            <div class="article__content">
                                <a class="text text--strong" href="edit/?id=<?=$campaign['id']; ?>"><?=Format::htmlspecialchars($campaign['name']); ?></a>
            					<div class="text text--mute">
                                    <?php if (!empty($campaign['leads'])) : ?>
                                        <a href="<?=URL_BACKEND; ?>leads/?submit=true&opt_marketing=in&groups[]=<?=implode('&groups[]=', array_keys($campaign['groups'])) . ($campaign['agent_id'] != 1 ? '&agents[]=' . $campaign['agent_id'] : ''); ?>">
                                            <?=number_format($campaign['leads']); ?> <?= __('Leads'); ?>
                                        </a> ,
                                    <?php endif; ?>
                                    <?=number_format($campaign['emails']); ?>
                                    <?=(($campaign['active'] == 'Y') ? __('Active') : __('Inactive')); ?>
            					</div>
                                <div class="text text--mute">
                                <a href="<?=URL_BACKEND; ?>agents/agent/summary/?id=<?=$campaign['agent_id']; ?>">
                                    <div class="thumb-text-center token thumb thumb--tiny -bg-<?=strtolower(substr($campaign['agent_name'], 0, 1)); ?>"><span class="thumb__label"><?=$campaign['initials']; ?></span></div>
                                    <?=$campaign['agent_name']; ?>
                                </a>
                                </div>
                            </div>
                        </div>
                        <div class="article__foot">
                            <?php if (!empty($campaign['groups'])) : ?>

    							<?php

		                        // Campaign Groups
		                        if (!empty($campaign['groups'])) {

		                            // Group Labels
		                            $labels = array();
		                            foreach ($campaign['groups'] as $group) {
		                                $labels[] = '<label class="token group_' . $group['id'] . '" title="' . Format::htmlspecialchars($group['title']) . '"><span class="token__thumb thumb thumb--tiny -bg-'. $group['style'] .'"></span><span class="token__label">' . (strlen($group['name']) > 15 ? substr($group['name'], 0, 12) . '...' : $group['name']) . '</span></label>';
		                            }

                                    echo implode(array_slice($labels, 0, $show));

		                            // Display Labels
		                            $show = 2;
		                            if (count($labels) > $show) {
		                                echo '<label class="token"><span class="token__label">+' . (count($labels) - $show);
		                                echo '</span>';
		                                echo '</label>';
		                            }
		                        }

    		                    ?>

                            <?php endif; ?>
                        </div>
                	</div>
                	<div class="nodes__actions">
                        <a class="btn btn--ghost btn--ico" href="?delete=<?=$campaign['id']; ?>" onclick="return confirm('<?= __('Are you sure you want to delete this campaign?'); ?>');">
                            <svg class="icon icon-trash mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use></svg>
                        </a>
                	</div>
                </div>

			</div>
		</li>
		<?php endforeach; ?>
    </ul>
</div>
<?php else : ?>
<p class="block"><?= __('There are currently no campaigns to manage.'); ?></p>
<?php endif; ?>