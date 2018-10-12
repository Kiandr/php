<form action="?submit" method="post" class="rew_check" enctype="multipart/form-data">

	<div class="bar">
		<div class="bar__title"><?= __('Add Agent'); ?></div>
		<div class="bar__actions">
			<a href="/backend/agents/" class="bar__action"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
		</div>
	</div>

	<div class="btns btns--stickyB">
        <span class="R">
		    <button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?></button>
		</span>
    </div>


    <div class="block">
        <div class="cols">
        	<div class="field col w1/2">
        		<label class="field__label"><?= __('Username'); ?> <em class="required">*</em></label>
        		<input class="w1/1" type="text" name="username" value="<?=htmlspecialchars($_POST['username']); ?>" required>
        	</div>
        	<div class="field col w1/2">
        		<label class="field__label"><?= __('Password'); ?> <em class="required">*</em></label>
        		<input class="w1/1" type="password" name="password" value="<?=htmlspecialchars($_POST['password']); ?>" required>
        	</div>
            <div class="field col w1/2">
        		<label class="field__label"><?= __('First Name'); ?> <em class="required">*</em></label>
        		<input class="w1/1" type="text" name="first_name" value="<?=htmlspecialchars($_POST['first_name']); ?>" required>
        	</div>
        	<div class="field col w1/2">
        		<label class="field__label"><?= __('Last Name'); ?> <em class="required">*</em></label>
        		<input class="w1/1" type="text" name="last_name" value="<?=htmlspecialchars($_POST['last_name']); ?>" required>
        	</div>
        	<div class="field col w1/2">
        		<label class="field__label"><?= __('Email Address'); ?> <em class="required">*</em></label>
        		<input class="w1/1" type="email" name="email" value="<?=htmlspecialchars($_POST['email']); ?>" required>
        	</div>
        	<div class="field col w1/2">
        		<label class="field__label"><?= __('SMS Email Address'); ?></label>
        		<input class="w1/1" type="email" name="sms_email" value="<?=htmlspecialchars($_POST['sms_email']); ?>">
        		<p class="text--mute">
                    <?= __('Must contain the full 10 digit phone number followed by the cell providers SMS Gateway. (eg. 1234567890@sms.provider.net).'); ?>
                    <br>
        			<?= __('Once Agent has been created, SMS notifications can be enabled from the Notification Settings page.'); ?>
                </p>
        	</div>
        </div>

    	<?php if (!empty($offices)) : ?>
    	<div class="field">
    		<label class="field__label"><?= __('Agent Office'); ?></label>
    		<select class="w1/1" name="office">
    			<option value=""><?= __('Select Agent\'s Office'); ?></option>
    			<?php foreach ($offices as $office) : ?>
    			<option value="<?=$office['value']; ?>"<?=($_POST['office'] == $office['value']) ? ' selected' : ''; ?>>
    			<?=Format::htmlspecialchars($office['title']); ?>
    			</option>
    			<?php endforeach; ?>
    		</select>
    	</div>
    	<?php endif; ?>
        <?php $page->container(REW\Core\Interfaces\Definitions\Containers\AgentInterface::AFTER_BASIC_INFO)->loadModules(true, null); ?>
    	<h3><?= __('Phone Numbers'); ?></h3>
        <div class="cols">
        	<div class="field col w1/2">
        		<label class="field__label"><?= __('Office Phone'); ?></label>
        		<input class="w1/1" type="tel" name="office_phone" value="<?=htmlspecialchars($_POST['office_phone']); ?>">
        	</div>
        	<div class="field col w1/2">
        		<label class="field__label"><?= __('Home Phone'); ?></label>
        		<input class="w1/1" type="tel" name="home_phone" value="<?=htmlspecialchars($_POST['home_phone']); ?>">
        	</div>
        	<div class="field col w1/2">
        		<label class="field__label"><?= __('Cell Phone'); ?></label>
        		<input class="w1/1" type="tel" name="cell_phone" value="<?=htmlspecialchars($_POST['cell_phone']); ?>">
        	</div>
        	<div class="field col w1/2">
        		<label class="field__label"><?= __('Fax'); ?></label>
        		<input class="w1/1" type="tel" name="fax" value="<?=htmlspecialchars($_POST['fax']); ?>">
        	</div>
        </div>
        <h3 class="panel__hd"><?= __('Agent Details'); ?></h3>
    	<div class="field">
        	<label class="field__label"><?= __('Agent Bio'); ?></label>
        	<textarea class="w1/1" name="remarks" rows="6" cols="85"><?=htmlspecialchars($_POST['remarks']); ?></textarea>
        </div>
        <div class="cols">
        	<div class="field col w1/2">
            	<label class="field__label"><?= __('Professional Title'); ?></label>
            	<input class="w1/1" type="text" name="title" value="<?=htmlspecialchars($_POST['title']); ?>">
            </div>
        	<?php if (!empty(Settings::getInstance()->MODULES['REW_AGENT_MANAGER']) || !empty($_COMPLIANCE['backend']['always_show_idx_agent'])) : ?>
        	<?php if (!empty(Settings::getInstance()->MODULES['REW_AGENT_MANAGER'])) : ?>
        	<div class="field col w1/2">
            	<label class="field__label"><?= __('Website URL'); ?></label>
            	<input class="w1/1" type="url" name="website" value="<?=htmlspecialchars($_POST['website']); ?>" placeholder="http://" pattern="https?://.+">
            </div>
        	<?php endif; ?>
        	<?php if (!empty(Settings::getInstance()->IDX_FEEDS)) { ?>
        	<?php foreach (Settings::getInstance()->IDX_FEEDS as $feed => $settings) { ?>
        	<div class="field col w1/2">
        		<label class="field__label">
        			<?=$settings['title'];?>
        			<?= __('Agent ID'); ?></label>
        		<input class="w1/1" type="text" name="agent_id[<?=htmlspecialchars($feed);?>]" value="<?=htmlspecialchars($_POST['agent_id'][$feed]);?>">
        	</div>
        	<?php } ?>
        	<?php } else { ?>
        	<div class="field col w1/2">
        		<label class="field__label"><?= __('Agent ID'); ?></label>
        		<input class="w1/1" type="text" name="agent_id[<?=htmlspecialchars(Settings::getInstance()->IDX_FEED);?>]" value="<?=htmlspecialchars($_POST['agent_id'][Settings::getInstance()->IDX_FEED]); ?>">
            </div>
    		<?php } ?>
        </div>
    		<?php endif; ?>
    		<?php if ($agentsAuth->canManageApp($authuser)) { ?>
    		<div class="field">
    			<label class="field__label"><?= __('API Source'); ?></label>
    			<select class="w1/1" name="api_source_id">
    				<?php if (!empty($api_applications)) { ?>
    				<option value="">- None -</option>
    				<?php foreach ($api_applications as $app) { ?>
    				<?php $selected = $_POST['api_source_id'] == $app['id'] ? 'selected' : '';?>
    				<option <?=$selected;?> value="<?=$app['id'];?>">
    				<?=htmlspecialchars($app['name']);?>
    				</option>
    				<?php } ?>
    				<?php } else { ?>
    				<option value="">- <?= __('No API Applications available'); ?> -</option>
    				<?php } ?>
    			</select>
                <p class="text--mute">
                    <?= __(
                        'Pick an API application here in order to make leads created via the %s automatically get assigned to this agent.',
                        '<a href="' . URL_BACKEND . 'settings/api/">API</a>'
                    );
                    ?>
                    <?= __('If multiple agents have the same API Source, leads from that source will be evenly assigned &amp; rotated between those agents.'); ?>
                </p>
    		</div>
    			<?php } ?>
    			<h3><?= __('Email Signature'); ?></h3>
    			<div class="field">
    				<label class="field__label"><?= __('Signature'); ?></label>
    				<textarea class="w1/1 tinymce email simple" id="signature" name="signature" rows="8" cols="85"><?=htmlspecialchars($_POST['signature']); ?></textarea>
    			</div>
    			<div class="field">
    				<label class="field__label"><?= __('Add Signature to Emails'); ?></label>
    				<label class="toggle">
    					<input type="radio" name="add_sig" value="Y"<?=($_POST['add_sig'] == 'Y') ? ' checked' : ''; ?>>
    					<span class="toggle__label"><?= __('Yes'); ?></span>
                    </label>
    				<label class="toggle">
    					<input type="radio" name="add_sig" value="N"<?=($_POST['add_sig'] != 'Y') ? ' checked' : ''; ?>>
    					<span class="toggle__label"><?= __('No'); ?></span>
                    </label>
    			</div>
                    <?php if (!empty($networks)) { ?>
                        <div id="social-media-section">
                            <h3><?= __('Social Media'); ?></h3>
                            <?php $even = true; foreach ($networks as $network) { ?>
                                <div class="field">
                                    <label class="field__label"><?= Format::htmlspecialchars($network['name']) ?></label>
                                    <input type="text" name="<?= Format::slugify($network['form_field']); ?>" value="<?= Format::htmlspecialchars($network['url']); ?>" placeholder="http://" class="w1/1">
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <h3 class="panel__hd"><?= __('Agent Photo'); ?></h3>
                <div class="field">
                    <input class="w1/1" name="agent_photo" type="file">
                </div>
    			<h3 class="panel__hd"><?= __('Default Settings'); ?></h3>
                <div class="cols">
        			<div class="field col w1/3">
        				<label class="field__label"><?= __('Filter'); ?></label>
        				<select class="w1/1" name="default_filter">
        					<option value="my-leads"<?=($agent['default_filter'] == 'my-leads') ? ' selected' : ''; ?>><?= __('My Leads'); ?></option>
        					<option value="inquiries"<?=($_POST['default_filter'] == 'inquiries') ? ' selected' : ''; ?>><?= __('Inquiries'); ?></option>
        					<option value="accepted"<?=($agent['default_filter'] == 'accepted') ? ' selected' : ''; ?>><?= __('Accepted'); ?></option>
        					<option value="pending"<?=($_POST['default_filter'] == 'pending') ? ' selected' : ''; ?>><?= __('Pending'); ?></option>
        					<option value="online"<?=($_POST['default_filter'] == 'online') ? ' selected' : ''; ?>><?= __('Online'); ?></option>
        				</select>
        			</div>
                    <div class="field col w1/3">
                        <label class="field__label">Sort By</label>
                        <select class="w1/1" name=default_order>
                            <option value="score"<?=($_POST['default_order'] == 'score') ? ' selected' : ''; ?>><?= __('Score'); ?></option>
                            <option value="value"<?=($_POST['default_order'] == 'value') ? ' selected' : ''; ?>><?= __('Lead Value'); ?></option>
                            <option value="name"<?=($_POST['default_order'] == 'name') ? ' selected' : ''; ?>><?= __('Name'); ?></option>
                            <option value="email"<?=($_POST['default_order'] == 'email') ? ' selected' : ''; ?>><?= __('Email'); ?></option>
                            <option value="status"<?=($_POST['default_order'] == 'status') ? ' selected' : ''; ?>><?= __('Status'); ?></option>
                            <option value="agent"<?=($_POST['default_order'] == 'agent') ? ' selected' : ''; ?>><?= __('Agent'); ?></option>
                            <option value="lender"<?=($_POST['default_order'] == 'lender') ? ' selected' : ''; ?>><?= __('Lender'); ?></option>
                            <option value="created"<?=($_POST['default_order'] == 'created') ? ' selected' : ''; ?>><?= __('Date/Time Created'); ?></option>
                            <option value="active"<?=($_POST['default_order'] == 'active') ? ' selected' : ''; ?>><?= __('Last Active'); ?></option>
                        </select>
                    </div>
                    <div class="field col w1/3">
                        <label class="field__label"><?= __('Sort Order'); ?></label>
                        <select class="w1/1" name="default_sort">
                            <option value="DESC"<?=($_POST['default_sort'] == 'DESC') ? ' selected' : ''; ?>><?= __('Descending'); ?></option>
                            <option value="ASC"<?=($_POST['default_sort'] == 'ASC') ? ' selected' : ''; ?>><?= __('Ascending'); ?></option>
                        </select>
                    </div>
                </div>
            <div class="cols">
                <div class="field col w1/2">
                    <label class="field__label"><?= __('Timezone'); ?></label>
                    <select class="w1/1" name="timezone">
                        <?php foreach ($timezone as $zone) : ?>
                        <option value="<?=$zone['id']; ?>"<?=($_POST['timezone'] == $zone['id']) ? ' selected' : ''; ?>>
                        <?=$zone['name']; ?>
                        (GMT
                        <?=$zone['gmt_off']; ?>
                        )</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field col w1/2">
                    <label class="field__label"><?= __('Page Limit'); ?></label>
                    <select class="w1/1" name="page_limit">
                        <option value="10"<?=($_POST['page_limit'] == '10') ? ' selected' : ''; ?>>10</option>
                        <option value="20"<?=($_POST['page_limit'] == '20') ? ' selected' : ''; ?>>20</option>
                        <option value="30"<?=($_POST['page_limit'] == '30') ? ' selected' : ''; ?>>30</option>
                        <option value="50"<?=($_POST['page_limit'] == '50') ? ' selected' : ''; ?>>50</option>
                        <option value="100"<?=($_POST['page_limit'] == '100') ? ' selected' : ''; ?>>100</option>
                    </select>
                </div>
            </div>
    			<h3 class="panel__hd"><?= __('Lead Settings'); ?></h3>
    			<div class="field">
    				<label class="field__label"><?= __('Auto-Assign'); ?></label>
    				<div class="toggle">
    					<input id="auto_assign_admin_true" type="radio" name="auto_assign_admin" value="true"<?=($_POST['auto_assign_admin'] == 'true') ? ' checked' : ''; ?>>
    					<label class="toggle__label" for="auto_assign_admin_true"><?= __('Yes'); ?></label>
    					<input id="auto_assign_admin_false" type="radio" name="auto_assign_admin" value="false"<?=($_POST['auto_assign_admin'] != 'true') ? ' checked' : ''; ?>>
    					<label class="toggle__label" for="auto_assign_admin_false"><?= __('No'); ?></label>
    				</div>
    				<p class="text--mute">
    					<?=tpl_lang('DESC_FORM_AUTO_ASSIGN_ADMIN'); ?>
    				</p>
    			</div>
    			<div class="field">
    				<label class="field__label"><?= __('Auto-Rotate'); ?></label>
    				<div class="toggle">
    					<input id="auto_rotate_true" type="radio" name="auto_rotate" value="true"<?=($_POST['auto_rotate'] == 'true') ? ' checked' : ''; ?>>
    					<label class="toggle__label" for="auto_rotate_true"><?= __('Yes'); ?></label>
    					<input id="auto_rotate_false" type="radio" name="auto_rotate" value="false"<?=($_POST['auto_rotate'] != 'true') ? ' checked' : ''; ?>>
    					<label class="toggle__label" for="auto_rotate_false"><?= __('No'); ?></label>
    				</div>
    				<p class="text--mute">
    					<?=tpl_lang('DESC_FORM_AUTO_ROTATE'); ?>
    				</p>
    			</div>
    			<div class="field">
    				<label class="field__label"><?= __('Auto-Opt-Out'); ?></label>
    				<div class="toggle">
    					<input type="radio" id="auto_optout_true" name="auto_optout" value="true"
                                    	<?=($_POST['auto_assign_admin'] != 'true' && $_POST['auto_rotate'] != 'true') ? ' disabled' : ''; ?>
                                    	<?=($_POST['auto_optout'] == 'true') ? ' checked' : ''; ?>
                                    >
    					<label class="toggle__label" for="auto_optout_true"><?= __('Yes'); ?></label>
    					<input type="radio" id="auto_optout_false" name="auto_optout" value="false"
                                    	<?=($_POST['auto_assign_admin'] != 'true' && $_POST['auto_rotate'] != 'true') ? ' disabled' : ''; ?>
                                    	<?=($_POST['auto_optout'] != 'true') ? ' checked' : ''; ?>
                                    >
    					<label class="toggle__label" for="auto_optout_false"><?= __('No'); ?></label>
    				</div>
    				<p class="text--mute">
    					<?=tpl_lang('DESC_FORM_AUTO_OPTOUT'); ?>
    				</p>
    			</div>

                <div class="field">
                    <label class="field__label"><?= __('Auto Generated Searches'); ?></label>
                    <div class="toggle">
                        <input type="radio" id="auto_search_true" name="auto_search" value="true"
                            <?=($_POST['auto_search'] != 'false') ? ' checked' : ''; ?>
                        >
                        <label class="toggle__label" for="auto_search_true" class="boolean"><?= __('Yes'); ?></label>
                        <input type="radio" id="auto_search_false" name="auto_search" value="false"
                            <?=($_POST['auto_search'] == 'false') ? ' checked' : ''; ?>
                        >
                        <label class="toggle__label" for="auto_search_false" class="boolean"><?= __('No'); ?></label>
                    </div>
                    <p class="text--mute"><?=tpl_lang('DESC_FORM_AUTO_SEARCH'); ?></p>
                </div>

    			<h3 class="panel__hd"><?= __('Agent Settings'); ?></h3>
    			<?php if (!empty(Settings::getInstance()->MODULES['REW_AGENT_MANAGER'])) : ?>
    			<div class="field">
    				<label class="field__label"><?= __('Show on Agents Page'); ?></label>
    				<div class="toggle">
    					<input id="display_true" type="radio" name="display" value="Y"<?=($_POST['display'] == 'Y') ? ' checked' : ''; ?>>
    					<label class="toggle__label" for="display_true"><?= __('Yes'); ?></label>
    					<input id="display_false" type="radio" name="display" value="N"<?=($_POST['display'] != 'Y') ? ' checked' : ''; ?>>
    					<label class="toggle__label" for="display_false"><?= __('No'); ?></label>
    				</div>
    			</div>
    			<?php endif; ?>
    			<?php if (!empty(Settings::getInstance()->MODULES['REW_AGENT_SPOTLIGHT'])) : ?>
    			<div class="field">
    				<label class="field__label"><?= __('Display on Agents Feature'); ?></label>
    				<div class="toggle">
    					<input id="display_feature_true" type="radio" name="display_feature" value="Y"<?=($_POST['display_feature'] == 'Y') ? ' checked' : ''; ?>>
    					<label class="toggle__label" for="display_feature_true"><?= __('Yes'); ?></label>
    					<input id="display_feature_false" type="radio" name="display_feature" value="N"<?=($_POST['display_feature'] != 'Y') ? ' checked' : ''; ?>>
    					<label class="toggle__label" for="display_feature_false"><?= __('No'); ?></label>
    				</div>
    			</div>
    			<?php endif; ?>
    			<?php if (!empty(Settings::getInstance()->MODULES['REW_AGENT_CMS'])) : ?>
    			<div class="field">
    				<label class="field__label"><?= __('Agent Website Enabled'); ?></label>
    				<div class="toggle">
    					<input id="settings_cms_true" type="radio" name="cms" value="true"<?=($_POST['cms'] == 'true') ? ' checked' : ''; ?>>
    					<label class="toggle__label" for="settings_cms_true"><?= __('Yes'); ?></label>
    					<input id="settings_cms_false" type="radio" name="cms" value="false"<?=($_POST['cms'] != 'true') ? ' checked' : ''; ?>>
    					<label class="toggle__label" for="settings_cms_false"><?= __('No'); ?></label>
    				</div>
    			</div>
    			<div id="agent-cms"<?=($_POST['cms'] == 'true') ? '' : ' class="hidden"'; ?>>
    				<div class="field">
    					<input type="hidden" name="mls_boards" value="">
    					<label><?= __('Please provide the MLS board(s) which this agent is a member of'); ?>: *</label>
    					<ul style="list-style: none; padding: 0;">
    						<?php foreach ($idx_feeds as $name => $title) { ?>
                                <?php $checked = isset($_POST['requested_feeds'][$name]); ?>
                                <li class="-marB8">
                                    <input type="checkbox" value="<?=$name; ?>" name="requested_feeds[<?=$name; ?>]" id="requested_feeds_<?=$name; ?>"<?=$checked ? ' checked' : ''; ?>>
                                    <label class="boolean toggle__label" for="requested_feeds_<?=$name; ?>"><?=$title; ?></label>
                                    <input name="feeds_agent[<?=$name; ?>]" placeholder="Agent ID For <?=strtoupper($name); ?>" value="<?=Format::htmlspecialchars($_POST['feeds_agent'][$name]); ?>"<?=$checked ? ' required' : ' class="hidden"'; ?>>
                                </li>
    						<?php } ?>
    					</ul>
    				</div>
    				<div class="field">
    					<label><?= __('Agent Website IDXs'); ?></label>
    					<? if (Settings::isREW()) { ?>
    					<p><?= __('This section is editable by REW staff only. Check with our IDX team before enabling a specific feed for an agent website.'); ?></p>
    					<? } else { ?>
    					<p><?= __('This section is editable by REW staff only. Please contact customer support to enable IDX access for this agent website.'); ?></p>
    					<? } ?>
    					<ul style="list-style: none; padding: 0;">
    						<?php foreach ($idx_feeds as $name => $title) { ?>
                                <li class="-marB8">
                                    <input type="checkbox" <?=Settings::isREW() ? "" : "disabled"?> value="<?=$name; ?>" name="feeds[]" id="settings_feeds_<?=$name; ?>">
                                    <label class="boolean toggle__label" for="settings_feeds_<?=$name; ?>"><?=$title; ?></label>
                                </li>
    						<?php } ?>
    					</ul>
    				</div>
                    <div>
                        <label class="-marB8 dB"><?=__('Agent Website Addons'); ?></label>
                        <?php if (!empty($addons)) { ?>
                            <ul style="list-style: none; padding: 0;">
                                <?php foreach ($addons as $addon) { ?>
                                    <li class="-marB8">
                                        <label>
                                            <input type="checkbox" <?=(isset($_POST['cms_addons']) && in_array($addon['db_val'], $_POST['cms_addons'])) ? ' checked' : ''; ?> value="<?=$addon['db_val']; ?>" name="cms_addons[]">
                                            <span class="toggle__label"><?=__($addon['title']); ?></span>
                                        </label>
                                    </li>
                                <?php } ?>
                            </ul>
                        <?php } else { ?>
                            <div class="text--mute">No Addons Available</div>
                        <?php } ?>
                    </div>
    				<div class="field">
    					<label class="field__label"><?= __('Agent Link'); ?> <em>*</em></label>
    					<input class="w1/1" name="cms_link" value="<?=htmlspecialchars($_POST['cms_link']); ?>" data-link="<?=sprintf(URL_AGENT_SITE, '*'); ?>">
    				</div>
    				<div class="field">
    					<label class="field__label"><?= __('Agent Site'); ?></label>
    					<a href="javascript:void(0);" id="account-link">
    					<?=sprintf(URL_AGENT_SITE, $_POST['cms_link'] ? Format::htmlspecialchars($_POST['cms_link']) : '*'); ?>
    					</a>
                    </div>
    			</div>
    			<?php endif; ?>
    			<?php if (!empty(Settings::getInstance()->MODULES['REW_REMAX_LAUNCHPAD'])) { ?>
    			<h3 class="panel__hd"><?= __('SSO Settings'); ?></h3>
    			<div class="field">
    				<label class="field__label"><?= __('RE/MAX Launchpad Username'); ?></label>
    				<input class="w1/1" type="text" name="remax_launchpad_username" value="<?=htmlspecialchars($_POST['remax_launchpad_username']); ?>" maxlength="100">
    				<p class="text--mute"><?= __('Setting up your RE/MAX Launchpad username here will allow you to directly access this REW backend account from the corresponding Launchpad account, without prompting you for your REW credentials.'); ?></p>
    			</div>
    		<?php } ?>
    		<?php if (!empty(Settings::getInstance()->MODULES['REW_SHOWING_SUITE'])) { ?>
    		<h3><?= __('Showing Suite Settings'); ?></h3>
    		<div class="field">
    			<label class="field__label"><?= __('Email Address'); ?></label>
    			<input class="w1/1" type="email" name="showing_suite_email" value="<?=htmlspecialchars($_POST['showing_suite_email']); ?>">
    			<p class="text--mute"><?= __('This is the email address that is tied to your Showing Suite account. You may leave this blank if it is the same as your agent account\'s email address.'); ?></p>
    		</div>
    		<?php } ?>
        <?php $page->container(REW\Core\Interfaces\Definitions\Containers\AgentInterface::AFTER_SETTINGS)->loadModules(true, null); ?>

    </div>

</form>
