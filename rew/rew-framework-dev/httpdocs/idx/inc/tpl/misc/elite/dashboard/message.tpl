<section class="section-message">

    <div class="uk-nbfc uk-clearfix uk-margin-top">
        <h3 class="uk-align-left">Read Message</h3>
        <a href="?view=messages" class="uk-button uk-align-right show-compose">Back to Messages</a>
    </div>

    <div>
        <span class="uk-text-bold">Message Subject:</span> <?= Format::htmlspecialchars($thread['subject']); ?>
        <br>
        <span class="uk-text-bold">Message Sent By:</span> <?= $thread['sent_from'] == 'agent' ? Format::htmlspecialchars($thread['agent']) : 'You'; ?>
    </div>

    <p>
        <span class="uk-text-bold">Message</span>
        <?= $thread['message']; ?>
    </p>

    <p class="uk-text-muted uk-text-small">
        <time title="<?= date('l, F jS Y \@ g:ia', $thread['timestamp']); ?>">
            Message was sent <?= Format::dateRelative($thread['timestamp']); ?>
        </time>
    </p>

    <?php if (!empty($replies)) { ?>
        <h3>Replies to Message</h3>
        <ul class="uk-comment-list">
            <?php
                end($replies);
                $last_reply_key = key($replies);
            ?>
            <?php foreach ($replies as $i => $reply) { ?>
            <li>
                <article class="uk-comment">
                    <header class="uk-comment-header">
                        <h4 class="uk-comment-title">
                            Author: <?= $reply['sent_from'] == 'agent' ? Format::htmlspecialchars($reply['agent']) : 'You'; ?>
                        </h4>
                        <div class="uk-comment-meta">
                            Sent: <time title="<?= date('l, F jS Y \@ g:ia', $reply['timestamp']); ?>"><?= Format::dateRelative($reply['timestamp']); ?></time>
                        </div>
                    </header>
                    <div class="uk-comment-body">
                        <?= $reply['message']; ?>
                        <?php if ($reply['sent_from'] == 'lead') { ?>
                            <form method="post" data-confirm="Are you sure you want to delete this reply?" action="<?= Format::htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
                                <input type="hidden" name="delete" value="<?= $reply['id']; ?>">
                                <button type="submit" class="uk-button uk-button-small uk-margin-top negative">
                                    Delete Message
                                </button>
                            </form>
                        <?php } ?>
                    </div>
                    <?php if ($i != $last_reply_key) { ?>
                        <hr class="uk-article-divider">
                    <?php } ?>
                </article>
            </li>
            <?php } ?>
        </ul>
    <?php } ?>

    <form id="form-reply" method="post" class="uk-form uk-form-stacked" action="<?= Format::htmlspecialchars($_SERVER['REQUEST_URI']); ?>">
        <input type="hidden" name="form" value="reply">
        <fieldset>
            <legend>Reply to this message&hellip;</legend>
            <div class="uk-form-row">
                <label class="uk-form-label" for="message">Your Reply</label>
                <div class="uk-form-controls">
                    <textarea name="message" id="message" rows="9" class="uk-width-1-1" required></textarea>
                </div>
            </div>
        </fieldset>
        <div class="uk-form-row">
            <button type="submit" class="uk-button uk-button-medium uk-margin-top">Send Reply</button>
        </div>
    </form>

</section>
