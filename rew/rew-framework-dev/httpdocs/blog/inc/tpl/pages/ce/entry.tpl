<?php if (!empty($entry)) { ?>
    <header>
        <div>
            <h1><?=htmlspecialchars($entry['title']); ?><small><?=($entry['published'] == 'false') ? ' (Unpublished)' : ''; ?></small></h1>
        </div>
        <p>
            <em>
                Posted
                <?php if (!empty($author)) { ?>
                    by <a href="<?=sprintf(URL_BLOG_AUTHOR, $author['link']); ?>"><?=htmlspecialchars($author['name']); ?></a>
                <?php } ?>
                on <?=($entry['published'] == 'false') ? '(Unpublished)' : date('l\, F jS\, Y \a\t g:ia', strtotime($entry['timestamp_published'])); ?>.
            </em>
        </p>
    </header>
    <div class="body -mar-bottom-md" style="overflow: hidden;"><?=$entry['body']; ?></div>
    <?php if ($author['blog_signature_on'] == 'true') { ?>
        <div class="signature">
            <?=$author['blog_signature']; ?>
        </div>
    <?php } ?>
    <?php if (!empty($entry['link_url1']) ||!empty($entry['link_url2']) || !empty($entry['link_url3'])) { ?>
        <div class="nav">
            <h4>Related Links</h4>
            <ul>
                <?php if (!empty($entry['link_url1'])) { ?>
                    <li><a href="<?=htmlspecialchars($entry['link_url1']); ?>" target="_blank"><?=htmlspecialchars($entry['link_title1']); ?></a></li>
                <?php } ?>
                <?php if (!empty($entry['link_url2'])) { ?>
                    <li><a href="<?=htmlspecialchars($entry['link_url2']); ?>" target="_blank"><?=htmlspecialchars($entry['link_title2']); ?></a></li>
                <?php } ?>
                <?php if (!empty($entry['link_url3'])) { ?>
                    <li><a href="<?=htmlspecialchars($entry['link_url3']); ?>" target="_blank"><?=htmlspecialchars($entry['link_title3']); ?></a></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>
    <?php if (!empty($tags)) { ?>
        <div class="blog__tags -clear -mar-vertical-lg">
            <h4>Tags</h4>
            <ul class="tag__list">
                <?php foreach ($tags as $tag) { ?>
                   <li class="tag__item"><a class="tag__link -text-xs -pad-xs -mar-right-xs -pill" href="<?=sprintf(URL_BLOG_TAG, $tag['link']); ?>"><?=htmlspecialchars($tag['title']); ?> (<?=$tag['total']; ?>)</a></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>
    <div class="buttons">
        <a class="button print" title="Opens new window with print ready page" rel="nofollow" target="_blank" href="<?=sprintf(URL_BLOG_ENTRY_PRINT, $entry['link']); ?>"><em class="icon-print"></em> Print</a>
        <a class="button share popup" title="Opens dialog" href="<?=sprintf(URL_BLOG_ENTRY_SHARE, $entry['link']); ?>"><em class="icon-share"></em> Share</a>
    </div>
    <a name="comments"></a>
    <div id="blog-comments" class="-pad-top">
        <?php

            // Success
            if (!empty($message)) {
                echo '<div class="notice notice--positive -mar-bottom">';
                echo sprintf('<div class="notice__message">%s</div>', $message);
                echo '</div>';
            }

            // Errors
            if (!empty($errors)) {
                echo '<div class="notice notice--negative -mar-bottom">';
                echo sprintf('<div class="notice__message">%s</div>',implode('<br />', $errors));
                echo '</div>';
            }

        ?>
        <div class="-pad-vertical">
            <?php if (!empty($count_comments['total']) && !empty($comments)) { ?>
                <h4><?=Format::number($count_comments['total']);?> <?=Format::plural($count_comments['total'], 'Responses', 'Response'); ?> to "<?=htmlspecialchars($entry['title']); ?>"</h4>
                <?php foreach ($comments as $comment) { ?>
                    <div class="comment -mar-bottom">
                        <?php if (!empty($comment['website'])) { ?>
                            <strong><a href="<?=htmlspecialchars($comment['website']); ?>" rel="external nofollow" target="_blank"><?=htmlspecialchars($comment['name']); ?></a> wrote:</strong>
                        <?php } else { ?>
                            <strong><?=htmlspecialchars($comment['name']); ?> wrote:</strong>
                        <?php } ?>
                        <?=nl2br(trim(htmlspecialchars($comment['comment']), "\n ")); ?>
                        <small class="-mar-top-xs"><em>Posted on <?=date('l\, F jS\, Y \a\t g:ia', strtotime($comment['timestamp_created'])); ?>.</em></small>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
    <?php

    if ($entry['published'] == 'true') {
        if (!empty($show_form)) {
            if ($commentForm = $page->locateTemplate('blog', 'misc', 'comment-form')) {
                include $commentForm;
            }
        }
    }

    ?>
<?php } else { ?>
    <h1>Blog Error</h1>
    <div class="notice notice--negative">
        <div class="notice__message">The selected blog entry could not be found.</div>
    </div>
<?php } ?>
