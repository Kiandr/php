<div class="brew-dashboard">
	<div class="tabset" id="dashboard_tabs">
		<ul class="clearfix">
			<?php

				// Display dashboard tabs
				foreach ($dashboard_views as $dashboard_link => $dashboard_view) {
					echo '<li' . ($current_view['link'] == $dashboard_link ? ' class="current"' : '') . '>';
					echo '<a href="' . $dashboard_view['url'] . '">';
					echo Format::htmlspecialchars($dashboard_view['title']);
					if (isset($dashboard_view['count'])) {
						echo ' <small class="label">' . Format::number($dashboard_view['count']) . '</small>';
					}
					echo '</a>';
					echo '</li>';

				}

			?>
		</ul>
		<ul class="clearfix">
			<li><a href="/idx/logout.html" target="_parent">Sign Out</a></li>
		</ul>
	</div>
	<div id="dashboard" class="pane">
		<?php

			// Success notifications
			if (!empty($success)) {
				echo '<div class="msg positive">';
				echo '<p>' . implode('</p><p>', $success) . '</p>';
				echo '</div>';
			}

			// Error notifications
			if (!empty($errors)) {
				echo '<div class="msg negative">';
				echo '<p>' . implode('</p><p>', $errors) . '</p>';
				echo '</div>';
			}

			// IDX feed switcher
			if (in_array($current_view['link'], array('searches', 'listings'))) {
				$feed_switcher = '';
				$selected_feed = '';
				if (!empty(Settings::getInstance()->IDX_FEEDS)) {
					$feed_switcher .= '<select onchange="window.location.href = this.value;" class="pull-right">';
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

				// Display feed switcher
				echo $feed_switcher;

				// Display listing filters
				echo '<div class="tabset"><ul class="clearfix">';
				foreach ($listing_filters as $listing_filter) {
					$selected = $current_filter['link'] == $listing_filter['link'] ? ' class="current"' : '';
					echo '<li' . $selected . '>';
					echo '<a href="' . $listing_filter['url'] . '">';
					echo Format::htmlspecialchars($listing_filter['title']);
					if (isset($listing_filter['count'])) {
						echo ' (' . Format::number($listing_filter['count']) . ')';
					}
					echo '</a>';
					echo '</li>';
				}
				echo '</ul></div>';

				// Display listings
				if (!empty($listings)) {
					$result_tpl = $page->locateTemplate('idx', 'misc', 'result');
					// All these crazy class names are needed due to the various skins in the framework...
					$classList = array('articleset colset listings flowgrid gridded colset-1-sm colset-2-md colset-3-lg colset-3-xl');
					if (Settings::getInstance()->SKIN === 'lec-2013') $classList[] = 'colset_3';
					echo '<div class="' . implode(' ', $classList) . '">';
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

			/**
			 * Viewing search activitiy
			 */
			} elseif ($current_view['link'] === 'searches') {

		?>
		<section class="section section-searches">
			<?php

				// Display feed switcher
				echo $feed_switcher;

				// Display search filters
				echo '<div class="tabset"><ul class="clearfix">';
				foreach ($search_filters as $search_filter) {
					$selected = $current_filter['link'] == $search_filter['link'] ? ' class="current"' : '';
					echo '<li' . $selected . '>';
					echo '<a href="' . $search_filter['url'] . '">';
					echo Format::htmlspecialchars($search_filter['title']);
					if (isset($search_filter['count'])) {
						echo ' (' . Format::number($search_filter['count']) . ')';
					}
					echo '</a>';
					echo '</li>';
				}
				echo '</ul></div>';

				// Display searches
				if (!empty($searches)) {

			?>
				<table>
					<thead>
						<tr>
							<?php if ($current_filter['link'] === 'saved') { ?>
								<th>Search Title</th>
								<th>Update</th>
								<th>Sent</th>
								<th>Saved</th>
								<th width="1">&nbsp;</th>
							<?php } else if ($current_filter['link'] === 'suggested') { ?>
								<th>Search Title</th>
								<th>Suggested</th>
								<th width="1">&nbsp;</th>
							<?php } else if ($current_filter['link'] === 'viewed') { ?>
								<th>Search Title</th>
								<th>Last Viewed</th>
								<th width="1">&nbsp;</th>
							<?php } ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($searches as $search) { ?>
							<tr>
								<td>
									<a href="<?=$search['url']; ?>" target="_parent">
										<?=Format::htmlspecialchars($search['title']); ?>
									</a>
								</td>
								<?php if ($current_filter['link'] === 'saved') { ?>
									<td><?=ucwords($search['frequency']); ?></td>
									<td nowrap>
										<?php if (!empty($search['timestamp_sent'])) { ?>
											<time title="<?=date('l, F jS Y \@ g:ia', $search['timestamp_sent']); ?>">
												<?=Format::dateRelative($search['timestamp_sent']); ?>
											</time>
										<?php } else { ?>
											<em>&ndash;</em>
										<?php } ?>
									</td>
								<?php } ?>
								<td nowrap>
									<time title="<?=date('l, F jS Y \@ g:ia', $search['timestamp']); ?>">
										<?=Format::dateRelative($search['timestamp']); ?>
									</time>
								</td>
								<td class="btnset mini">
									<form method="post" onsubmit="return confirm('Are you sure you want to remove this search?');">
										<input type="hidden" name="delete" value="<?=$search['id']; ?>">
										<button type="submit" class="btn small negative">&times;</button>
									</form>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php

				} else {

					// No searches found for current filter
					echo '<div class="msg text-center none"><p>';
					echo 'You currently have no ' . Format::htmlspecialchars($current_filter['link']) . ' seaches';
					if (!empty($selected_feed)) echo ' in ' . Format::htmlspecialchars($selected_feed);
					echo '.</p></div>';

				}

			?>
		</section>
		<?php

			/**
			 * Viewing preferences
			 */
			} elseif ($current_view['link'] === 'preferences') {

		?>
		<section class="section-preferences">

			<form id="form-preferences" method="post"
				<?=$current_form !== 'preferences' ? ' class="hidden"' : ''; ?>"
			>
				<input type="hidden" name="form" value="preferences">
				<div class="x6 o0">
					<section class="section section-preferences">
						<fieldset>
							<h4>Contact Information</h4>
							<div class="field x6">
								<label>First Name</label>
								<input name="first_name" value="<?=Format::htmlspecialchars($preferences['first_name']); ?>">
							</div>
							<div class="field x6 last">
								<label>Last Name</label>
								<input name="last_name" value="<?=Format::htmlspecialchars($preferences['last_name']); ?>">
							</div>
							<div class="field x12">
								<label>Email Address <small class="required">*</small></label>
								<input type="email" name="email" value="<?=Format::htmlspecialchars($preferences['email']); ?>" required>
							</div>
							<div class="field x12">
								<label class="field__label">Alternate Email Address</label>
								<input type="email" name="email_alt" value="<?=Format::htmlspecialchars($preferences['email_alt']); ?>">
								<label class="toggle toggle--stacked">
									<input type="checkbox" name="email_alt_cc_searches" value="saved_searches"<?=($preferences['email_alt_cc_searches'] === 'true' ? ' checked' : ''); ?>>
									<span class="toggle__label">Send CC of saved search updates to this email address.</span>
								</label>
							</div>
						</fieldset>
					</section>
					<section class="section section-fieldset">
						<fieldset>
							<h4>Phone Numbers</h4>
							<div class="field x6">
								<label>Primary Phone<?php if (!empty(Settings::getInstance()->SETTINGS['registration_phone'])) echo '<small class="required">*</small>'; ?></label>
								<input type="tel" name="phone" value="<?=$preferences['phone']; ?>">
							</div>
							<div class="field x6 last">
								<label>Secondary Phone</label>
								<input type="tel" name="phone_cell" value="<?=Format::htmlspecialchars($preferences['phone_cell']); ?>">
							</div>
							<div class="field x6">
								<label>Work Phone</label>
								<input type="tel" name="phone_work" value="<?=Format::htmlspecialchars($preferences['phone_work']); ?>">
							</div>
							<div class="field x6 last">
								<label>Fax Number</label>
								<input type="tel" name="phone_fax" value="<?=Format::htmlspecialchars($preferences['phone_fax']); ?>">
							</div>
						</fieldset>
					</section>
				</div>
				<div class="x6 o6">
					<section class="section section-fieldset">
						<fieldset>
							<h4>Mailing Address</h4>
							<div class="field x12">
								<label>Street Address</label>
								<input name="address1" value="<?=Format::htmlspecialchars($preferences['address1']); ?>">
							</div>
							<div class="field x6">
								<label>City</label>
								<input name="city" value="<?=Format::htmlspecialchars($preferences['city']); ?>">
							</div>
							<div class="field x6 last">
								<label><?=Locale::spell('State'); ?></label>
								<input name="state" value="<?=Format::htmlspecialchars($preferences['state']); ?>">
							</div>
							<div class="field x6">
								<label><?=Locale::spell('Zip Code'); ?></label>
								<input name="zip" value="<?=Format::htmlspecialchars($preferences['zip']); ?>">
							</div>
							<div class="field x6 last">
								<label>Country</label>
								<input name="country" value="<?=Format::htmlspecialchars($preferences['country']); ?>">
							</div>
						</fieldset>
					</section>
					<section class="section section-fieldset">
						<fieldset>
							<h4>Subscription Settings</h4>
							<div class="field x12 toggleset">
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
						</fieldset>
					</section>
				</div>
				<div class="btnset">
					<button type="submit" class="strong">Update Preferences</button>
					<?php if (!empty(Settings::getInstance()->SETTINGS['registration_password'])) { ?>
						<button type="button" class="btn show-password">Change My Password</button>
					<?php } ?>
				</div>
			</form>

			<?php if (!empty(Settings::getInstance()->SETTINGS['registration_password'])) { ?>
				<form id="form-password" method="post"
					<?=$current_form !== 'password' ? ' class="hidden"' : ''; ?>"
				>
					<div class="x6 o0">
						<input type="hidden" name="form" value="password">
						<section class="section section-fieldset">
							<fieldset>
								<h4>Change My Password</h4>
								<?php if (!empty($preferences['password'])) { ?>
									<div class="field">
										<label>Your Current Password <small class="required">*</small></label>
										<input type="password" name="current_password" value="" required>
									</div>
								<?php } ?>
								<div class="field">
									<label>Your New Password <small class="required">*</small></label>
									<input type="password" name="new_password" value="" required>
								</div>
								<div class="field">
									<label>Repeat New Password <small class="required">*</small></label>
									<input type="password" name="confirm_password" value="" required>
								</div>
							</fieldset>
						</section>
						<div class="btnset">
							<button type="submit" class="strong">Update Password</button>
							<button type="button" class="btn show-preferences">Back to Preferences</button>
						</div>
					</div>
				</form>
			<?php } ?>

		</section>

		<?php

			/**
			 * Viewing messages
			 */
			} elseif ($current_view['link'] === 'messages') {

		?>
		<section id="section-messages" class="section<?=$_GET['form'] === 'compose' ? ' hidden' : ''; ?>">
			<header>
				<a class="btn pull-right show-compose">Compose Message</a>
				<h4>My Messages</h4>
			</header>
			<?php if (!empty($threads)) { ?>
				<table>
					<thead>
						<tr>
							<th>Message Subject</th>
							<th>Agent Name</th>
							<th>Replies</th>
							<th>Last Message</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($threads as $thread) { ?>
							<tr valign="top"<?=!empty($thread['unread']) ? ' class="unread"' : ''; ?>>
								<td>
									<?php if (!empty($thread['unread'])) { ?>
										<span class="new">New</span>
									<?php } ?>
									<a href="<?=$thread['url']; ?>">
										<?=Format::htmlspecialchars($thread['subject']); ?>
									</a>
								</td>
								<td>
									<a href="<?=$thread['url']; ?>">
										<?=Format::number($thread['agent']); ?>
									</a>
								</td>
								<td>
									<a href="<?=$thread['url']; ?>">
										<?=Format::number($thread['replies']); ?>
									</a>
								</td>
								<td nowrap>
									<time title="<?=date('l, F jS Y \@ g:ia', $thread['timestamp']); ?>">
										<?=Format::dateRelative($thread['timestamp']); ?>
									</time>
								</td>
								<td class="btnset mini">
									<form method="post" onsubmit="return confirm('Are you sure you want to delete this message?');">
										<input type="hidden" name="delete" value="<?=$thread['id']; ?>">
										<button type="submit" class="btn small negative">&times;</button>
									</form>
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			<?php } else { ?>
				<div class="msg text-center none">
					<p>You currently have no messages.</p>
				</div>
			<?php } ?>
		</section>
		<form id="form-compose" method="post" class="x12 o0<?=$_GET['form'] === 'compose' ? '' : ' hidden'; ?>">
			<section class="section section-fieldset">
				<input type="hidden" name="form" value="compose">
				<fieldset>
					<a class="btn pull-right show-messages">Back to Messages</a>
					<h4>Compose New Message</h4>
					<div class="field">
						<label>Subject <small class="required">*</small></label>
						<input name="subject" value="" required>
					</div>
					<div class="field">
						<label>Message <small class="required">*</small></label>
						<textarea name="message" rows="6" required></textarea>
					</div>
				</fieldset>
				<div class="btnset">
					<button type="submit" class="strong">Send Message</button>
				</div>
			</section>
		</form>
		<?php

			/**
			 * Viewing message thread
			 */
			} elseif ($current_view['link'] === 'message') {

		?>
		<section class="section-message">
			<header>
				<a href="?view=messages" class="btn pull-right">Back to Messages</a>
				<h4><?=Format::htmlspecialchars($thread['subject']); ?></h4>
			</header>
			<div class="thread">
				<?=$thread['sent_from'] == 'agent' ? Format::htmlspecialchars($thread['agent']) : 'You'; ?> sent message:
				<div class="msg"><?=$thread['message']; ?></div>
				<time title="<?=date('l, F jS Y \@ g:ia', $thread['timestamp']); ?>">
					<?=Format::dateRelative($thread['timestamp']); ?>
				</time>
			</div>
			<?php if (!empty($replies)) { ?>
				<div class="replies">
					<?php foreach ($replies as $reply) { ?>
						<div class="reply">
							<span class="sender">
								<?=$reply['sent_from'] == 'agent' ? Format::htmlspecialchars($reply['agent']) : 'You'; ?> replied:
								<time title="<?=date('l, F jS Y \@ g:ia', $reply['timestamp']); ?>">
									<?=Format::dateRelative($reply['timestamp']); ?>
								</time>
							</span>
							<div class="msg">
								<p><?=$reply['message']; ?></p>
							</div>
							<?php if ($reply['sent_from'] == 'lead') { ?>
								<form method="post" onsubmit="return confirm('Are you sure you want to delete this reply?');">
									<input type="hidden" name="delete" value="<?=$reply['id']; ?>">
									<button type="submit" class="btn small negative">&times;</button>
								</form>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
			<?php } ?>
			<form id="form-reply" method="post" class="x12 o0">
				<input type="hidden" name="form" value="reply">
				<section class="section section-fieldset">
					<fieldset>
						<h4>Reply to this message&hellip;</h4>
						<div class="field">
							<textarea name="message" rows="6" required></textarea>
						</div>
					</fieldset>
					<div class="btnset">
						<button type="submit" class="strong">Send Reply</button>
					</div>
				</section>
			</form>
		</section>
		<?php

			}

		?>
	</div>
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

})();
/* </script> */
<?php

// Write Javascript
$page->writeJS(ob_get_clean());