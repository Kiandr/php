<section id="section-messages" class="section<?= $_GET['form'] === 'compose' ? 'uk-hidden' : ''; ?>">

    <div class="uk-nbfc uk-clearfix">
        <h3 class="uk-align-left">My Messages</h3>
        <a class="uk-button uk-align-right" data-uk-toggle="{target:'#form-compose'}">Compose Message</a>
    </div>

    <?php if (!empty($threads)) { ?>
        <table class="uk-table uk-table-striped uk-table-hover uk-text-nowrap">
            <thead>
                <tr>
                    <th>Message Subject</th>
                    <th>Agent Name</th>
                    <th>Replies</th>
                    <th>Last Message</th>
                    <th>&nbsp</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($threads as $thread) { ?>
                    <tr valign="top"<?= !empty($thread['unread']) ? ' class="unread"' : ''; ?>>
                        <td>
                            <?php if (!empty($thread['unread'])) { ?>
                                <span class="uk-badge uk-badge-success">New</span>
                            <?php } ?>
                            <a href="<?= Format::htmlspecialchars($thread['url']); ?>">
                                <?= Format::htmlspecialchars($thread['subject']); ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?= Format::htmlspecialchars($thread['url']); ?>">
                                <?= Format::number($thread['agent']); ?>
                            </a>
                        </td>
                        <td>
                            <a href="<?= Format::htmlspecialchars($thread['url']); ?>">
                                <?= Format::number($thread['replies']); ?>
                            </a>
                        </td>
                        <td>
                            <time title="<?= date('l, F jS Y \@ g:ia', $thread['timestamp']); ?>">
                                <?= Format::dateRelative($thread['timestamp']); ?>
                            </time>
                        </td>
                        <td class="uk-text-right">
                            <form method="post" data-confirm="Are you sure you want to delete this message?" action="<?= Format::htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                                <input type="hidden" name="delete" value="<?= $thread['id']; ?>">
                                <button type="submit" class="uk-button uk-button-plain uk-button-small"><i class="uk-icon uk-icon-remove"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>

        <div data-uk-alert="" class="uk-alert">
            <a class="uk-alert-close uk-close" href=""></a>
            <p>You currently have no messages.</p>
        </div>

    <?php } ?>

    <form id="form-compose" method="POST" class="uk-form uk-form-stacked<?= $_GET['form'] === 'compose' ? '' : ' uk-hidden'; ?>" action="<?= Format::htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
        <input type="hidden" name="form" value="compose">
        <fieldset data-uk-margin>
            <legend>Compose New Message</legend>

            <div class="uk-form-row">
                <label class="uk-form-label" for="subject">Subject</label>
                <div class="uk-form-controls">
                    <input name="subject" id="subject" class="uk-width-1-1 uk-form-large" value="" required>
                </div>
            </div>

            <div class="uk-form-row">
                <label class="uk-form-label" for="message">Message</label>
                <div class="uk-form-controls">
                    <textarea name="message" id="message" rows="9" class="uk-width-1-1" required></textarea>
                </div>
            </div>

        </fieldset>

        <div class="uk-form-row">
            <button type="submit" class="uk-button uk-button-medium uk-margin-top">Send Message</button>
            <a class="uk-button uk-button-medium uk-button-plain uk-margin-top" data-uk-toggle="{target:'#form-compose'}">Back to Messages</a>
        </div>
    </form>

</section>
