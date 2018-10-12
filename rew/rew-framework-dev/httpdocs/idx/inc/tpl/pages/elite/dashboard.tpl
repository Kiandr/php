<div class="modal-header">
    <h1>My Dashboard</h1>
    <p>The dashboard below allows you to manage your account, including saved listings, searches,
        messages, preferences and more.</p>
</div>

<div class="modal-body">

    <div class="uk-nbfc uk-margin-bottom">
        <nav class="uk-navbar">
            <ul class="uk-navbar-nav">
                <?php foreach ($dashboard_views as $dashboard_link => $dashboard_view) { ?>
                    <li<?= ($current_view['link'] == $dashboard_link ? ' class="uk-active"' : ''); ?>>
                        <a href="<?= Format::htmlspecialchars(Settings::getInstance()->SETTINGS['URL_IDX_DASHBOARD'] . $dashboard_view['url']); ?>">
                            <?= Format::htmlspecialchars($dashboard_view['title']); ?>
                            <?php if (isset($dashboard_view['count'])) { ?>
                                <small class="label"><?= Format::number($dashboard_view['count']); ?></small>
                            <?php } ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
            <div class="uk-navbar-content uk-navbar-flip uk-hidden-small">
                <a href="<?= Format::htmlspecialchars(Settings::getInstance()->SETTINGS['URL_IDX_LOGOUT']); ?>" class="uk-button">Sign Out</a>
            </div>
        </nav>
    </div>

    <?php if (!empty($success)) { ?>
        <div class="uk-alert uk-alert-success">
            <p><?= implode('</p><p>', $success); ?></p>
        </div>
    <?php } ?>

    <?php if (!empty($errors)) { ?>
        <div class="uk-alert uk-alert-danger">
            <p><?= implode('</p><p>', $errors); ?></p>
        </div>
    <?php } ?>

    <?php
    // IDX feed switcher
    if (in_array($current_view['link'], array('searches', 'listings'))) {
        $feed_switcher = '';
        $selected_feed = '';
        if (!empty(Settings::getInstance()->IDX_FEEDS)) {
            $feed_switcher .= '<select onchange="REW.GoToURL(this.value);" class="uk-align-right">';
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

    // Current view
    if (!empty($current_view['link']) && ($view_file = $page->locateTemplate('idx', 'misc', 'dashboard', $current_view['link']))) {
        require $view_file;
    }
    ?>
</div>
