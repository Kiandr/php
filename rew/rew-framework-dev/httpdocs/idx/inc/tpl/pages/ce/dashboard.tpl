<?php

/**
 * @var \REW\Core\Interfaces\PageInterface $page
 * @var \REW\Core\Interfaces\User\SessionInterface $user
 */
$skinUrl = $page->getSkin()->getUrl();
$assetPath = sprintf('%s/img', $skinUrl);

?>
<div class="dashboard">
    <ul class="dashboard__menu">
        <?php

            // Display dashboard tabs
            foreach ($dashboard_views as $dashboard_link => $dashboard_view) {
                echo '<li class="dashboard__list">';
                echo '<a class="act' . ($current_view['link'] == $dashboard_link ? ' current' : '') . '" href="' . $dashboard_view['url'] . '">';
                echo Format::htmlspecialchars(str_replace("My","", $dashboard_view['title']));
                if (isset($dashboard_view['count'])) {
                    echo ' <small class="label">' . Format::number($dashboard_view['count']) . '</small>';
                }
                echo '</a>';
                echo '</li>';
            }

        ?>
    </ul>
    <div id="dashboard" class="pane">
        <div class="user-messages">
            <?php

                // Success notifications
                if (!empty($success)) {
                    echo '<div class="notice notice--positive -mar-bottom">';
                    echo '<div class="notice__message">' . implode('</div><div>', $success) . '</div>';
                    echo '</div>';
                }

                // Error notifications
                if (!empty($errors)) {
                    echo '<div class="notice notice--negative -mar-bottom">';
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
                    $feed_switcher .= '<div class="-mar-bottom"><select onchange="window.location.href = this.value;">';
                    foreach (Settings::getInstance()->IDX_FEEDS as $link => $feed) {
                        $feed_url = '?' . http_build_query(array_merge($query_params, array(
                            'filter' => $current_filter['link'],
                            'feed' => $link
                        )));
                        $selected = (Settings::getInstance()->IDX_FEED === $link ? 'selected' : '');
                        if (!empty($selected)) $selected_feed = $feed['title'];
                        $feed_switcher .= '<option value="' . $feed_url . '"' . $selected . '>' . Format::htmlspecialchars($feed['title']) . '</option>';
                    }
                    $feed_switcher .= '</select></div>';
                }
            }

            /**
             * Display listing activity
             */
            if ($current_view['link'] === 'listings') {

        ?>
        <section class="dashboard__listings">
            <?php

                // Display listing filters
                echo '<ul class="dashboard__filters">';
                echo '<li class="filters__list">';
                echo sprintf(
                    '<a>%s%s</a>',
                    htmlspecialchars($current_filter['title']),
                    (isset($current_filter['count']) ? sprintf(' (%d)', (int) $current_filter['count']) : '')
                );
                echo '<ul class="filters__dropdown">';
                foreach ($listing_filters as $filter) {
                    echo sprintf(
                        '<li class="dropdown__item%s"><a class="dropdown__link" href="%s">%s%s</a></li>',
                        $current_filter['link'] === $filter['link'] ? ' dropdown__current' : '',
                        htmlspecialchars($filter['url']),
                        htmlspecialchars($filter['title']),
                        (isset($filter['count']) ? sprintf(' (%d)', (int) $filter['count']) : '')
                    );
                }
                echo '</ul>';
                echo '</li>';
                echo '</ul>';

                // IDX Feed Switcher
                echo $feed_switcher;

                // Display listings
                if (!empty($listings)) {
                    $result_tpl = $page->locateTemplate('idx', 'misc', 'result');
                    echo '<div class="listings columns">';
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

                    echo '<p>';
                    echo sprintf('You currently have no %s listings', htmlspecialchars($current_filter['link']));
                    if (!empty($selected_feed)) echo ' in ' . htmlspecialchars($selected_feed);
                    echo '.</p>';

                }

            ?>
        </section>
        <?php

            /*** Viewing search activity ***/
            } elseif ($current_view['link'] === 'searches') {

        ?>
        <section class="dashboard__searches">
            <?php

                // Display search filters
                echo '<ul class="dashboard__filters">';
                echo '<li class="filters__list">';
                echo sprintf(
                    '<a>%s%s</a>',
                    htmlspecialchars($current_filter['title']),
                    (isset($current_filter['count']) ? sprintf(' (%d)', (int) $current_filter['count']) : '')
                );
                echo '<ul class="filters__dropdown">';
                foreach ($search_filters as $filter) {
                    echo sprintf(
                        '<li class="dropdown__item%s"><a class="dropdown__link" href="%s">%s%s</a></li>',
                        $current_filter['link'] === $filter['link'] ? ' dropdown__current' : '',
                        htmlspecialchars($filter['url']),
                        htmlspecialchars($filter['title']),
                        (isset($filter['count']) ? sprintf(' (%d)', (int) $filter['count']) : '')
                    );
                }
                echo '</ul>';
                echo '</li>';
                echo '</ul>';

                // IDX Feed Switcher
                echo $feed_switcher;

                // Display searches
                if (!empty($searches)) {

            ?>
            <div class="saved__searches">
                <?php foreach ($searches as $search) { ?>
                    <div class="article -mar-bottom-lg">
                        <div class="article__body">
                            <h2 class="-text-lg -mar-bottom-xs -text-upper">
                                <a href="<?=$search['url']; ?>" target="_parent"><?=Format::htmlspecialchars($search['title']); ?></a>
                                <form class="-right" id="delete-form" method="post" onsubmit="return confirm('Are you sure you want to remove this search?');">
                                    <input type="hidden" name="delete" value="<?=$search['id']; ?>">
                                    <button class="search__delete" onclick="$('#delete-form').submit()">
                                        <svg class="icon-trash">
                                            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="<?=$assetPath; ?>/assets.svg#icon--trash"></use>
                                        </svg>
                                    </button>
                                </form>
                            </h2>
                        </div>
                        <span class="search__details">
                            <?php if ($current_filter['link'] === 'suggested') { ?>
                                Suggested:
                            <?php } else if ($current_filter['link'] === 'viewed') { ?>
                                Last Viewed:
                            <?php } else { ?>
                                Search Saved:
                            <?php } ?>
                            <time title="<?=date('l, F jS Y \@ g:ia', $search['timestamp']); ?>">
                                <?=Format::dateRelative($search['timestamp']); ?>
                            </time>
                        </span>
                        <div class="search__stats">
                            <?php if ($current_filter['link'] === 'saved') { ?>
                                <span class="search__stat search__frequency">Email Updates: <?=ucwords($search['frequency']); ?></span>
                                <?php if (!empty($search['timestamp_sent'])) { ?>
                                    <span class="search__stat search__update">
                                        Last Update:
                                        <time title="<?=date('l, F jS Y \@ g:ia', $search['timestamp_sent']); ?>">
                                            <?=Format::dateRelative($search['timestamp_sent']); ?>
                                        </time>
                                    </span>
                                <?php } ?>
                            <?php } ?>
                            <?php if ($current_filter['link'] === 'viewed') { ?>
                                <span class="search__stat"><?=number_Format($search['views']); ?> <?=Format::plural($search['views'], 'Views', 'View'); ?></span>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <?php

                } else {

                    echo '<p>';
                    echo sprintf('You currently have no %s searches', htmlspecialchars($current_filter['link']));
                    if (!empty($selected_feed)) echo ' in ' . Format::htmlspecialchars($selected_feed);
                    echo '.</p>';

                }

            ?>
        </section>
        <?php

            /*** Viewing preferences ***/
            } elseif ($current_view['link'] === 'preferences') {

        ?>
        <section class="preferences">
            <form id="form-preferences" method="post"<?=$current_form !== 'preferences' ? ' class="hidden"' : ''; ?>">
                <input type="hidden" name="form" value="preferences">
                <div class="divider">
                    <span class="divider__label -left">Contact Information</span>
                </div>
                <div class="columns">
                    <div class="field column -width-1/2 -width-1/1@xs">
                        <label class="field__label">First Name</label>
                        <input name="first_name" value="<?=Format::htmlspecialchars($preferences['first_name']); ?>">
                    </div>
                    <div class="field column -width-1/2 -width-1/1@xs">
                        <label class="field__label">Last Name</label>
                        <input name="last_name" value="<?=Format::htmlspecialchars($preferences['last_name']); ?>">
                    </div>
                    <div class="field column -width-1/1 ">
                        <label class="field__label">Email Address <small class="required">*</small></label>
                        <input type="email" name="email" value="<?=Format::htmlspecialchars($preferences['email']); ?>" required>
                    </div>
                    <div class="field column -width-1/1 ">
                        <label class="field__label">Alternate Email Address</label>
                        <input class="w1/1"  type="email" name="email_alt" value="<?=Format::htmlspecialchars($preferences['email_alt']); ?>">
                        <label class="toggle toggle--stacked">
                            <input type="checkbox" name="email_alt_cc_searches" value="saved_searches"<?=($preferences['email_alt_cc_searches'] === 'true' ? ' checked' : ''); ?>>
                            <span class="toggle__label">Send CC of saved search updates to this email address.</span>
                        </label>
                    </div>
                </div>
                <div class="columns">
                    <div class="field column -width-1/2 -width-1/1@xs">
                        <label class="field__label">Primary Phone<?php if (!empty(Settings::getInstance()->SETTINGS['registration_phone'])) echo '<small class="required">*</small>'; ?></label>
                        <input type="tel" name="phone" value="<?=$preferences['phone']; ?>">
                    </div>
                    <div class="field column -width-1/2 -width-1/1@xs">
                        <label class="field__label">Secondary Phone</label>
                        <input type="tel" name="phone_cell" value="<?=Format::htmlspecialchars($preferences['phone_cell']); ?>">
                    </div>
                    <div class="field column -width-1/2 -width-1/1@xs">
                        <label class="field__label">Work Phone</label>
                        <input type="tel" name="phone_work" value="<?=Format::htmlspecialchars($preferences['phone_work']); ?>">
                    </div>
                    <div class="field column -width-1/2 -width-1/1@xs">
                        <label class="field__label">Fax Number</label>
                        <input type="tel" name="phone_fax" value="<?=Format::htmlspecialchars($preferences['phone_fax']); ?>">
                    </div>
                </div>
                <div class="divider">
                    <span class="divider__label -left">Mailing Address</span>
                </div>
                <div class="columns">
                    <div class="field column -width-1/1">
                        <label class="field__label">Street Address</label>
                        <input name="address1" value="<?=Format::htmlspecialchars($preferences['address1']); ?>">
                    </div>
                    <div class="field column -width-1/2 -width-1/1@xs">
                        <label class="field__label">City</label>
                        <input name="city" value="<?=Format::htmlspecialchars($preferences['city']); ?>">
                    </div>
                    <div class="field column -width-1/2 -width-1/1@xs">
                        <label class="field__label"><?=Locale::spell('State'); ?></label>
                        <input name="state" value="<?=Format::htmlspecialchars($preferences['state']); ?>">
                    </div>
                    <div class="field column -width-1/2 -width-1/1@xs">
                        <label class="field__label"><?=Locale::spell('Zip Code'); ?></label>
                        <input name="zip" value="<?=Format::htmlspecialchars($preferences['zip']); ?>">
                    </div>
                    <div class="field column -width-1/2 -width-1/1@xs">
                        <label class="field__label">Country</label>
                        <input name="country" value="<?=Format::htmlspecialchars($preferences['country']); ?>">
                    </div>
                </div>
                <div class="divider">
                    <span class="divider__label -left">Subscription Settings</span>
                </div>
                    <div class="field">
                        <label class="toggle">
                            <input type="checkbox" name="opt_searches" value="in"<?=($preferences['opt_searches'] != 'out' ? ' checked' : ''); ?>>
                            <span class="toggle__label">Yes, I would like to receive listing updates matching my saved search criteria.</span>
                        </label>
                    </div>
                    <div class="field">
                        <label class="toggle">
                            <input type="checkbox" name="opt_marketing" value="in"<?=($preferences['opt_marketing'] != 'out' ? ' checked' : ''); ?>>
                            <span class="toggle__label"><?=$opt_text['opt_marketing'] ?: 'Please send me updates concerning this website and the real estate market.'; ?></span>
                        </label>
                    </div>
                    <?php if (Settings::getInstance()->MODULES['REW_PARTNERS_TWILIO']) { ?>
                    <div class="field">
                        <label class="toggle">
                            <input type="checkbox" name="opt_texts" value="in"<?=($preferences['opt_texts'] == 'in' ? ' checked' : ''); ?>>
                            <span class="toggle__label"><?=$opt_text['opt_texts'] ?: 'I consent to receiving text messages from this site.'; ?></span>
                        </label>
                    </div>
                    <?php } ?>
                <div class="preference__btns -mar-top buttons">
                    <button type="submit" class="button button--strong -mar-bottom-sm@xs -mar-bottom-sm@sm">Update Preferences</button>
                    <?php if (!empty(Settings::getInstance()->SETTINGS['registration_password'])) { ?>
                        <button type="button" class="button show-password">Change My Password</button>
                    <?php } ?>
                </div>
            </form>
            <?php if (!empty(Settings::getInstance()->SETTINGS['registration_password'])) { ?>
                <form id="form-password" method="post"<?=$current_form !== 'password' ? ' class="hidden"' : ''; ?>">
                    <div class="divider">
                        <span class="divider__label -left">Change Password</span>
                    </div>
                    <div class="columns">
                        <input type="hidden" name="form" value="password">
                        <?php if (!empty($preferences['password'])) { ?>
                            <div class="field column -width-1/1">
                                <label class="field__label">Your Current Password <small class="required">*</small></label>
                                <input type="password" name="current_password" value="" required>
                            </div>
                        <?php } ?>
                        <div class="field column -width-1/1">
                            <label class="field__label">Your New Password <small class="required">*</small></label>
                            <input type="password" name="new_password" value="" required>
                        </div>
                        <div class="field column -width-1/1">
                            <label class="field__label">Repeat New Password <small class="required">*</small></label>
                            <input type="password" name="confirm_password" value="" required>
                        </div>
                    </div>
                    <div class="preference__btns mar-top buttons">
                        <button type="submit" class="button button--strong -mar-bottom-sm@xs -mar-bottom-sm@sm">Update Password</button>
                        <button type="button" class="button show-preferences">Back to Preferences</button>
                    </div>
                </form>
            <?php } ?>
        </section>
        <?php

            /*** Viewing messages ***/
            } elseif ($current_view['link'] === 'messages') {

        ?>
        <section id="section-messages" class="section<?=$_GET['form'] === 'compose' ? ' hidden' : ''; ?>">
            <header class="messages__header -clear">
                <span>Messages</span>
                <a class="button button--sm -right show-compose">Compose Message</a>
            </header>
            <?php if (!empty($threads)) { ?>
                <div class="message__threads">
                    <?php foreach ($threads as $thread) { ?>
                        <div class="message__thread">
                            <a href="<?=$thread['url']; ?>">Message Subject: <?=Format::htmlspecialchars($thread['subject']); ?></a>
                            <form class="thread__delete" method="post" onsubmit="return confirm('Are you sure you want to delete this message?');">
                                <input type="hidden" name="delete" value="<?=$thread['id']; ?>">
                                <button type="submit">
                                    <svg class="icon-trash">
                                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="<?=$assetPath; ?>/assets.svg#icon--trash"></use>
                                    </svg>
                                </button>
                            </form>
                            <div class="thread__details">
                                <span>Agent Name: <?=Format::number($thread['agent']); ?></span>
                                <span>Replies: <?=Format::number($thread['replies']); ?></span>
                                <span>
                                    Last Message Sent On: 
                                    <time title="<?=date('l, F jS Y \@ g:ia', $thread['timestamp']); ?>"><?=Format::dateRelative($thread['timestamp']); ?>
                                    </time>
                                </span>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <p>You currently have no messages.</p>
            <?php } ?>
        </section>
        <form id="form-compose" method="post" class=" o0<?=$_GET['form'] === 'compose' ? '' : ' hidden'; ?>">
            <section class="section section-fldset">
                <input type="hidden" name="form" value="compose">
                <fldset>
                    <header class="messages__header -clear">
                        <span>Compose New Message</span>
                        <a class="button button--sm -right show-messages">Back to Messages</a>
                    </header>
                    <div class="field">
                        <label class="field__label">Subject <small class="required">*</small></label>
                        <input name="subject" value="" required>
                    </div>
                    <div class="field">
                        <label class="field__label">Message <small class="required">*</small></label>
                        <textarea name="message" rows="6" required></textarea>
                    </div>
                </fldset>
                <div class="buttons">
                    <button type="submit" class="button button--strong">Send Message</button>
                </div>
            </section>
        </form>
        <?php

            /*** Viewing message thread ***/
            } elseif ($current_view['link'] === 'message') {

        ?>
        <section id="section-messages" class="messages">
            <header class="messages__header">
                <span><?=$thread['sent_from'] == 'agent' ? Format::htmlspecialchars($thread['agent']) : 'Your'; ?> Message</span>
                <a class="button button--sm -right" href="?view=messages">Back to Messages</a>
            </header>
            <div class="-width-1/2">
                <div class="message<?=($thread['sent_from'] == 'agent' ? ' message--agent' : ''); ?>">
                    <span class="message__avatar -thumb">
                        <?php if ($thread['sent_from'] === 'agent') { ?>
                            <img height="48" width="48" data-src="<?=$thread['agent_photo']
                                ? sprintf('/thumbs/100x100/uploads/agents/%s', htmlspecialchars($thread['agent_photo']))
                                : sprintf('%s/person.png', $assetPath); ?>">
                        <?php } else { ?>
                            <img height="48" width="48" data-src="<?=htmlspecialchars($user->getPhotoUrl()); ?>">
                        <?php } ?>
                    </span>
                    <div class="message__content">
                        <span class="message__title"><?=Format::htmlspecialchars($thread['subject']); ?></span>
                        <p class="message__body">
                            <?=$thread['message']; ?>
                        </p>
                        <span class="message__timestamp">
                            <time title="<?=date('l, F jS Y \@ g:ia', $thread['timestamp']); ?>">
                                Sent: <?=Format::dateRelative($thread['timestamp']); ?>
                            </time>
                        </span>
                    </div>
                </div>
                <?php if (!empty($replies)) { ?>
                    <?php foreach ($replies as $reply) { ?>
                        <div class="message<?=($reply['sent_from'] == 'agent' ? ' message--agent' : ''); ?>">
                            <span class="message__avatar -thumb">
                                <?php if ($reply['sent_from'] === 'agent') { ?>
                                    <img height="48" width="48" data-src="<?=$reply['agent_photo']
                                        ? sprintf('/thumbs/100x100/uploads/agents/%s', htmlspecialchars($reply['agent_photo']))
                                        : sprintf('%s/person.png', $assetPath); ?>">
                                <?php } else { ?>
                                    <img height="48" width="48" data-src="<?=htmlspecialchars($user->getPhotoUrl()); ?>">
                                <?php } ?>
                            </span>
                            <div class="message__content">
                                <p class="message__body">
                                    <?=$reply['message']; ?>
                                </p>
                                <span class="message__timestamp">
                                    <?=$reply['sent_from'] == 'agent' ? Format::htmlspecialchars($reply['agent']) : 'You'; ?> replied:
                                    <time title="<?=date('l, F jS Y \@ g:ia', $reply['timestamp']); ?>">
                                        <?=Format::dateRelative($reply['timestamp']); ?>
                                    </time>
                                </span>
                                <?php if ($reply['sent_from'] == 'lead') { ?>
                                    <form method="post" class="-mar-top-xs" onsubmit="return confirm('Are you sure you want to delete this reply?');">
                                        <input type="hidden" name="delete" value="<?=$reply['id']; ?>">
                                        <button type="submit" class="button button-sm">
                                            Delete Reply
                                        </button>
                                    </form>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
            <form id="form-reply" method="post" class="">
                <input type="hidden" name="form" value="reply">
                <div class="input field reply--field">
                    <input name="message" placeholder="New Message..." required>
                    <button type="submit" class="button button--strong button--sm">Send</button>
                </div>
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