    <div class="pane dashboard-menu">
        <ul class="navbar">
            <?php
                // Display dashboard tabs
                foreach ($dashboard_views as $dashboard_link => $dashboard_view) {
                    echo '<li>';
                    echo '<a class="act' . ($current_view['link'] == $dashboard_link ? ' cur' : '') . '" href="' . $dashboard_view['url'] . '">';
                    echo Format::htmlspecialchars(str_replace("My","", $dashboard_view['title']));
                    if (isset($dashboard_view['count'])) {
                        echo ' <small class="label">' . Format::number($dashboard_view['count']) . '</small>';
                    }
                    echo '</a>';
                    echo '</li>';
                }
            ?>
            <li class="R sign-out-link"><a class="act" href="/idx/logout.html" target="_parent">Sign Out</a></li>
        </ul>
    </div>
	<div id="dashboard" class="pane">
	    <div class="user-messages">
            <?php

                // Success notifications
                if (!empty($success)) {
                    echo '<div class="msg msg--pos">';
                    echo '<div>' . implode('</div><div>', $success) . '</div>';
                    echo '</div>';
                }

                // Error notifications
                if (!empty($errors)) {
                    echo '<div class="msg msg--neg">';
                    echo '<div>' . implode('</div><div>', $errors) . '</div>';
                    echo '</div>';
                }

            ?>
	    </div>
		<?php
			// IDX feed switcher
			if (in_array($current_view['link'], array('searches', 'listings'))) {
				$clear = '';
				$selected_feed = '';
				if (!empty(Settings::getInstance()->IDX_FEEDS)) {
					$feed_switcher .= '<select onchange="window.location.href = this.value;">';
					foreach (Settings::getInstance()->IDX_FEEDS as $link => $feed) {
						$feed_url = '?' . http_build_query(array_merge($query_params, array(
							'filter' => $current_filter['link'],
							'feed' => $link
						)));
						$selected = (Settings::getInstance()->IDX_FEED === $link ? 'selected' : '');
						if (!empty($selected)) $selected_feed = $feed['title'];
						$feed_switcher .= '<option value="' . $feed_url . '"' . $selected . '>' . Format::htmlspecialchars($feed['title']) . '</option>';
					}
					$feed_switcher .= '</select>';
				}
			}
			/**
			 * Display listing activity
			 */
			if ($current_view['link'] === 'listings') {
		?>
		<section class="section section-listings">
			<?php

				// Display listing filters
				echo '<div class="dashboard-menu"><ul class="navbar">';
				foreach ($listing_filters as $filter) {
					echo sprintf('<li class="menu-item%s">', $current_filter['link'] == $filter['link'] ? ' menu-item--cur' : '');
					echo '<a href="' . $filter['url'] . '">';
					echo Format::htmlspecialchars($filter['title']);
					if (isset($filter['count'])) {
						echo ' (' . Format::number($filter['count']) . ')';
					}
					echo '</a>';
					echo '</li>';
				}
                echo '<li>' . $feed_switcher . '</li>';
				echo '</ul></div>';

				// Display listings
				if (!empty($listings)) {
					$result_tpl = $page->locateTemplate('idx', 'misc', 'result');
					echo '<div class="listings cols">';
					foreach ($listings as $result) {
						include $result_tpl;
					}
					echo '</div>';

					// Pagination links
					if (!empty($pagination)) {
						echo '<div class="pagination">';
						if (!empty($pagination['prev'])) echo '<a class="prev" href="' . $pagination['prev'] . '">&#171;</a>';
						if (!empty($pagination['next'])) echo '<a class="next" href="' . $pagination['next'] . '">&#187;</a>';
						echo '</div>';
					}

				} else {

					// No listings found for current filter
					echo '<div class="msg text-center none"><p>';
					echo 'You currently have no ' . Format::htmlspecialchars($current_filter['link']) . ' listings';
					if (!empty($selected_feed)) echo ' in ' . Format::htmlspecialchars($selected_feed);
					echo '.</p></div>';

				}

			?>
		</section>
		<?php

			/*** Viewing search activitiy ***/
			} elseif ($current_view['link'] === 'searches') {

		?>
		<section class="section section-searches">
			<?php

				// Display search filters
			    echo '<div class="dashboard-menu"><ul class="navbar">';
				foreach ($search_filters as $filter) {
					echo sprintf('<li class="menu-item%s">', $current_filter['link'] == $filter['link'] ? ' menu-item--cur' : '');
					echo '<a href="' . $filter['url'] . '">';
					echo Format::htmlspecialchars($filter['title']);
					if (isset($filter['count'])) {
						echo ' (' . Format::number($filter['count']) . ')';
					}
					echo '</a>';
					echo '</li>';
				}
				echo '<li>' . $feed_switcher . '</li>';
				echo '</ul></div>';

				// Display searches
				if (!empty($searches)) {

			?>
            <div class="dashboard-saved-searches">
            	<?php if ($current_filter['link'] === 'suggested') { ?>
                    <h2>Suggested Searches</h2>
                <?php } else if ($current_filter['link'] === 'viewed') { ?>
                    <h2>Recent Searches</h2>
                <?php } else { ?>
                    <h2>Saved Searches</h2>
                <?php } ?>
            	<?php foreach ($searches as $search) { ?>
            	    <span class="saved-search-title"><a href="<?=$search['url']; ?>" target="_parent"><?=Format::htmlspecialchars($search['title']); ?></a></span>
                    <?php if ($current_filter['link'] === 'saved') { ?>
                    <span class="saved-search-frequency">Frequency: <?=ucwords($search['frequency']); ?></span>
                    <span class="saved-search-last-update">Last Update Sent On -
                        <?php if (!empty($search['timestamp_sent'])) { ?>
                            <time title="<?=date('l, F jS Y \@ g:ia', $search['timestamp_sent']); ?>">
                                <?=Format::dateRelative($search['timestamp_sent']); ?>
                            </time>
                        <?php } else { ?>
                            NA
                        <?php } ?>
                    </span>
                    <?php } ?>
                    <span class="saved-search-save-date">
                        <?php if ($current_filter['link'] === 'suggested') { ?>
                            Suggested On
                        <?php } else if ($current_filter['link'] === 'viewed') { ?>
                            Last Viewed
                        <?php } else { ?>
                            Search Saved On -
                        <?php } ?>
                        <time title="<?=date('l, F jS Y \@ g:ia', $search['timestamp']); ?>">
                            <?=Format::dateRelative($search['timestamp']); ?>
                        </time>
                    </span>
                    <span class="saved-search-delete">
                        <form method="post" onsubmit="return confirm('Are you sure you want to remove this search?');">
                            <input type="hidden" name="delete" value="<?=$search['id']; ?>">
                            <a href="<?=$search['url']; ?>" target="_parent" class="btn small"><i class="ion-ios-search"></i> View Search</a> <button type="submit" class="btn small"><i class="ion-ios-trash-outline"></i> Delete Search</button>
                        </form>                         
                    </span>
            	<?php } ?>
            </div>
			<?php

				} else {

    				// No searches found for current filter
    				echo '<div class="dashboard-saved-searches">';
    				echo '<h2>No Search Found</h2>';
    				echo '<div class="msg text-center none"><p>';
    				echo 'You currently have no ' . Format::htmlspecialchars($current_filter['link']) . ' seaches';
    				if (!empty($selected_feed)) echo ' in ' . Format::htmlspecialchars($selected_feed);
    				echo '.</p></div>';
    				echo '</div>';
    			}
			?>
		</section>
		<?php

			/*** Viewing preferences ***/
			} elseif ($current_view['link'] === 'preferences') {

		?>
		<section class="section-preferences">
			<form id="form-preferences" method="post"<?=$current_form !== 'preferences' ? ' class="hidden"' : ''; ?>">
				<input type="hidden" name="form" value="preferences">
				<div class="flds">
					<h2 class="margin-top">Contact Information</h2>
					<div class="cols">
						<div class="fld col w1/2">
							<label>First Name</label>
							<input name="first_name" value="<?=Format::htmlspecialchars($preferences['first_name']); ?>">
						</div>
						<div class="fld col w1/2">
							<label>Last Name</label>
							<input name="last_name" value="<?=Format::htmlspecialchars($preferences['last_name']); ?>">
						</div>
						<div class="fld col w1/1">
							<label>Email Address <small class="required">*</small></label>
							<input type="email" name="email" value="<?=Format::htmlspecialchars($preferences['email']); ?>" required>
						</div>
						<div class="fld col w1/1">
							<label class="field__label">Alternate Email Address</label>
							<input type="email" name="email_alt" value="<?=Format::htmlspecialchars($preferences['email_alt']); ?>">
							<label class="toggle toggle--stacked">
								<input type="checkbox" name="email_alt_cc_searches" value="saved_searches"<?=($preferences['email_alt_cc_searches'] === 'true' ? ' checked' : ''); ?>>
								<span class="toggle__label">Send CC of saved search updates to this email address.</span>
							</label>
						</div>
					</div>
				</div>
				<hr>
				<div class="flds">
					<h2>Phone Numbers</h2>
					<div class="cols">
						<div class="fld col w1/2">
							<label>Primary Phone<?php if (!empty(Settings::getInstance()->SETTINGS['registration_phone'])) echo '<small class="required">*</small>'; ?></label>
							<input type="tel" name="phone" value="<?=$preferences['phone']; ?>">
						</div>
						<div class="fld col w1/2">
							<label>Secondary Phone</label>
							<input type="tel" name="phone_cell" value="<?=Format::htmlspecialchars($preferences['phone_cell']); ?>">
						</div>
						<div class="fld col w1/2">
							<label>Work Phone</label>
							<input type="tel" name="phone_work" value="<?=Format::htmlspecialchars($preferences['phone_work']); ?>">
						</div>
						<div class="fld col w1/2">
							<label>Fax Number</label>
							<input type="tel" name="phone_fax" value="<?=Format::htmlspecialchars($preferences['phone_fax']); ?>">
						</div>
					</div>
				</div>
				<hr>
				<div class="flds">
					<h2>Mailing Address</h2>
					<div class="cols">
						<div class="fld col w1/1">
							<label>Street Address</label>
							<input name="address1" value="<?=Format::htmlspecialchars($preferences['address1']); ?>">
						</div>
						<div class="fld col w1/2">
							<label>City</label>
							<input name="city" value="<?=Format::htmlspecialchars($preferences['city']); ?>">
						</div>
						<div class="fld col w1/2">
							<label><?=Locale::spell('State'); ?></label>
							<input name="state" value="<?=Format::htmlspecialchars($preferences['state']); ?>">
						</div>
						<div class="fld col w1/2">
							<label><?=Locale::spell('Zip Code'); ?></label>
							<input name="zip" value="<?=Format::htmlspecialchars($preferences['zip']); ?>">
						</div>
						<div class="fld col w1/2">
							<label>Country</label>
							<input name="country" value="<?=Format::htmlspecialchars($preferences['country']); ?>">
						</div>
					</div>
				</div>
				<hr>
				<div class="flds">
					<h2>Subscription Settings</h2>
					<div class="cols">
						<div class="fld col w1/1">
							<label class="toggle">
								<input type="checkbox" name="opt_searches" value="in"<?=($preferences['opt_searches'] != 'out' ? ' checked' : ''); ?>>
								Yes, I would like to receive listing updates matching my saved search criteria.
							</label>
							<label class="toggle">
								<input type="checkbox" name="opt_marketing" value="in"<?=($preferences['opt_marketing'] != 'out' ? ' checked' : ''); ?>>
								<?=$opt_text['opt_marketing'] ?: 'Please send me updates concerning this website and the real estate market.'; ?>
							</label>
							<?php if (Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO']) { ?>
								<label class="toggle">
									<input type="checkbox" name="opt_texts" value="in"<?=($preferences['opt_texts'] == 'in' ? ' checked' : ''); ?>>
									<?=$opt_text['opt_texts'] ?: 'I consent to receiving text messages from this site.'; ?>
								</label>
							<?php } ?>
						</div>
					</div>
				</div>
				<div class="btns cols">
					<button type="submit" class="btn">Update Preferences</button>
					<?php if (!empty(Settings::getInstance()->SETTINGS['registration_password'])) { ?>
						<button type="button" class="btn show-password">Change My Password</button>
					<?php } ?>
				</div>
			</form>
			<?php if (!empty(Settings::getInstance()->SETTINGS['registration_password'])) { ?>
				<form id="form-password" method="post"<?=$current_form !== 'password' ? ' class="hidden"' : ''; ?>">
                    <div class="flds">
                        <h2>Change My Password</h2>
                        <div class="cols">
                            <input type="hidden" name="form" value="password">
                            <?php if (!empty($preferences['password'])) { ?>
                                <div class="fld col w1/1">
                                    <label>Your Current Password <small class="required">*</small></label>
                                    <input type="password" name="current_password" value="" required>
                                </div>
                            <?php } ?>
                            <div class="fld col w1/1">
                                <label>Your New Password <small class="required">*</small></label>
                                <input type="password" name="new_password" value="" required>
                            </div>
                            <div class="fld col w1/1">
                                <label>Repeat New Password <small class="required">*</small></label>
                                <input type="password" name="confirm_password" value="" required>
                            </div>
                        </div>
                    </div>
                    <div class="btns cols">
                        <button type="submit" class="w1/2 strong">Update Password</button>
                        <button type="button" class="w1/2 btn show-preferences">Back to Preferences</button>
                    </div>
				</form>
			<?php } ?>
		</section>

		<?php
			/*** Viewing messages ***/
			} elseif ($current_view['link'] === 'messages') {
		?>

		<section id="section-messages" class="section<?=$_GET['form'] === 'compose' ? ' hidden' : ''; ?>">

			<header>
				<a class="btn pull-right show-compose">Compose Message</a>
				<h2>Messages</h2>
			</header>



			<?php if (!empty($threads)) { ?>
				<div class="dashboard-messages">
    				<?php foreach ($threads as $thread) { ?>
        				<span class="message-title">Message Subject: <a href="<?=$thread['url']; ?>"><?=Format::htmlspecialchars($thread['subject']); ?></a></span>
        				<span>Agent Name: <?=Format::number($thread['agent']); ?></span>
        				<span>Replies: <?=Format::number($thread['replies']); ?></span>
        				<span>Last Message Sent On: <time title="<?=date('l, F jS Y \@ g:ia', $thread['timestamp']); ?>"><?=Format::dateRelative($thread['timestamp']); ?></time></span>
        				<span>
        				<form method="post" onsubmit="return confirm('Are you sure you want to delete this message?');">
                            <input type="hidden" name="delete" value="<?=$thread['id']; ?>">
                            <a href="<?=$thread['url']; ?>" class="btn small"><i class="ion-ios-search"></i> View Message</a> <button type="submit" class="btn small"><i class="ion-ios-trash-outline"></i> Delete Message</button>
                        </form>
                        </span>
    				<?php } ?>
                </div>
            <?php } else { ?>
				<div class="msg text-center none">
					<p>You currently have no messages.</p>
				</div>
			<?php } ?>
		</section>

		<form id="form-compose" method="post" class="x12 o0<?=$_GET['form'] === 'compose' ? '' : ' hidden'; ?>">
			<section class="section section-fldset">
				<input type="hidden" name="form" value="compose">
				<fldset>
					<a class="btn pull-right show-messages">Back to Messages</a>
					<h2>Compose New Message</h2>
					<div class="fld">
						<label>Subject <small class="required">*</small></label>
						<input name="subject" value="" required>
					</div>
					<div class="fld">
						<label>Message <small class="required">*</small></label>
						<textarea name="message" rows="6" required></textarea>
					</div>
				</fldset>
				<div class="btnset">
					<button type="submit" class="strong">Send Message</button>
				</div>
			</section>
		</form>

		<?php
			/*** Viewing message thread ***/
			} elseif ($current_view['link'] === 'message') {
		?>

		<section id="section-message" class="section">

			<header class="cols">
                <div class="col w1/2">
                    <h2 class="message-header"><?=$thread['sent_from'] == 'agent' ? Format::htmlspecialchars($thread['agent']) : 'View'; ?> Message</h2>
                </div>
                <div class="col w1/2">
                    <a href="?view=messages"><button class="btn small pull-right">Back to Messages</button></a>
                </div>
            </header>

			<div class="uk-panel uk-panel-box margin-top-bottom">
                <h3 class="uk=panel-title">Subject: <?=Format::htmlspecialchars($thread['subject']); ?></h3>
                <?=$thread['message']; ?>
                <span class="message-timestamp">
                    <time title="<?=date('l, F jS Y \@ g:ia', $thread['timestamp']); ?>">
                        Sent: <?=Format::dateRelative($thread['timestamp']); ?>
                    </time>
                </span>
            </div>

			<h2>View Replies</h2>

			<?php if (!empty($replies)) { ?>
                <?php foreach ($replies as $reply) { ?>
                    <div class="uk-panel uk-panel-box margin-top-bottom">
                        <?=$reply['message']; ?>
                        <span class="message-timestamp">
                            <?=$reply['sent_from'] == 'agent' ? Format::htmlspecialchars($reply['agent']) : 'You'; ?> replied:
                            <time title="<?=date('l, F jS Y \@ g:ia', $reply['timestamp']); ?>">
                                <?=Format::dateRelative($reply['timestamp']); ?>
                            </time>
                        </span>
                        <?php if ($reply['sent_from'] == 'lead') { ?>
                                <form method="post" onsubmit="return confirm('Are you sure you want to delete this reply?');">
                                    <input type="hidden" name="delete" value="<?=$reply['id']; ?>">
                                    <button type="submit" class="btn small negative"><i class="ion-ios-trash-outline"></i> Delete Reply</button>
                                </form>
                            <?php } ?>
                    </div>
                <?php } ?>
            <?php } ?>

			<form id="form-reply" method="post" class="x12 o0">
				<input type="hidden" name="form" value="reply">
				<section class="section section-fldset">
					<fldset>
						<h2>Reply to this message&hellip;</h2>
						<div class="fld">
							<textarea name="message" rows="6" required></textarea>
						</div>
					</fldset>
					<div class="btnset">
						<button type="submit" class="btn">Send Reply</button>
					</div>
				</section>
			</form>
		</section>
		<?php

			}

		?>
	</div>

<?php

ob_start();

?>
/* <script> */
(function () {

	var $dashboard = $('#dashboard')
		, $password = $('#form-password')
		, $preferences = $('#form-preferences')
		, $messages = $('#section-messages')
		, $compose = $('#form-compose')
	;

	// Show password form
	$dashboard.on('click', '.show-password', function () {
		$password.removeClass('hidden');
		$preferences.addClass('hidden');
	});

	// Show preferences form
	$dashboard.on('click', '.show-preferences', function () {
		$preferences.removeClass('hidden');
		$password.addClass('hidden');
	});

	// Show new message form
	$dashboard.on('click', '.show-compose', function () {
		$compose.removeClass('hidden');
		$messages.addClass('hidden');
	});

	// Show list of messages
	$dashboard.on('click', '.show-messages', function () {
		$messages.removeClass('hidden');
		$compose.addClass('hidden');
	});

	//Redirect iDevices to non-iframe version of this page
	if(window.location.search.match(/popup/) != null) {
		if(navigator.userAgent.match(/iP(hone|ad|od)/i)) {
			window.top.location = "/idx/dashboard.html";
		}
	}


})();
/* </script> */
<?php

// Write Javascript
$page->writeJS(ob_get_clean());