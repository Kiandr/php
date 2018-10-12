<section class="section section-searches uk-margin-small-left">
    <h3>My Saved Searches</h3>
    <div class="uk-nbfc uk-clearfix uk-margin-bottom">
        <?= $feed_switcher; ?>
        <ul class="uk-subnav uk-subnav-line uk-float-left">

            <?php foreach ($search_filters as $search_filter) { ?>
                <?php $selected = $current_filter['link'] == $search_filter['link'] ? ' class="uk-active"' : ''; ?>
                <li<?= $selected; ?>>
                    <a href="<?= Format::htmlspecialchars($search_filter['url']); ?>">
                        <?= Format::htmlspecialchars($search_filter['title']); ?>
                        <?php if (isset($search_filter['count'])) { ?>
                            (<?= Format::number($search_filter['count']); ?>)
                        <?php } ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
        <?php if (empty($searches)) { ?>
            <div class="uk-alert uk-alert-danger uk-margin-large-top">
                You currently have no <?= Format::htmlspecialchars($current_filter['link']); ?> searches
                <?php if (!empty($selected_feed)) echo ' in ' . Format::htmlspecialchars($selected_feed); ?>.
            </div>
        <?php } ?>
    </div>

    <?php if (!empty($searches)) { ?>
        <div class="uk-overflow-container">
            <table class="uk-table uk-table-striped uk-table-hover uk-text-nowrap">
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
                                <a href="<?= Format::htmlspecialchars($search['url']); ?>" target="_parent">
                                    <?= Format::htmlspecialchars($search['title']); ?>
                                </a>
                            </td>
                            <?php if ($current_filter['link'] === 'saved') { ?>
                                <td>
                                    <?= Format::htmlspecialchars(ucwords($search['frequency'])); ?>
                                </td>
                                <td nowrap>
                                    <?php if (!empty($search['timestamp_sent'])) { ?>
                                        <time title="<?= date('l, F jS Y \@ g:ia', $search['timestamp_sent']); ?>">
                                            <?= Format::dateRelative($search['timestamp_sent']); ?>
                                        </time>
                                    <?php } else { ?>
                                        <em>&ndash;</em>
                                    <?php } ?>
                                </td>
                            <?php } ?>
                            <td nowrap>
                                <time title="<?= date('l, F jS Y \@ g:ia', $search['timestamp']); ?>">
                                    <?= Format::dateRelative($search['timestamp']); ?>
                                </time>
                            </td>
                            <td>
                                <form class="uk-form" method="post" data-confirm="Are you sure you want to remove this search?" action="<?= Format::htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                                    <input type="hidden" name="delete" value="<?= $search['id']; ?>">
                                    <button type="submit" class="uk-button uk-button-plain uk-button-small"><i class="uk-icon uk-icon-remove"></i></button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
</section>
