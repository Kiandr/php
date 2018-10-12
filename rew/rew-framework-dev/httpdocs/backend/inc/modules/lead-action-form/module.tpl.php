<div id="quick_note" class="hidden">
    <div class="tabbed">

        <!-- Action Types -->
        <ul class="nav nav_tabbed" id="quick_note_tabset">
            <li<?=($action == 'note') ? ' class="current ui-tabs-selected"' : ''; ?>><a href="#quick_note_note"><?= __('Note'); ?></a></li>
            <li<?=($action == 'call') ? ' class="current ui-tabs-selected"' : ''; ?>><a href="#quick_note_call"><?= __('Call'); ?></a></li>
	        <?php if ($authuser->isAgent() || $authuser->isAssociate()) { ?>
	        	<li<?=($action == 'reminder') ? ' class="current ui-tabs-selected"' : ''; ?>><a href="#quick_note_reminder"><?= __('Reminder'); ?></a></li>
	            <li<?=($action == 'listing') ? ' class="current ui-tabs-selected"' : ''; ?>><a href="#quick_note_listing"><?= __('Listing'); ?></a></li>
	            <li<?=($action == 'search') ? ' class="current ui-tabs-selected"' : ''; ?>><a href="#quick_note_search"><?= __('Search'); ?></a></li>
			<?php  } ?>
        </ul>

        <!-- Lead Note -->
        <div id="quick_note_note" class="quick_note_content<?=($action != 'note') ? ' hidden' : ''; ?>">
            <form autocomplete="off" class="rew_check">
                <input type="hidden" name="action" value="note">
                <input type="hidden" name="lead" value="<?=$lead['id']; ?>">
                <div class="field">
                	<textarea class="w1/1" name="note" cols="24" rows="8" placeholder="<?= __('Add Lead Note...'); ?>" required></textarea>
                </div>
                <div class="btns">
                    <button type="submit" class="btn btn--strong"><?= __('Save'); ?></button>
                    <label class="boolean R"><input type="checkbox" name="share" value="true" checked> <?= __('Share this Note'); ?></label>
                    <div class="action-message"></div>
                </div>
            </form>
        </div>

        <!-- Lead Phone Call -->
        <div id="quick_note_call" class="quick_note_content<?=($action != 'call') ? ' hidden' : ''; ?>">
            <h2 class="phone">
				<?php
					$phone_numbers = [];
					if ($lead['phone']) $phone_numbers[] = 'Primary: ' . $lead['phone'];
					if ($lead['phone_cell']) $phone_numbers[] = 'Secondary: ' . $lead['phone_cell'];
					echo !empty($phone_numbers) ? implode('<br>', $phone_numbers) : __('(No Number Provided)');
				?>
            </h2>
            <form autocomplete="off" class="rew_check">
                <input type="hidden" name="action" value="call">
                <input type="hidden" name="lead" value="<?=$lead['id']; ?>">
                <div class="field">
                    <select class="w1/1" name="type" required>
                        <option value="" selected><?= __('Outcome'); ?>...</option>
                        <?php foreach ($phone['types'] as $type) : ?>
                            <option value="<?=$type['value']; ?>"><?=$type['title']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field">
                    <textarea class="w1/1" name="details" cols="24" rows="4" placeholder="<?= __('Call Details'); ?>" required></textarea>
                </div>
                <div class="btns">
                    <button type="submit" class="btn btn--strong"><?= __('Save'); ?></button>
                    <div class="action-message"></div>
                </div>
            </form>
        </div>

		<?php if ($authuser->isAgent() || $authuser->isAssociate()) { ?>

	        <!-- Lead Reminder -->
	        <div id="quick_note_reminder" class="quick_note_content<?=($action != 'reminder') ? ' hidden' : ''; ?>">
	            <form autocomplete="off" class="rew_check">
	                <input type="hidden" name="action" value="reminder">
	                <input type="hidden" name="lead" value="<?=$lead['id']; ?>">
                    <div class="field">
                        <div id="date-quick" class="btns--compact clickbuttons">
                            <?php foreach ($reminder['dates'] as $date) : ?><button class="btn quickpick<?=($date['timestamp'] == $reminder['timestamp']) ? ' active' : ''; ?>" type="button"
                                    data-timestamp="<?=$date['timestamp']; ?>"
                                    data-date="<?=date('l, F jS Y', $date['timestamp']); ?>"
                                ><?=$date['title']; ?></button><?php endforeach; ?><a href="#" class="btn quickpick custom">&hellip;</a>
                        </div>
                    </div>
                    <div class="field">
                        <div id="date-custom">
                            <div class="date"><input class="w1/1" name="timestamp" value="<?=date('D, M. j, Y', $reminder['timestamp']); ?>" readonly></div>
                        </div>
                    </div>
                    <div class="field">
                        <select class="w1/1" name="type" required>
                            <?php if (!empty($reminder['types'])) : ?>
                                <?php foreach ($reminder['types'] as $type) : ?>
                                    <option value="<?=$type['value']; ?>"><?=$type['title']; ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="field">
                        <textarea class="w1/1" name="details" cols="24" rows="6" placeholder="<?= __('Reminder Details'); ?>" required></textarea>
                    </div>
                    <div class="buttonset">
                        <?php if (!empty($can_share)) { ?>
                            <label class="boolean R"><input class="complete" type="checkbox" name="share" value="share"> <?= __('Share with Agent'); ?></label>
                        <?php } ?>
                        <button type="submit" class="btn btn--strong"><?= __('Save'); ?></button>
                        <div class="action-message"></div>
                    </div>
	            </form>
	        </div>

	        <!-- Lead Listing -->
	        <div id="quick_note_listing" class="quick_note_content<?=($action != 'listing') ? ' hidden' : ''; ?>">
	            <form autocomplete="off" class="rew_check">
	                <input type="hidden" name="action" value="listing">
	                <input type="hidden" name="lead" value="<?=$lead['id']; ?>">
                    <?php if (!empty(Settings::getInstance()->IDX_FEEDS)) { ?>
                        <select class="w1/1" name="feed" required>
                            <?php foreach (Settings::getInstance()->IDX_FEEDS as $feed => $settings) { ?>
                                <?php $selected = Settings::getInstance()->IDX_FEED === $feed ? 'selected' : ''; ?>
                                <option <?=$selected;?> value="<?=$feed;?>"><?=$settings['title'];?></option>
                            <?php } ?>
                        </select>
                    <?php } ?>
                    <div class="field">
                        <input class="w1/1 autocomplete listing" name="mls_number" value="" placeholder="<?= __('Enter MLS&reg; Number or Street Address'); ?>" required>
                    </div>
                    <div id="quick_note_listing_message">
                        <textarea class="w1/1" name="message" cols="24" rows="6" placeholder="<?= __('Include this Message'); ?>..."></textarea>
                        <p class="tip show">Available Tags: {signature}, {first_name}, {last_name}, {email}, {verify}</p>
                    </div>
                    <div class="buttonset">
                        <label class="boolean R">
                            <input type="checkbox" name="notify" value="true" checked> <?= __('Send Listing Email to Lead'); ?>
                        </label>
                        <button type="submit" class="btn btn--strong"><?= __('Save'); ?></button>
                        <div class="action-message"></div>
                    </div>
	            </form>
	        </div>

            <!-- Lead Search -->
            <div id="quick_note_search" class="quick_note_content<?=($action != 'search') ? ' hidden' : ''; ?>">
                <form action="<?=Settings::getInstance()->SETTINGS['URL_IDX_SEARCH']; ?>" autocomplete="off" class="rew_check">
                    <input type="hidden" name="lead_id" value="<?=$lead['id']; ?>">
                    <input type="hidden" name="create_search" value="true">
                    <div class="buttonset">
                        <button type="submit" class="btn btn--strong"><?= __('Create Saved Search'); ?></button>
                        <div class="action-message"></div>
                    </div>
                </form>
            </div>

        <?php } ?>

    </div>
</div>