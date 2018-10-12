<div class="block">
    <form action="?submit" method="post" class="rew_check">
    	<h2>Send Text Message to Leads</h2>
    	<div class="btns btns--stickyB"><span class="R">
    		<button class="btn btn--positive" type="submit">Send</button>
    		</span> </div>
    	<div class="field">
    		<h2>Send To Leads <em class="required">*</em></h2>
    		<?php

    						// Display leads that have opted in
    						echo '<div id="to-number" class="gridded">';
    						if (!empty($optin_leads) && is_array($optin_leads)) {
    							foreach ($optin_leads as $lead) {
    								$phone_number = Format::htmlspecialchars($lead['phone_number']);
    								$leadname = Format::trim(Format::htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']));
    								if (empty($leadname)) {
    									$leadname = '<em class="no-name" title="' . Format::htmlspecialchars($lead['email']) . '">' . Format::truncate(Format::htmlspecialchars($lead['email']), 25) . '</em>';
    								}
    								echo '<div class="boxed">';
    								echo '<input type="hidden" name="leads[]" value=' . $lead['id'] . '">';
    								echo '<label><input type="checkbox" name="to[]" value="' . $phone_number . '" checked>' . PHP_EOL;
    								echo $leadname . '<br><small>' . Format::phone($lead['phone_number']) . '</small>';
    								echo '</label><br>';
    								if (!empty($lead['optout'])) {
    									echo PHP_EOL . '<div class="flag flag--negative" title="' . Format::dateRelative($lead['optout']) . '"><span class="ico"></span> Opt-Out &ndash; Cannot Text</div>';
    								} else if (!empty($lead['verified'])) {
    									echo PHP_EOL . '<div class="flag flag--positive" title="' . Format::dateRelative($lead['verified']) . '"><span class="ico"></span> Verified</div>';
    								} else {
    									echo PHP_EOL . '<div class="flag flag--negative"><span class="ico"></span> Unverified</div>';
    								}
    								// Checked the box to opt-in
    								if ($lead['opt_texts'] === 'in' && empty($lead['optout'])) {
    									echo PHP_EOL . '<div class="flag flag--positive"><span class="ico"></span> Opt-In</div>';
    								}
    								echo '</div>';
    							}
    						} else {
    							echo '<div class="no-number">';
    							echo 'No leads available to text.';
    							echo '</div>';
    						}
    						echo '<div class="clear"></div>';
    						echo '</div>';


    						// Display leads that have opted out
    						if (!empty($optout_leads) && is_array($optout_leads)) {
    							echo '<br><div class="notify notify-error" style="line-height: 2;">';
    							echo '<p>' . Format::number(count($optout_leads)) . ' of the selected leads cannot be sent indirect text messages because they have not opted in. <a id="view-optout-leads">Show Leads</a></p>';
    							echo '</div>';
    							echo '<div id="optout-leads" class="gridded boxed hidden">';
    							foreach ($optout_leads as $lead) {
    								$phone_number = Format::htmlspecialchars($lead['phone_number']);
    								$leadname = Format::trim(Format::htmlspecialchars($lead['first_name'] . ' ' . $lead['last_name']));
    								if (empty($leadname)) {
    									$leadname = '<em class="no-name" title="' . Format::htmlspecialchars($lead['email']) . '">' . Format::truncate(Format::htmlspecialchars($lead['email']), 25) . '</em>';
    								}
    								echo '<div class="boxed">';
    								echo '<label>' . $leadname . '</label>';
    								if (!empty($phone_number)) {
    									echo '<br><small>' . Format::phone($lead['phone_number']) . '</small>';
    								} else {
    									echo '<br><small>(No number available)</small>';
    								}
    								echo '<br>';
    								if (!empty($lead['optout'])) {
    									echo PHP_EOL . '<div class="flag flag--negative" title="' . Format::dateRelative($lead['optout']) . '"><span class="ico"></span> Opt-Out &ndash; Cannot Text</div>';
    								} else if (!empty($lead['verified'])) {
    									echo PHP_EOL . '<div class="flag flag--positive" title="' . Format::dateRelative($lead['verified']) . '"><span class="ico"></span> Verified</div>';
    								} else {
    									echo PHP_EOL . '<div class="flag flag--negative"><span class="ico"></span> Unverified</div>';
    								}
    								// Checked the box to opt-in
    								if ($lead['opt_texts'] === 'out' && empty($lead['optout'])) {
    									echo PHP_EOL . '<div class="flag flag--negative"><span class="ico"></span> Opt-Out</div>';
    								}
    								echo '</div>';
    							}
    							echo '<div class="clear"></div>';
    							echo '</div>';
    						}

    					?>
    	</div>
    	<div class="field">
    		<h3>Your Text Message <em class="required">*</em></h3>
    		<textarea class="w1/1" name="body" rows="4" maxlength="<?=$maxlength; ?>"<?=(!$media ? ' required' : ''); ?>><?=Format::htmlspecialchars($_POST['body']); ?>
    </textarea>
    		<!-- <label class="hint"><em class="charsRemaining">&nbsp;</em></label> -->
    		<div class="hint marB marT"><strong>Available Tags:</strong> {first_name}, {last_name}</div>
    	</div>
    	<div class="field">
    		<div id="attach-preview" style="clear: both;"></div>
    		<button id="attach-media" type="button" class="btn<?=($media ? ' hidden' : ''); ?> btn--positive">Attach an Image</button>
    		<?php

    						// Media attachment
    						if (!empty($media)) {
    							echo '<div class="attached-media">';
    							echo '<input type="hidden" name="media" value="' . $media . '">';
    							echo '<img src="' . $media . '" alt="">';
    							echo '<em>(click image to remove)</em>';
    							echo '</div>';
    						}

    					?>
    	</div>
    </form>
</div>
