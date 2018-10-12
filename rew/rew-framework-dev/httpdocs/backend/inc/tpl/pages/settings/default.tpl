<form action="?submit" method="post" class="rew_check">

	<div class="bar">
    	<div class="bar__title"><?= __('Lead Manager Settings'); ?></div>
	</div>

<div class="block">

	<div class="field">
		<label class="field__label"><?= __('Lead Auto-Assignment'); ?></label>
		<label for="auto_assign_true">
		    <input type="radio" name="auto_assign" id="auto_assign_true" value="true"<?=($settings['auto_assign'] == 'true') ? ' checked' : ''; ?>>
		    <span class="toggle__label"><?= __('On'); ?></span>
		</label>
		<label for="auto_assign_false">
		    <input type="radio" name="auto_assign" id="auto_assign_false" value="false"<?=($settings['auto_assign'] != 'true') ? ' checked' : ''; ?>>
		    <span class="toggle__label"><?= __('Off'); ?></span>
		</label>
        <?php if ($settings['auto_assign'] == 'true') { ?>
            <p>
                <?= __(
                    'Currently %s %s in auto-assignment.',
                    '<a href="' . URL_BACKEND . 'agents/?filter=auto_assign">' . count($auto_assign) . '</a>',
                    n__('agent', 'agents', count($auto_assign))
                ); ?>
            </p>
        <?php } ?>
		<p class="text--mute"><?= __('If this is on, new leads will be assigned evenly to the agents who have auto-assign enabled.'); ?></p>
	</div>

	<div class="field">
		<label class="field__label"><?= __('Lead Auto-Rotation'); ?></label>
		<label class="label" for="auto_rotate_true">
		    <input type="radio" name="auto_rotate" id="auto_rotate_true" value="true"<?=($settings['auto_rotate'] == 'true') ? ' checked' : ''; ?>>
		    <span class="toggle__label"><?= __('On'); ?></span>
        </label>
		<label class="label" for="auto_rotate_false">
		    <input type="radio" name="auto_rotate" id="auto_rotate_false" value="false"<?=($settings['auto_rotate'] != 'true') ? ' checked' : ''; ?>>
		    <span class="toggle__label"><?= __('Off'); ?></span>
        </label>
        <?php if ($settings['auto_rotate'] == 'true') { ?>
            <p>
                <?= __(
                    'Currently %s %s in rotation.',
                    '<a href="' . URL_BACKEND . 'agents/?filter=auto_rotate">' . count($auto_rotate) . '</a>',
                    n__('agent', 'agents', count($auto_rotate))
                ); ?>
            </p>
        <?php } ?>
		<p class="text--mute">
            <?= __('If this is on, leads that are still %s will be rotated to the next agent after the Rotation Time.',
                '<span class="status status--pending" style="display: inline-block;">' . __('Pending') . '</span>'); ?>
        </p>
	</div>

	<?php if (!empty(Settings::getInstance()->MODULES['REW_SHARK_TANK'])) { ?>
	<div class="field">
		<label class="field__label"><?= __('Shark Tank Lead Distribution'); ?></label>
		<div>
			<label class="toggle" for="shark_tank_true">
				<input type="radio" name="shark_tank" id="shark_tank_true" value="true"<?=($settings['shark_tank'] == 'true') ? ' checked' : ''; ?>>
				<span class="toggle__label"><?= __('On'); ?></span>
			</label>
			<label class="toggle" for="shark_tank_false">
				<input type="radio" name="shark_tank" id="shark_tank_false" value="false"<?=($settings['shark_tank'] != 'true') ? ' checked' : ''; ?>>
				<span class="toggle__label"><?= __('Off'); ?></span>
			</label>
		</div>
		<p class="text--mute"><?= __('If this is on, new Un-Assigned leads will be entered into the Shark Tank. Agents who are granted permissions to the Shark Tank will receive notifications when a new lead is entered, and will be able to accept these leads on a first-come-first-serve basis.'); ?></p>
		<?php if ($settings['auto_assign'] == 'true' && $settings['shark_tank'] == 'true') { ?>
		<p class="text--strong"><?= __('Warning! You have enabled both lead auto-assignment and shark tank setting. If you have one or more agents opted into lead auto-assignment, then the leads will never reach shark tank.'); ?></p>
		<?php } ?>
	</div>
	<?php } ?>

	<div id="auto-rotate-settings"<?=($settings['auto_rotate'] != 'true') ? ' class="hidden"' : ''; ?>>
		<div class="field">
			<label class="field__label"><?= __('Rotation Days'); ?></label>
			<?php
				// Days of the Week
				echo '<div class="gridded-inline">';
				$start = strtotime('last Sunday');
				for ($d = 0; $d < 7; $d ++) {
					$checked = is_array($settings['auto_rotate_days']) && in_array($d, $settings['auto_rotate_days']) ? ' checked' : '';
					echo '<label><input type="checkbox" name="auto_rotate_days[]" value="' . $d . '"' . $checked . '><span class="toggle__label"> ' . date('l', $start) . '</span></label> ';
					$start += 60 * 60 * 24;
				}
				echo '</div>';

            ?>
			<p class="text--mute"><?= __('These are the days that lead auto-rotation is in effect.'); ?></p>
		</div>

		<div class="field">
			<label class="field__label"><?= __('Rotation Hours'); ?></label>
			<div class="slider">
				<input type="text" name="auto_rotate_hours" value="<?=htmlentities($settings['auto_rotate_hours']); ?>">
			</div>
			<p class="text--mute"><?= __('These are the hours of day that lead auto-rotation is in effect.'); ?></p>
		</div>

		<div class="field">
			<label class="field__label"><?= __('Rotation Time'); ?></label>
			<select class="w1/1" name="auto_rotate_frequency">
				<option value="15"<?=($settings['auto_rotate_frequency'] == 15) ? ' selected' : ''; ?>>15 <?= __('Minutes'); ?></option>
				<option value="30"<?=($settings['auto_rotate_frequency'] == 30) ? ' selected' : ''; ?>>30 <?= __('Minutes'); ?></option>
				<option value="60"<?=($settings['auto_rotate_frequency'] == 60) ? ' selected' : ''; ?>>1 <?= __('Hour'); ?></option>
				<option value="120"<?=($settings['auto_rotate_frequency'] == 120) ? ' selected' : ''; ?>>2 <?= __('Hours'); ?></option>
				<option value="180"<?=($settings['auto_rotate_frequency'] == 180) ? ' selected' : ''; ?>>3 <?= __('Hours'); ?></option>
				<option value="240"<?=($settings['auto_rotate_frequency'] == 240) ? ' selected' : ''; ?>>4 <?= __('Hours'); ?></option>
				<option value="300"<?=($settings['auto_rotate_frequency'] == 300) ? ' selected' : ''; ?>>5 <?= __('Hours'); ?></option>
				<option value="360"<?=($settings['auto_rotate_frequency'] == 360) ? ' selected' : ''; ?>>6 <?= __('Hours'); ?></option>
				<option value="420"<?=($settings['auto_rotate_frequency'] == 420) ? ' selected' : ''; ?>>7 <?= __('Hours'); ?></option>
				<option value="480"<?=($settings['auto_rotate_frequency'] == 480) ? ' selected' : ''; ?>>8 <?= __('Hours'); ?></option>
				<option value="540"<?=($settings['auto_rotate_frequency'] == 540) ? ' selected' : ''; ?>>9 <?= __('Hours'); ?></option>
				<option value="600"<?=($settings['auto_rotate_frequency'] == 600) ? ' selected' : ''; ?>>10 <?= __('Hours'); ?></option>
				<option value="660"<?=($settings['auto_rotate_frequency'] == 660) ? ' selected' : ''; ?>>11 <?= __('Hours'); ?></option>
				<option value="720"<?=($settings['auto_rotate_frequency'] == 720) ? ' selected' : ''; ?>>12 <?= __('Hours'); ?></option>
				<option value="1440"<?=($settings['auto_rotate_frequency'] == 1440) ? ' selected' : ''; ?>>24 <?= __('Hours'); ?></option>
				<option value="2880"<?=($settings['auto_rotate_frequency'] == 2880) ? ' selected' : ''; ?>>48 <?= __('Hours'); ?></option>
			</select>
			<p class="text--mute"><?= __('After this time, pending leads will be re-assigned to next agent.'); ?></p>
		</div>

		<div class="field">
			<label class="field__label"><?= __('Rotation Un-Assign'); ?></label>
			<label class="toggle">
				<input type="checkbox" name="auto_rotate_unassign" value="true"<?=($settings['auto_rotate_unassign'] == 'true') ? ' checked' : ''; ?>>
				<span class="field__label"><?= __('Un-Assign Rotated Leads'); ?></span>
            </label>
			<p class="text--mute"><?= __('If checked, rotated leads will be un-assigned instead of assigned to the next agent.'); ?></p>
		</div>

	</div>

	<?php if (!empty(Settings::getInstance()->MODULES['REW_LENDERS_MODULE'])) { ?>
	<div class="field">
		<label class="field__label"><?= __('Lender Auto-Assignment'); ?></label>
		<label class="toggle" for="auto_assign_lenders_true">
		    <input type="radio" name="auto_assign_lenders" id="auto_assign_lenders_true" value="true"<?=($settings['auto_assign_lenders'] == 'true') ? ' checked' : ''; ?>>
		    <span class="toggle__label"><?= __('On'); ?></span>
        </label>
		<label class="toggle" for="auto_assign_lenders_false">
		    <input type="radio" name="auto_assign_lenders" id="auto_assign_lenders_false" value="false"<?=($settings['auto_assign_lenders'] != 'true') ? ' checked' : ''; ?>>
		    <span class="toggle__label"><?= __('Off'); ?></span>
        </label>
        <?php if ($settings['auto_assign_lenders'] == 'true' && !empty($auto_assign_lenders)) { ?>
            <p class="text--mute">
                <?= __(
                    'Currently %s %s in auto-assignment.',
                    '<strong>' . count($auto_assign_lenders) . '</strong>',
                    n__('lender', 'lenders', count($auto_assign_lenders))
                ); ?>
            </p>
        <?php } ?>
		<p class="text--mute"><?= __('If this is on, new leads will be assigned evenly to the lenders who have auto-assign enabled.'); ?></p>
	</div>
	<?php } ?>

	<div class="field">
		<label class="field__label"><?= __('Lead Auto Generated Searches'); ?></label>
		<div>
			<label class="toggle" for="auto_generated_searches_true">
			    <input type="radio" name="auto_generated_searches" id="auto_generated_searches_true" value="true"<?=($settings['auto_generated_searches'] == 'true') ? ' checked' : ''; ?>>
			    <span class="toggle__label"><?= __('On'); ?></span>
			</label>
			<label class="toggle" for="auto_generated_searches_false">
			    <input type="radio" name="auto_generated_searches" id="auto_generated_searches_false" value="false"<?=($settings['auto_generated_searches'] != 'true') ? ' checked' : ''; ?>>
			    <span class="toggle__label"><?= __('Off'); ?></span>
			</label>
		</div>
		<p class="text--mute"><?= __('If this is on, leads will have a saved search automatically generated if they have never saved a search and have viewed 20 listings'); ?>.</p>
	</div>

	<h3><?= __('Anti-Spam Settings'); ?></h3>
	<div class="field">
		<label class="field__label"><?= __('Confirmation of Consent for Emailing Marketing'); ?></label>
		<textarea class="w1/1" rows="2" name="anti_spam_consent_text" placeholder="<?= $consentMessage; ?>" ><?=$settings['anti_spam_consent_text']; ?></textarea>
		<p class="text--mute"><?= __('This text will display next to the email marketing opt-in checkbox'); ?>.</p>
	</div>
	<?php $isCanadian = Settings::getInstance()->LANG == 'en-CA'; ?>
	<div class="field">
		<label class="field__label"><?= __('Opt-In to Emails by Default'); ?></label>
		<div>
			<label class="toggle" for="anti_spam_optin_true">
			    <input<?=$isCanadian ? ' disabled' : ''; ?> type="radio" name="anti_spam_optin" id="anti_spam_optin_true" value="in"<?=($settings['anti_spam_optin'] == 'in') ? ' checked' : ''; ?>>
			    <span class="toggle__label"><?= __('On'); ?></span>
			</label>
			<label class="toggle" for="anti_spam_optin_false">
			    <input<?=$isCanadian ? ' disabled checked' : ''; ?> type="radio" name="anti_spam_optin" id="anti_spam_optin_false" value="out"<?=($settings['anti_spam_optin'] != 'in') ? ' checked' : ''; ?>>
			    <span class="toggle__label"><?= __('Off'); ?></span>
			</label>
		</div>

        <p class="text--mute"><?= __('This will determine whether the email opt-in checkboxes on forms will default to checked or un-checked.'); ?>
            <br>
            <?= $isCanadian ?
                __('%s law requires this to be disabled.', '<a target="_blank" href="http://fightspam.gc.ca/">' . __('CASL') . '</a>') :
                __('Know your country\'s anti-spam laws - they may require this to be disabled.'); ?>
        </p>

	</div>
	<?php if (Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO']) { ?>
	<div class="field">
		<label class="field__label"><?= __('Confirmation of Consent for Text Messaging'); ?></label>
		<textarea class="w1/1" rows="2" name="anti_spam_sms_consent_text" placeholder="<?= __('I consent to receiving text messages from this site.'); ?>"><?=$settings['anti_spam_sms_consent_text']; ?></textarea>
		<p class="text--mute"><?= __('This text will display next to the text messaging opt-in checkbox'); ?>.</p>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Opt-In to Texts by Default'); ?></label>
		<div>
			<label class="toggle" for="anti_spam_sms_optin_1">
			    <input<?=$isCanadian ? ' disabled' : ''; ?> type="radio" name="anti_spam_sms_optin" id="anti_spam_sms_optin_1" value="in"<?=($settings['anti_spam_sms_optin'] == 'in') ? ' checked' : ''; ?>>
			    <span class="toggle__label"><?= __('On'); ?></span>
			</label>
			<label class="toggle" for="anti_spam_sms_optin_0">
			    <input<?=$isCanadian ? ' disabled checked' : ''; ?> type="radio" name="anti_spam_sms_optin" id="anti_spam_sms_optin_0" value="out"<?=($settings['anti_spam_sms_optin'] != 'in') ? ' checked' : ''; ?>>
			    <span class="toggle__label"><?= __('Off'); ?></span>
			</label>
		</div>
        <p class="text--mute"><?= __('This will determine whether the text messaging opt-in checkbox will default to checked or un-checked.'); ?>
            <br>
            <?= $isCanadian ?
                __('%s law requires this to be disabled.', '<a target="_blank" href="http://fightspam.gc.ca/">' . __('CASL') . '</a>') :
                __('Know your country\'s anti-spam laws - they may require this to be disabled.');
            ?>
        </p>
	</div>
	<?php } ?>
	<h3><?= __('Automated Agent Opt-Out'); ?></h3>
	<div class="field">
		<div>
			<label class="toggle" for="auto_optout_true">
			    <input type="radio" name="auto_optout" id="auto_optout_true" value="true"<?=($settings['auto_optout'] == 'true') ? ' checked' : ''; ?>>
			    <span class="toggle__label"><?= __('On'); ?></span>
			</label>
			<label class="toggle" for="auto_optout_false">
			    <input type="radio" name="auto_optout" id="auto_optout_false" value="false"<?=($settings['auto_optout'] != 'true') ? ' checked' : ''; ?>>
			    <span class="toggle__label"><?= __('Off'); ?></span>
			</label>
		</div>
		<?php if ($settings['auto_optout'] == 'true') { ?>
            <p>
                <?= __(
                    'Currently %s %s in automated opt-out.',
                    '<a href="' . URL_BACKEND . 'agents/?filter=auto_optout">' . count($auto_optout) . '</a>',
                    n__('agent', 'agents', count($auto_optout))
                ); ?>
            </p>
		<?php } ?>
		<p class="text--mute"><?= __('This feature will automatically remove Agents from receiving Auto-assigned and Auto-rotated Leads if they do not perform certain actions within a set timeframe.'); ?></p>
	</div>

	<div id="auto-optout-settings"<?=($settings['auto_optout'] != 'true') ? ' class="hidden"' : ''; ?>>

		<div class="field">
			<label class="field__label"><?= __('Days in Effect'); ?></label>
			<?php
				// Days of the Week
				echo '<div>';
				$start = strtotime('last Sunday');
				for ($d = 0; $d < 7; $d ++) {
					$checked = is_array($settings['auto_optout_days']) && in_array($d, $settings['auto_optout_days']) ? ' checked' : '';
					echo '<label><input type="checkbox" name="auto_optout_days[]" value="' . $d . '"' . $checked . '> ' . date('l', $start) . '</label> ';
					$start += 60 * 60 * 24;
				}
				echo '</div>';
				?>
		</div>


		<div class="field">
			<label class="field__label"><?= __('Hours in Effect'); ?></label>
			<div class="slider">
				<input type="text" name="auto_optout_hours" value="<?=htmlentities($settings['auto_optout_hours']); ?>">
			</div>
		</div>


		<div class="field">
			<label class="field__label"><?= __('Timeframe'); ?></label>
			<select class="w1/1" name="auto_optout_time">
				<option value="60"<?=($settings['auto_optout_time'] == 60) ? ' selected' : ''; ?>>1 <?= __('Hour'); ?></option>
				<option value="120"<?=($settings['auto_optout_time'] == 120) ? ' selected' : ''; ?>>2 <?= __('Hours'); ?></option>
				<option value="180"<?=($settings['auto_optout_time'] == 180) ? ' selected' : ''; ?>>3 <?= __('Hours'); ?></option>
				<option value="240"<?=($settings['auto_optout_time'] == 240) ? ' selected' : ''; ?>>4 <?= __('Hours'); ?></option>
				<option value="300"<?=($settings['auto_optout_time'] == 300) ? ' selected' : ''; ?>>5 <?= __('Hours'); ?></option>
				<option value="360"<?=($settings['auto_optout_time'] == 360) ? ' selected' : ''; ?>>6 <?= __('Hours'); ?></option>
				<option value="420"<?=($settings['auto_optout_time'] == 420) ? ' selected' : ''; ?>>7 <?= __('Hours'); ?></option>
				<option value="480"<?=($settings['auto_optout_time'] == 480) ? ' selected' : ''; ?>>8 <?= __('Hours'); ?></option>
				<option value="540"<?=($settings['auto_optout_time'] == 540) ? ' selected' : ''; ?>>9 <?= __('Hours'); ?></option>
				<option value="600"<?=($settings['auto_optout_time'] == 600) ? ' selected' : ''; ?>>10 <?= __('Hours'); ?></option>
				<option value="660"<?=($settings['auto_optout_time'] == 660) ? ' selected' : ''; ?>>11 <?= __('Hours'); ?></option>
				<option value="720"<?=($settings['auto_optout_time'] == 720) ? ' selected' : ''; ?>>12 <?= __('Hours'); ?></option>
				<option value="1440"<?=($settings['auto_optout_time'] == 1440) ? ' selected' : ''; ?>>24 <?= __('Hours'); ?></option>
				<option value="2880"<?=($settings['auto_optout_time'] == 2880) ? ' selected' : ''; ?>>48 <?= __('Hours'); ?></option>
			</select>
		</div>


		<div class="field">
			<label class="field__label"><?= __('Perform At Least Once'); ?></label>
			<div>
				<?php
					// Events for Agent Opt-Out Feature
					$events = Backend_Agent_OptOut::$events;
					foreach ($events as $value => $event) {
						$checked = empty($settings['auto_optout_actions']) || (is_array($settings['auto_optout_actions']) && in_array($value, $settings['auto_optout_actions'])) ? ' checked' : '';
						echo '<label><input type="checkbox" name="auto_optout_actions[]" value="' . $value . '"' . $checked . '> ' . Format::htmlspecialchars($event['title']) . '</label> ';
					}

					?>
			</div>
		</div>

		</div>

		<a id="gapi"></a>

		<h3><?= __('Google Maps API Key'); ?></h3>
		<p class="text--mute"><?= __('Insert API key here in order to continue to use Google\'s mapping features once you\'re live'); ?>.</p>


		<ul class="text--mute">
			<li><i><?= __('Step One'); ?>:</i> <?= __('Go to the %s.', '<a target="_blank" href="https://console.developers.google.com/flows/enableapi?apiid=maps_backend,geocoding_backend,directions_backend,distance_matrix_backend,elevation_backend&keyType=CLIENT_SIDE&reusekey=true">Google API Console</a>'); ?></li>
			<li><i><?= __('Step Two'); ?>:</i> <?= __('Create or select a project.'); ?></li>
			<li><i><?= __('Step Three'); ?>:</i> <?= __('Click %s to enable the API and any related services.', '<strong>' . __('Continue') . '</strong>'); ?></i></li>
			<li><i><?= __('Step Four'); ?>:</i> <?= __('On the %s page, get a %s (and set the API Credentials).', '<strong>' . __('Credentials') . '</strong>', '<strong>' . __('Browser key') . '</strong>'); ?>
				<br/><?= __('Note: If you have an existing %s, you may use that key.', '<strong>' . __('Browser key') . '</strong>'); ?> </li>
			<li><i><?= __('Step Five'); ?>:</i> <?= __('Click %s to enable the API and any related services.', '<strong>' . __('Continue') . '</strong>'); ?></i></li>
			<li><i><?= __('Step Six'); ?>:</i> <?= __('Copy and paste the browser key in to the field below.'); ?></li>
		</ul>

		<div class="field">
			<label class="field__label"><?= __('API Key'); ?></label>
			<input class="w1/1" name="settings[google.maps.api_key]" value="<?=Format::htmlspecialchars($googleMapsApiKey); ?>">
		</div>

		<h3><?= __('Lead Score Settings'); ?></h3>
		<?php $count = 0; ?>
		<?php foreach ($scores as $key => $score) { ?>
		<div class="field">
			<label class="field__label">
				<?=$score['title']; ?>
			</label>
			<div>
				<?php for ($i = 0; $i <= 10; $i++) { ?>
				<input id="scoring_<?=$key; ?>_<?=$i; ?>" type="radio" name="scoring[<?=$key; ?>]" value="<?=$i; ?>"<?=($i == $score['value']) ? ' checked' : ''; ?>>
				<label class="toggle" for="scoring_<?=$key; ?>_<?=$i; ?>">
					<span class="toggle__label"><?=$i; ?></span>
				</label>
				<?php } ?>
			</div>
		</div>
		<?php } ?>
		<div class="field">
			<label class="field__label">
				<?= __('Rental Prices?'); ?>
			</label>
			<label class="toggle" for="scoring_rental_yes">
			    <input id="scoring_rental_yes" type="radio" name="scoring[rental]" value="yes" <?=$settings['scoring']['rental'] == 'yes' ? ' checked' : ''; ?>>
                <span class="toggle__label"><?= __('Yes'); ?></span>
            </label>
			<label for="scoring_rental_no">
			    <input id="scoring_rental_no" type="radio" name="scoring[rental]" value="no" <?=$settings['scoring']['rental'] == 'no' || empty($settings['scoring']['rental']) ? ' checked' : ''; ?>>
			    <span class="toggle__label"><?= __('No'); ?></span>
			</label>
		</div>
		<div class="field field--avg-price">
			<div>
				<?php
				$_REQUEST['scoring[min_price]'] = $settings['scoring']['min_price'];
				$_REQUEST['scoring[max_price]'] = $settings['scoring']['max_price'];
				$_REQUEST['scoring[min_rent]'] = $settings['scoring']['min_rent'];
				$_REQUEST['scoring[max_rent]'] = $settings['scoring']['max_rent'];
				?>
                <label class="field__label">
                    <?=__('Target Average Price'); ?>
                </label>
				<?=IDX_Panel::get('Price', array (
						'title'         => __('Target Average Price'),
						'inputMinPrice' => 'scoring[min_price]',
						'inputMaxPrice' => 'scoring[max_price]',
						'inputMinRent'  => 'scoring[min_rent]',
						'inputMaxRent'  => 'scoring[max_rent]'
					)
				)->getMarkup(); ?>
			</div>
		</div>
		<?php if (!empty(Settings::getInstance()->MODULES['REW_MAIL_MANDRILL']) || !empty(Settings::getInstance()->MODULES['REW_MAIL_SENDGRID'])) { ?>
		<div class="field">
			<h3><?= __('Mail Provider Settings'); ?></h3>
			<div class="field">
				<div>
					<label class="toggle" for="mail_provider_none">
					    <input type="radio" name="mail_provider" id="mail_provider_none" value=""<?=empty($settings['mail_settings']['provider']) ? ' checked' : ''; ?>>
					    <span class="toggle__label"><?= __('Default (REW)'); ?></span>
					</label>
					<?php if (!empty(Settings::getInstance()->MODULES['REW_MAIL_MANDRILL'])) { ?>

					<label class="toggle" for="mail_provider_mandrill">
					    <input type="radio" name="mail_provider" id="mail_provider_mandrill" value="mandrill"<?=($settings['mail_settings']['provider'] === 'mandrill') ? ' checked' : ''; ?>>
					    <span class="toggle__label"><?= __('Mandrill'); ?></span>
					</label>
					<?php } ?>
					<?php if (!empty(Settings::getInstance()->MODULES['REW_MAIL_SENDGRID'])) { ?>

					<label class="toggle" for="mail_provider_sendgrid">
					    <input type="radio" name="mail_provider" id="mail_provider_sendgrid" value="sendgrid"<?=($settings['mail_settings']['provider'] === 'sendgrid') ? ' checked' : ''; ?>>
					    <span class="toggle__label"><?= __('SendGrid'); ?></span>
					</label>
					<?php } ?>
				</div>
				<p class="text--mute"><?= __('This feature allows you to use a third-party service to send all outgoing e-mail. This includes all IDX (front-end) and Lead Manager (backend) e-mails sent by the system.'); ?></p>
			</div>


			<div id="mail-mandrill-settings"<?=($settings['mail_settings']['provider'] === 'mandrill') ? '' : ' class="hidden"'; ?>>
				<div class="field">
					<label class="field__label"><?= __('SMTP Username'); ?></label>
					<input class="w1/1" type="email" value="<?=Format::htmlspecialchars($settings['mail_settings']['mandrill']['username']);?>" name="mandrill_username" placeholder="Mandrill Email Address">
					<label class="hint"><?= __('You can find this in your Mandrill account under SMTP &amp; API Credentials'); ?></label>
				</div>
				<div class="field">
					<label class="field__label"><?= __('SMTP Password'); ?></label>
					<input class="w1/1" type="text" value="<?=Format::htmlspecialchars($settings['mail_settings']['mandrill']['password']);?>" name="mandrill_password" placeholder="Any valid Mandrill API Key">
					<label class="hint"><?= __('You can find this in your Mandrill account under SMTP &amp; API Credentials'); ?></label>
				</div>
			</div>
			<div id="mail-sendgrid-settings"<?=($settings['mail_settings']['provider'] === 'sendgrid') ? '' : ' class="hidden"'; ?>>
				<div class="field">
					<label class="field__label"><?= __('SendGrid Username'); ?></label>
					<input class="w1/1" type="text" value="<?=Format::htmlspecialchars($settings['mail_settings']['sendgrid']['username']);?>" name="sendgrid_username" placeholder="Username">
					<label class="hint"><?= __('This is the username you use to log into SendGrid'); ?></label>
				</div>
				<div class="field">
					<label class="field__label"><?= __('SendGrid Password'); ?></label>
					<input class="w1/1" type="password" value="<?=Format::htmlspecialchars($settings['mail_settings']['sendgrid']['password']);?>" name="sendgrid_password" placeholder="Password" autocomplete="new-password">
					<label class="hint"><?= __('This is the password you use to log into SendGrid'); ?></label>
				</div>
			</div>
		</div>
		<?php } ?>

		<h3><?= __('Calendar Notifications'); ?></h3>
		<div class="field">
			<div>
				<label class="toggle" for="calendar_notifications_true">
				    <input type="radio" name="calendar_notifications" id="calendar_notifications_true" value="true"<?=($settings['calendar_notifications'] == 'true') ? ' checked' : ''; ?>>
				    <span class="toggle__label"><?= __('On'); ?></span>
				</label>
				<label class="toggle" for="calendar_notifications_false">
				    <input type="radio" name="calendar_notifications" id="calendar_notifications_false" value="false"<?=($settings['calendar_notifications'] != 'true') ? ' checked' : ''; ?>>
				    <span class="toggle__label"><?= __('Off'); ?></span>
				</label>
			</div>

			<p class="text--mute"><?= __('This toggle allows you to enable/disable email notifications for calendar and lead reminders.'); ?></p>

		</div>
        <?php if (Settings::getInstance()->SKIN === 'elite') { ?>
            <div class="field">
                <label class="field__label"><?= __('Search Area Label'); ?></label>
                <input class="w1/1" name="settings[search_area_label]" value="<?= Format::htmlspecialchars($settings['search_area_label']); ?>">
            </div>
        <?php } ?>
	</div>
	<div class="btns btns--stickyB">
	<span class="R">
		<button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?></button>
		</span>
	</div>
</form>


</div>