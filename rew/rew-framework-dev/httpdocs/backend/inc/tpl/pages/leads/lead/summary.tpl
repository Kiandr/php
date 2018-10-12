<?php

// Render lead summary header (menu/title/preview)
echo $this->view->render('inc/tpl/partials/lead/summary.tpl.php', [
    'title' => 'Lead Summary',
    'lead' => $lead,
    'leadAuth' => $leadAuth
]);

?>
<div class="block">
    <div class="keyvals keyvals--bordered -marB">
        <?php if(!empty($lead['phone'])) { ?><div class="keyvals__row keyvals__row--rows@sm"><span class="keyvals__key text text--strong -padB0@sm">Primary Phone</span><span class="keyvals__val text text--mute"><?=$lead['phone'];?></span></div><?php } ?>
        <?php if ($leadAuth->canEmailLead()) { ?>
            <div class="keyvals__row keyvals__row--rows@sm">
                <span class="keyvals__key text text--strong -padB0@sm">Email</span>
                <span class="keyvals__val text text--mute -padT0@sm">
                    <a href="<?=URL_BACKEND; ?>email/?id=<?=$lead['id']; ?>&type=leads"><?=Format::htmlspecialchars($lead['email']); ?></a>
                </span>
            </div>
        <?php } ?>
    </div>
</div>


    <div class="block">

		<?php if($lead['heat']) {

			echo '<span class="';

			switch ($lead['heat']) {
				case 'hot' :
					echo 'group_hot"> Hot';
					break;
				case 'mediumhot' :
					echo 'group_medhot"> Med Hot';
					break;
				case 'warm' :
					echo 'group_warm"> Warm';
					break;
				case 'lukewarm' :
					echo 'group_luke"> Luke Warm';
					break;
				case 'cold' :
					echo 'group_cold"> Cold';
					break;
			}

			echo '</span>';

		}
		?>

		<?php
		// Lead Groups
		if (!empty($lead['groups'])) {

		    // Group Labels
		    $labels = array();
		    foreach ($lead['groups'] as $group) {
		        $labels[] = '<span class="token" title="' . Format::htmlspecialchars($group['title']) . '"><span class="token__thumb thumb thumb--tiny -bg-' . $group['style'] . '"></span><span class="token__label">' . Format::htmlspecialchars(strlen($group['name']) > 15 ? substr($group['name'], 0, 12) . '...' : $group['name']) . '</span></span> ';
		    }

		    echo implode(array_slice($labels, $show));

		}
        ?>

	</div>


    <div class="block">
        <p class="scaleline"> <span class="line" style="width: <?=$lead['score']; ?>%;"></span> <span class="potential">
        	<?php if ($lead['value'] > 0) { ?>
        	<span title="Potential Value">$
        	<?=Format::shortNumber($lead['value']); ?>
        	</span> / <span title="Potential Commission (3%)">$
        	<?=Format::shortNumber($lead['commission']); ?>
        	</span>
        	<?php } else { ?>
        	Unknown Value ($)
        	<?php } ?>
        	</span>
        </p>

        <div class="notes" data-lead-quick-notes="<?=htmlentities(json_encode([
            'lead' => (int) $lead['id'],
            'notes' => $lead['notes']
        ])); ?>" title="Click to Edit"><?=Format::htmlspecialchars($lead['notes']) ?: '<a>Add Quick Notes</a>'; ?></div>

    </div>

    <div class="block -marB">
        <div class="divider -marV">
            <span class="divider__label divider__label--left text text--large">Notes</span>
            <a href="/backend/leads/lead/notes/?id=<?=$lead['id']; ?>" class="divider__label divider__label--right" title="Add a New Note">
                <svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"/></svg>
            </a>
        </div>
        <?php if(!empty($notes)) { ?>
            <div id="agentNotesContainer">
                <div id="agentNotes" class="notes notes--agent">
                    <div class="keyvals marB" style="display: block;">
                        <?php foreach ($notes as $note) { ?>
                            <div class="keyvals__row keyvals__row--rows -marB">
                                <span class="keyvals__key text text--small text--mute">
                                    <time datetime="<?=date('c', $note['timestamp']); ?>" title="<?=date('l, F jS Y \@ g:ia', $note['timestamp']); ?>"><?=Format::dateRelative($note['timestamp']); ?></time>
                                </span>
                                <a href="../notes/?id=<?=$lead['id']; ?>&edit=<?=$note['id']; ?>" class="text"><?=Format::htmlspecialchars($note['note']); ?></a>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="text--center"><img src="/backend/img/ghost.png" width="72"><p class="text">No Notes yet! Why not <a href="/backend/leads/lead/notes/?id=<?=$lead['id']; ?>" title="Add a New Note">Add One</a>?</p></div>
        <?php } ?>

        <?php if(!empty($lead['comments'])) { ?>
            <div class="divider -marV">
                <span class="divider__label divider__label--left text text--large">Lead Comments</span>
            </div>
            <div class="notes">
                <?=!empty($lead['comments']) ? nl2br(Format::htmlspecialchars(strip_tags($lead['comments'], '<a>'))) : '(No Comments)'; ?>
            </div>
        <?php } ?>

        <?php if(!empty($lead['remarks'])) { ?>
            <div class="divider -marV">
                <span class="divider__label divider__label--left text text--large">Lead Remarks</span>
            </div>
            <div class="notes">
                <?=!empty($lead['remarks']) ? nl2br(Format::htmlspecialchars(strip_tags($lead['remarks'], '<a>'))) : '(No Remarks)'; ?>
            </div>
        <?php } ?>
    </div>

    <div class="block -marB">
        <div class="divider -marB">
            <span class="divider__label divider__label--left text text--large">Listings</span>
        </div>
        <div class="keyvals keyvals--columns text--center -marB">
            <div class="keyvals__row keyvals__row--rows">
                <span class="keyvals__key text text--small text--mute">Views</span>
                <a href="../listings/?id=<?=$lead['id']; ?>&type=viewed" class="keyvals__val text text--strong"><?=number_format($lead['num_listings']); ?></a>
            </div>
            <div class="keyvals__row keyvals__row--rows">
                <span class="keyvals__key text text--small text--mute">Favorites</span>
                <a href="../listings/?id=<?=$lead['id']; ?>&type=saved" class="keyvals__val text text--strong"><?=number_format($lead['num_favorites']); ?></a>
            </div>
            <div class="keyvals__row keyvals__row--rows">
                <span class="keyvals__key text text--small text--mute">Searches</span>
                <a href="../searches/?id=<?=$lead['id']; ?>" class="keyvals__val text text--strong"><?=number_format($lead['num_searches']); ?></a>
            </div>
            <div class="keyvals__row keyvals__row--rows">
                <span class="keyvals__key text text--small text--mute">Sav. Search</span>
                <a href="../searches/?id=<?=$lead['id']; ?>" class="keyvals__val text text--strong"><?=number_format($lead['num_saved']); ?></a>
            </div>
        </div>
        <div class="keyvals keyvals--bordered -marB">
            <div class="keyvals__row keyvals__row--rows@sm">
                <span class="keyvals__key text text--strong -padB0@sm">Property Types</span>
                <span class="keyvals__val text -padT0@sm"><?=!empty($lead['search_type']) ? Format::htmlspecialchars($lead['search_type']) : 'Unknown'; ?></span>
            </div>
            <div class="keyvals__row keyvals__row--rows@sm">
                <span class="keyvals__key text text--strong -padB0@sm">Cities</span>
                <span class="keyvals__val text -padT0@sm"><?=!empty($lead['search_city']) ? Format::htmlspecialchars($lead['search_city']) : 'Unknown'; ?></span>
            </div>
            <div class="keyvals__row keyvals__row--rows@sm">
                <span class="keyvals__key text text--strong -padB0@sm">Subdivions</span>
                <span class="keyvals__val text -padT0@sm"><?=!empty($lead['search_subdivision']) ? Format::htmlspecialchars($lead['search_subdivision']) : 'Unknown'; ?></span>
            </div>
            <div class="keyvals__row keyvals__row--rows@sm">
                <span class="keyvals__key text text--strong -padB0@sm">Min. Price</span>
                <span class="keyvals__val text -padT0@sm">$<?=number_format($lead['search_minimum_price']); ?></span>
            </div>
            <div class="keyvals__row keyvals__row--rows@sm">
                <span class="keyvals__key text text--strong -padB0@sm">Max. Price</span>
                <span class="keyvals__val text -padT0@sm">$<?=number_format($lead['search_maximum_price']); ?></span>
            </div>
        </div>
        <div class="keyvals keyvals--bordered marB">
    		<div class="keyvals__row">
    		    <span class="keyvals__key text text--strong">Viewed Listings:</span>
                <span class="keyvals__val text">
                    <a class="view" href="../listings/?id=<?=$lead['id']; ?>&type=viewed">
    				<?=number_format($lead['num_listings']); ?>
    				</a>
                </span>
            </div>
    		<div class="keyvals__row">
    		    <span class="keyvals__key text text--strong">Recommended Listings:</span>
                <span class="keyvals__val text">
                    <a class="view" href="../listings/?id=<?=$lead['id']; ?>&type=recommended">
    				<?=number_format($lead['num_recommended']); ?>
    				</a>
                </span>
            </div>
    		<div class="keyvals__row">
    		    <span class="keyvals__key text text--strong">Saved <?=Locale::spell('Favorites');?></span></span>
                <span class="keyvals__val text">
                    <a class="view" href="../listings/?id=<?=$lead['id']; ?>&type=saved">
    				<?=number_format($lead['num_favorites']); ?>
    				</a>
    			</span>
    		</div>
    		<div class="keyvals__row">
    		    <span class="keyvals__key text text--strong">Viewed Searches:</span>
                <span class="keyvals__val text">
                    <a class="view" href="../searches/?id=<?=$lead['id']; ?>#recent">
    				<?=number_format($lead['num_searches']); ?>
    				</a>
    			</span>
    		</div>
    		<div class="keyvals__row">
    		    <span class="keyvals__key text text--strong">Suggested Searches</span>
                <span class="keyvals__val text">
                    <a class="view" href="../searches/?id=<?=$lead['id']; ?>">
    				<?=number_format($lead['num_suggested']); ?>
    				</a>
    			</span>
    		</div>
    		<div class="keyvals__row">
    		    <span class="keyvals__key text text--strong">Saved Searches</span>
                <span class="keyvals__val text">
                    <a class="view" href="../searches/?id=<?=$lead['id']; ?>">
    				<?=number_format($lead['num_saved']); ?>
    				</a>
    			</span>
    		</div>
        </div>
        <div class="grid">
            <a class="btn col w1/2" href="/backend/leads/lead/searches/?id=<?=$lead['id']; ?>">View Searches</a>
            <a class="btn col w1/2" href="/backend/leads/lead/listings/?id=<?=$lead['id']; ?>">View Listings</a>
        </div>
    </div>

    <div class="block -marB">
        <div class="divider -marB">
            <span class="divider__label divider__label--left text text--large">Basics</span>
        </div>
        <div class="keyvals keyvals--bordered -marB">
    	    <?php if (!empty($agent)) { ?>
            <div class="keyvals__row keyvals__row--rows@sm">
                <span class="keyvals__key text text--strong -padB0@sm">Agent</span>
                <span class="keyvals__val text -padT0@sm">
                    <div class="article">
                        <div class="article__body">
                            <?php if(empty($agent['image'])) { ?>
                                <div class="article__thumb thumb thumb--small -bg-<?=strtolower($agent->info('last_name')[0]);?>">
                                    <span class="thumb__label"><?=$agent->info('first_name')[0];?><?=$agent->info('last_name')[0];?></span>
                                </div>
                            <?php } else { ?>
                                <div class="article__thumb thumb thumb--small"><img src="/thumbs/200x200/uploads/agents/<?=$agent['image']; ?>"></div>
                            <?php } ?>

                            <div class="article__content">
                                <div class="text text--strong"><?=Format::htmlspecialchars($agent->getName()); ?></div>
                                <div class="text text--mute text--small"><time datetime="<?=date('c', $lead['timestamp_assigned']); ?>" title="<?=date('l, F jS Y \@ g:ia', $lead['timestamp_assigned']); ?>"><?=Format::dateRelative($lead['timestamp_assigned']); ?></time> (<?=$lead['status']; ?>)</div>
                            </div>
                        </div>
                    </div>
                </span>
            </div>
            <?php } ?>
            <?php if (!empty(Settings::getInstance()->MODULES['REW_LENDERS_MODULE']) && !empty($lender)) { ?>
            <div class="keyvals__row keyvals__row--rows@sm">
                <span class="keyvals__key text text--strong -padB0@sm">Lender</span>
                <span class="keyvals__val text -padT0@sm">
                    <div class="article">
                        <div class="article__body">
                            <div class="article__thumb thumb thumb--small -bg-<?=strtolower($lender->info('last_name')[0]);?>">
                                <?php if(empty($lender['image'])) { ?>
                                <span class="thumb__label"><?=$lender->info('first_name')[0];?><?=$lender->info('last_name')[0];?></span>
                                <?php } ?>
                            </div>
                            <div class="article__content">
                                <div class="text text--strong"><?=Format::htmlspecialchars($lender->getName()); ?></div>
                            </div>
                        </div>
                    </div>
                </span>
            </div>
            <?php } ?>
        </div>
        <div class="keyvals keyvals--bordered marB">
    	    <?php if (!empty($agent)) { ?>
            <div class="keyvals__row keyvals__row--rows@sm">
                <span class="keyvals__key text text--strong -padB0@sm">Joined</span>
                <span class="keyvals__val text -padT0@sm">
                    <time datetime="<?=date('c', strtotime($lead['timestamp'])); ?>" title="<?=date('l, F jS Y \@ g:ia', strtotime($lead['timestamp'])); ?>">
    					<?=Format::dateRelative(strtotime($lead['timestamp'])); ?>
    				</time>
                </span>
            </div>
            <?php } ?>
            <?php if (!empty($lead['timestamp_active'])) { ?>
            <div class="keyvals__row keyvals__row--rows@sm">
                <span class="keyvals__key text text--strong -padB0@sm">Last Visit</span>
                <span class="keyvals__val text -padT0@sm">
    				<time datetime="<?=date('c', $lead['timestamp_active']); ?>" title="<?=date('l, F jS Y \@ g:ia', $lead['timestamp_active']); ?>">
    					<?=Format::dateRelative($lead['timestamp_active']); ?>
    				</time>
                </span>
            </div>
            <?php } ?>
        </div>
        <div class="grid">
            <a class="btn col w1/1" href="/backend/leads/lead/history/?id=<?=$lead['id']; ?>">View History</a>
        </div>
    </div>

    <div class="block -marB">
        <div class="divider -marB"><span class="divider__label divider__label--left text text--large">Subscriptions</span></div>
        <div class="keyvals keyvals--bordered marB">
    	    <?php if (!empty($agent)) { ?>
            <div class="keyvals__row keyvals__row--rows@sm">
                <span class="keyvals__key text text--strong -padB0@sm">Campaigns</span>
                <span class="keyvals__val text -padT0@sm">
                    <?=(($lead['opt_marketing'] == 'in') ? 'Yes' : 'No'); ?>
                </span>
            </div>
            <?php } ?>
            <div class="keyvals__row keyvals__row--rows@sm">
                <span class="keyvals__key text text--strong -padB0@sm">Searches</span>
                <span class="keyvals__val text -padT0@sm">
                    <?=(($lead['opt_searches'] == 'in') ? 'Yes' : 'No'); ?>
                </span>
            </div>
            <div class="keyvals__row keyvals__row--rows@sm">
                <span class="keyvals__key text text--strong -padB0@sm">Text Messages</span>
                <span class="keyvals__val text -padT0@sm">
                    <?=$lead['opt_texts'] == 'in' ? 'Yes' : 'No'; ?>
                </span>
            </div>
        </div>
    </div>

    <div class="block -marB">
        <div class="divider -marB"><span class="divider__label divider__label--left text text--large">Stats</span></div>
        <div class="keyvals keyvals--bordered marB">
    		<?php if (!empty($lead['source_app_id'])) { ?>
    		<div class="keyvals__row keyvals__row--rows@sm">
    		    <span class="keyvals__key">API Source</span>
    		    <span class="keyvals__val text -padT0@sm">
    		        <a href="<?=URL_BACKEND;?>settings/api/app/edit/?id=<?=$lead->getAPISource('id');?>"><?=htmlspecialchars($lead->getAPISource('name'));?></a>
    		     </span>
    		</div>
    		<?php } ?>
    		<div class="keyvals__row keyvals__row--rows@sm">
    			<span class="keyvals__key">
    			    Original Source
                </span>
                <span class="keyvals__val text -padT0@sm">
    				<?=!empty($lead['referer']) ? Format::htmlspecialchars($lead['referer']) : 'Unknown'; ?>
                </span>
    		</div>
    		<div class="keyvals__row keyvals__row--rows@sm">
    		    <span class="keyvals__key">Search Engine Keywords</span>
    		    <span class="keyvals__val text -padT0@sm">
    			    <?=!empty($lead['keywords']) ? $lead['keywords'] : 'Unknown'; ?>
    			</span>
    		</div>
    		<div class="keyvals__row keyvals__row--rows@sm">
    		    <span class="keyvals__key">Visits</span>
    		    <span class="keyvals__val text -padT0@sm">
                    <a class="view" href="../visits/?id=<?=$lead['id']; ?>"><?=number_format($lead['num_visits']); ?></a>
                </span>
    		</div>
    		<div class="keyvals__row keyvals__row--rows@sm">
    		    <span class="keyvals__key">Pages Viewed</span>
    		    <span class="keyvals__val text -padT0@sm">
                    <a class="view" href="../visits/?id=<?=$lead['id']; ?>"><?=number_format($lead['num_pages']); ?></a>
                </span>
    		</div>
    		<div class="keyvals__row keyvals__row--rows@sm">
    		    <span class="keyvals__key">Form Inquiries</span>
                <span class="keyvals__val text -padT0@sm">
                    <a class="view" href="../history/?id=<?=$lead['id']; ?>&filter=inquiries"><?=number_format($lead['num_forms']); ?></a>
                </span>
    		</div>
        </div>
    </div>


    <?php if (Settings::getInstance()->MODULES['REW_ACTION_PLANS'] && $leadAuth->canViewActionPlans()) { ?>

    <div class="block -marB">
        <div class="divider -marB">
            <span class="divider__label divider__label--left text text--large">Action Plans</span>
            <?php if ($can_assign_action_plans) { ?>
                <a href="/backend/leads/lead/tasks/?id=<?=$lead['id']; ?>" class="divider__label divider__label--right" title="Add an Action Plan">
                    <svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"/></svg>
                </a>
            <?php } ?>
        </div>
        <div class="action-plans">
            <?php

            // Display Action Plans
            if (!empty($lead['action_plans'])) {
                foreach ($lead['action_plans'] as $status => $plans) {
                    if (is_array($plans)) {
                        foreach ($plans as $plan) {
                            $plan = Backend_ActionPlan::load($plan['id']);
                            echo '<div class="-marB plan plan_' . $plan['style'] . ' plan_' . $plan['id'] . ($status === 'completed' ? ' completed' : '') . '" title="' . Format::htmlspecialchars($plan->info('description')) . '" style="overflow: hidden;">'
                                . '<a href="' . sprintf('%sleads/lead/tasks/?id=%s', URL_BACKEND, $lead['id']) . '">' . Format::htmlspecialchars($plan->info('name')) . '</a>';
                            if ($can_manage_action_plans) {
                                echo '<a class="R btn btn--ghost btn--ico" href="' . sprintf('%sleads/action_plans/edit/?id=%s', URL_BACKEND, $plan->info('id')) . '" style="padding: 0;">'
                                    . '<svg class="icon"><use xlink:href="' . URL_BACKEND . 'img/icos.svg#icon-cog" /></svg>'
                                    . '</a>';
                            }
                            echo '</div>';
                        }
                    }
                }
            } else {
                echo '<div class="text--center"><img src="/backend/img/ghost.png" width="72"><p class="text">No Assigned Action Plans.';
                if ($can_assign_action_plans) {
                    echo ' Why not <a href="' . sprintf('%sleads/lead/tasks/?id=%s', URL_BACKEND, $lead['id']) . '">Add One</a>?';
                }
                echo '</p></div>';
            }

            ?>
        </div>
    </div>

    <?php } ?>

    <div class="block  -marB">
        <div class="divider -marB"><span class="divider__label divider__label--left text text--large">Contact Info</span></div>
        <div class="keyvals keyvals--bordered marB">
            <?php if (!empty($lead['contact_method'])) { ?>
    		<div class="keyvals__row keyvals__row--rows@sm">
    		    <span class="keyvals__key">Preferred Method</span>
                <span class="keyvals__val text -padT0@sm">
    				<?=ucwords($lead['contact_method']); ?>
    			</span>
    		</div>
    		<?php } ?>
    		<?php if (!empty($leadAuth->canEmailLead())) { ?>
    		<div class="keyvals__row keyvals__row--rows@sm">
    		    <span class="keyvals__key">Email</span>
                <span class="keyvals__val text -padT0@sm">
                    <a href="<?=URL_BACKEND; ?>email/?id=<?=$lead['id']; ?>&type=leads"><?=Format::htmlspecialchars($lead['email']); ?></a>
    				<?php if ($lead['fbl'] == 'true') { ?>
    				<label class="group group_a">Reported</label>
    				<?php } else if ($lead['bounced'] == 'true') { ?>
    				<label class="group group_a">Bounced</label>
    				<?php } else if ($lead['verified'] == 'yes') { ?>
    				<label class="group group_i">Verified</label>
    				<?php } elseif (!Validate::verifyWhitelisted($lead['email']) && (Validate::verifyRequired($lead['email']) || !empty(Settings::getInstance()->SETTINGS['registration_verify'])) && $lead['verified'] != 'yes') { ?>
    				<label class="group group_a">Unverified</label>
    				<?php } ?>
    				</span>
            </div>
    		<?php } ?>
    		<?php if (!empty($lead['address1']) || !empty($lead['address2']) || !empty($lead['city']) || !empty($lead['state']) || !empty($lead['zip'])) { ?>
    		<div class="keyvals__row keyvals__row--rows@sm">
    		    <span class="keyvals__key"><span class="k">Address</span>
                <span class="keyvals__val text -padT0@sm">
    				<?=!empty($lead['address1']) ? Format::htmlspecialchars($lead['address1']) . '<br>' : ''; ?>
    				<?=!empty($lead['address2']) ? Format::htmlspecialchars($lead['address2']) . '<br>' : ''; ?>
    				<?=Format::htmlspecialchars($lead['city']); ?>
    				<?=Format::htmlspecialchars($lead['state']); ?>
    				<?=Format::htmlspecialchars($lead['zip']); ?>
    			</span>
    		</div>
    		<?php } ?>
    		<div class="keyvals__row keyvals__row--rows@sm">
    		    <span class="keyvals__key">Primary Phone</span>
                <span class="keyvals__val text -padT0@sm">
    				<?=Format::htmlspecialchars($lead['phone']); ?> (<?=!empty($lead['phone_home_status']) ? Format::htmlspecialchars($lead['phone_home_status']) : 'Not Selected'; ?>)
                </span>
    		</div>
    		<div class="keyvals__row keyvals__row--rows@sm">
    		    <span class="keyvals__key">Secondary Phone</span>
                <span class="keyvals__val text -padT0@sm">
    				<?=Format::htmlspecialchars($lead['phone_cell']); ?> (<?=!empty($lead['phone_cell_status']) ? Format::htmlspecialchars($lead['phone_cell_status']) : 'Not Selected'; ?>)
    			</span>
    		</div>
    		<div class="keyvals__row keyvals__row--rows@sm">
    		    <span class="keyvals__key">Work Phone</span>
                <span class="keyvals__val text -padT0@sm">
    				<?=$lead['phone_work']; ?> (<?=!empty($lead['phone_work_status']) ? Format::htmlspecialchars($lead['phone_work_status']) : 'Not Selected'; ?>)
    			</span>
    		</div>
    		<div class="keyvals__row keyvals__row--rows@sm">
    		    <span class="keyvals__key">Fax:</span>
                <span class="keyvals__val text -padT0@sm">
    				<?=!empty($lead['phone_fax']) ? Format::htmlspecialchars($lead['phone_fax']) : 'N/A'; ?>
    			</span>
    		</div>
        </div>
    </div>

    <?php if (!empty($customFields)) { ?>
        <div class="block custom-fields -marB">
            <div class="divider -marB"><span class="divider__label divider__label--left text text--large">Custom Fields</span></div>
            <div class="keyvals keyvals--bordered marB">
                <?php foreach ($customFields as $customField) : ?>
                    <div class="keyvals__row">
                        <span class="keyvals__key"><?=$customField->getTitle(); ?>:</span>
                        <span class="keyvals__val text">
                            <?=!empty($customValues[$customField->getName()]) ? htmlspecialchars($customValues[$customField->getName()]) : 'N/A'; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php } ?>

	<div class="block -marB">
        <div class="divider -marB"><span class="divider__label divider__label--left text text--large">Connected Networks</span></div>
        <div class="keyvals keyvals--bordered marB">
            <?php if (!empty($lead['networks'])) {
                foreach ($lead['networks'] as $network) {
                    if (!empty($network['link'])) echo '<span><a href="' . $network['link'] . '" target="_blank">';
                    echo '<img style="width: 32px; height: 32px; margin: 8px 8px 0;" src="' . Settings::getInstance()->URLS['URL_BACKEND'] . 'img/icons/' . $network['image'] . '" alt="' . $network['title'] . '">';
                    if (!empty($network['link'])) echo '</a></span>';
                    echo PHP_EOL;
                }
            } else {
                echo '<span class="keyvals__val text">None</span>';
            } ?>
        </div>
    </div>

    <?php
    if (
        !empty($can_manage_dotloop)
        && (
            !empty($dotloop_validated)
            || !empty($dotloop_token_expired)
            || !empty($dotloop_rate_limit)
        )
    ) {
    ?>
        <div class="block -marB">
            <div class="divider -marB">
                <span class="divider__label divider__label--left text text--large" style="position: relative; margin-right: 32px;">
                    DotLoop Integration
                    <div class="dotloop-circle"><span></span></div>
                </span>
            </div>
            <?php if (!empty($dotloop_validated)) { ?>
                <?php if (!empty($dotloop_contact_data['dotloop_contact_id'])) { ?>
                    <div class="keyvals keyvals--bordered -marB">
                        <div class="keyvals__row keyvals__row--rows@sm">
                            <span class="keyvals__key text text--strong -padBO@sm">Lead Linked On</span>
                            <span class="keyvals__val text -padTO@sm"><?=date('F jS, Y @g:ia', strtotime($dotloop_contact_data['timestamp_connected'])); ?></span>
                        </div>
                    </div>
                    <div class="grid">
                        <a class="btn col w1/2" title="Manage Loops" href="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>leads/lead/dotloop/?id=<?=$lead->getId(); ?>">
                            Manage Loops
                        </a>
                        <a class="btn btn--negative col w1/2" title="Unlink Lead from DotLoop" href="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>leads/lead/dotloop/unlink/?id=<?=$lead->getId(); ?>">
                            Unlink
                        </a>
                    </div>
                <?php } else { ?>
                    <div class="grid">
                        <a class="btn btn--positive col w1/1" title="Connect Lead to DotLoop" href="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>leads/lead/dotloop/?id=<?=$lead->getId(); ?>">
                            Link Lead to DotLoop
                        </a>
                    </div>
                <?php } ?>
            <?php } else if (!empty($dotloop_token_expired)) { ?>
                <p class="text text--negative">Your DotLoop API access token has expired. Please request a new token to activate DotLoop features.</p>
                <div class="grid">
                    <a class="btn btn--positive col w1/1" title="Request a new Access Token" href="<?=$dotloopApi->generateApprovalLink($_GET['id']); ?>">
                        <svg class="icon icon-add mar0">
                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use>
                        </svg> Request a New Token
                    </a>
                </div>
            <?php } else if (!empty($dotloop_rate_limit)) { ?>
                <p class="text text--negative">
                    DotLoop account's API Rate Rimit has been exceeded. Please try again<span id="dotloop-rate-timer" data-remaining="<?=ceil($dotloop_rate_limit['reset_countdown']/1000); ?>"> in <?=ceil($dotloop_rate_limit['reset_countdown']/1000); ?> seconds</span>.
                </p>
            <?php } ?>
        </div>
    <?php } ?>

<?php

if ($leadAuth->canEditLead()) {

    // Lead Action Form
    $module = new Module('lead-action-form', array(
        'path'   => Settings::getInstance()->DIRS['BACKEND'] . '/inc/modules/lead-action-form/',
        'lead'   => $lead['id'],
        'action' => isset($_GET['action']) ? $_GET['action'] : 'note'
    ));

    // Add Module to Page & Display Here
    $page->container('lead-summary')->module($module)->display();
}
?>