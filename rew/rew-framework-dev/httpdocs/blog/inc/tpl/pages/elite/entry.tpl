<?php if (!empty($entry)) { ?>

<article class="uk-article">
    <header>

        <h1 class="uk-article-title"><?=Format::htmlspecialchars($entry['title']); ?><?=($entry['published'] == 'false') ? ' (Unpublished)' : ''; ?></h1>

        <p class="uk-article-meta">
                Posted
                <?php if (!empty($author)) { ?>
                    by <a href="<?= Format::htmlspecialchars(sprintf(URL_BLOG_AUTHOR, $author['link'])); ?>"><?= Format::htmlspecialchars($author['name']); ?></a>
                <?php } ?>
                on <?=($entry['published'] == 'false') ? '(Unpublished)' : date('l\, F jS\, Y \a\t g:ia', strtotime($entry['timestamp_published'])); ?>.
        </p>

    </header>

    <?= $entry['body']; ?>

    <?php if ($author['blog_signature_on'] == 'true') { ?>
        <div class="signature">
            <?= $author['blog_signature']; ?>
        </div>
    <?php } ?>

    <?php if (!empty($entry['link_url1']) ||!empty($entry['link_url2']) || !empty($entry['link_url3'])) { ?>
        <div class="nav">
            <h4>Related Links</h4>
            <ul class="uk-list">
                <?php // Since we're touching this anyway, change this to use <= 3 (legacy) but also to allow any number ?>
                <?php for ($i = 1; !empty($entry['link_url' . $i]) || $i <= 3; $i++) { ?>
                    <?php if (!empty($entry['link_url' . $i])) { ?>
                        <li><a href="<?= Format::htmlspecialchars($entry['link_url' . $i]); ?>" target="_blank"><?= Format::htmlspecialchars($entry['link_title' . $i]); ?></a></li>
                    <?php } ?>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>

    <?php if (!empty($tags)) { ?>
        <div class="uk-clearfix uk-margin-bottom">
            <h4 class="uk-margin-bottom-remove">Tags</h4>
            <ul class="uk-list uk-margin-top-remove">
                <?php foreach ($tags as $tag) { ?>
                   <li class="uk-float-left uk-margin-right"><a href="<?= Format::htmlspecialchars(sprintf(URL_BLOG_TAG, $tag['link'])); ?>"><?= Format::htmlspecialchars($tag['title']); ?> (<?= $tag['total']; ?>)</a></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>

    <div class="uk-margin-bottom">
        <a class="uk-button" rel="nofollow" target="_blank" href="<?= Format::htmlspecialchars(sprintf(URL_BLOG_ENTRY_PRINT, $entry['link'])); ?>"><i class="uk-icon-print"></i> Print</a>
        <a class="uk-button share popup" href="<?= Format::htmlspecialchars(sprintf(URL_BLOG_ENTRY_SHARE, $entry['link'])); ?>" data-modal data-modal-auto><i class="uk-icon-share"></i> Share</a>
    </div>

    <a name="comments"></a>

    <div id="blog-comments">

        <?php if (!empty($message)) { ?>
            <div class="uk-alert"><?= $message; ?></div>
        <?php } ?>
        <?php if (!empty($errors)) { ?>
            <div class="uk-alert uk-alert-danger"><p><?= implode('</p><p>', $errors); ?></p></div>
        <?php } ?>

        <?php if (!empty($count_comments['total']) && !empty($comments)) { ?>

            <h4><?= Format::number($count_comments['total']);?> <?= Format::plural($count_comments['total'], 'Responses', 'Response'); ?> to "<?= Format::htmlspecialchars($entry['title']); ?>"</h4>

            <?php foreach ($comments as $comment) { ?>

                <div class="comment">
                    <?php if (!empty($comment['website'])) { ?>
                        <strong><a href="<?=Format::htmlspecialchars($comment['website']); ?>" rel="external nofollow" target="_blank"><?=Format::htmlspecialchars($comment['name']); ?></a> wrote:</strong>
                    <?php } else { ?>
                        <strong><?=Format::htmlspecialchars($comment['name']); ?> wrote:</strong>
                    <?php } ?>

                    <?= nl2br(trim(Format::htmlspecialchars($comment['comment']), "\n ")); ?>
                    <p class="uk-article-meta">Posted on <?=date('l\, F jS\, Y \a\t g:ia', strtotime($comment['timestamp_created'])); ?>.</p>
                </div>

            <?php } ?>

        <?php } ?>

    </div>

    <?php if ($entry['published'] == 'true') { ?>

        <?php if (!empty($show_form)) { ?>

            <h3>Leave a Comment</h3>

            <form action="?comment#blog-comments" method="post" class="uk-form">

                <div class="uk-grid">

                    <?php if ($backend_user->isValid()) { ?>

                        <div class="uk-width-1-1">
                            <p class="uk-margin-bottom">You are currently logged in as <strong><?=Format::htmlspecialchars($backend_user->info('first_name') . ' ' . $backend_user->info('last_name')); ?></strong>.</p>
                        </div>

                    <?php } else { ?>

                        <?php require Settings::getInstance()->URLS['HONEYPOT']; ?>

                        <div class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 uk-margin-bottom">
                            <input class="uk-width-1-1 uk-form-large" placeholder="Your Name" name="comment_name" value="<?=Format::htmlspecialchars($_POST['comment_name']); ?>" required>
                        </div>
                        <div class="uk-width-large-1-2 uk-width-medium-1-2 uk-width-small-1-1 uk-margin-bottom">
                            <input class="uk-width-1-1 uk-form-large" placeholder="Your Email (kept private)" type="email" name="comment_email" value="<?=Format::htmlspecialchars($_POST['comment_email']); ?>" required>
                        </div>

                        <div class="uk-width-1-1 uk-margin-bottom">
                            <input class="uk-width-1-1 uk-form-large" placeholder="Website (optional)" type="url" name="comment_website" value="<?=Format::htmlspecialchars($_POST['comment_website']); ?>">
                        </div>

                    <?php } ?>

                    <div class="uk-width-1-1 uk-margin-bottom">
                        <textarea class="uk-width-1-1" placeholder="Comment" cols="32" rows="5" name="comment" required><?=Format::htmlspecialchars($_POST['comment']); ?></textarea>
                    </div>

                    <?php // Require CAPTCHA Code ?>
                    <?php if ($blog_settings['captcha'] == 't' && !$backend_user->isValid()) { ?>
                        <div class="uk-width-1-1 uk-position-relative captcha-container">
                            <input class="uk-width-1-1 uk-form-large uk-margin-bottom" placeholder="Security Code" name="captcha">
                            <div class="captcha uk-width-1-1 uk-margin-bottom"><img src="/captcha.png?<?=md5(time()); ?>" border="0"></div>
                        </div>
                    <?php } ?>

                    <div class="uk-width-1-1">
                        <button type="submit" class="uk-button">Comment</button>
                    </div>

                </div>
            </form>

        <?php } ?>

    <?php } ?>

<?php } else { ?>

    <h1>Blog Error</h1>

    <div class="uk-alert uk-alert-danger">
        <p>The selected blog entry could not be found.</p>
    </div>

</article>
<?php } ?>
