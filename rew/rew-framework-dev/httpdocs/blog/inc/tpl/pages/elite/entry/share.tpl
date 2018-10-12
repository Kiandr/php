<?php if (!empty($entry)) { ?>

    <header>

        <h1><?= Format::htmlspecialchars($entry['title']); ?><?= ($entry['published'] == 'false') ? ' (Unpublished)' : ''; ?></h1>

        <p>
            <em>
                Posted
                <?php if (!empty($author)) { ?>
                    by <a href="<?= Format::htmlspecialchars(sprintf(URL_BLOG_AUTHOR, $author['link'])); ?>"><?=Format::htmlspecialchars($author['name']); ?></a>
                <?php } ?>
                on <?=($entry['published'] == 'false') ? '(Unpublished)' : date('l\, F jS\, Y \a\t g:ia', strtotime($entry['timestamp_published'])); ?>.
            </em>
        </p>

    </header>
    <p></p>

    <div class="modal-header">
        <h3 id="share-it">Share this Article with a Friend.</h3>
    </div>

    <div class="modal-body">
    <?php if ($entry['published'] == 'true') { ?>

        <div id="social-network-panel" class="uk-grid">
            <article class="uk-width-1-2 uk-width-small-1-4"><a title="Add to Facebook" href="javascript:var w = window.open('http://www.facebook.com/sharer.php?u=<?= rawurlencode(sprintf(URL_BLOG_ENTRY, $entry['link'])); ?>&amp;t=<?=urlencode($entry['title']); ?>', 'sharer', 'toolbar=0,status=0,scrollbars=1,width=660,height=400'); w.focus();"><img src="/img/facebook_001.jpg" alt=""> <h4>Facebook</h4> Share this post On Facebook</a></article>
            <article class="uk-width-1-2 uk-width-small-1-4"><a title="Share on Twitter" href="javascript:var w = window.open('http://twitter.com/home?status=<?= rawurlencode('Check out this blog post: ' . sprintf(URL_BLOG_ENTRY, $entry['link']));?>', 'twittersharer', 'toolbar=0,status=0,scrollbars=1,width=400,height=325'); w.focus();"><img src="/img/Twitter_001.jpg" alt=""> <h4>Twitter</h4> Tweet this post on Twitter</a></article>
            <article class="uk-width-1-2 uk-width-small-1-4"><a title="Share on Google+" href="javascript:var w = window.open('https://plusone.google.com/share?url=<?= rawurlencode(sprintf(URL_BLOG_ENTRY, $entry['link'])); ?>', 'gplusshare', 'toolbar=0,status=0,scrollbars=1,width=600,height=450'); w.focus();"><img src="/img/Google_Plus_001.jpg" alt=""> <h4>Google+</h4> Share this page on Google+</a></article>
            <article class="uk-width-1-2 uk-width-small-1-4"><a title="Send to Friend" href="mailto:?subject=<?=rawurlencode($entry['title']);?>&body=<?= rawurlencode("I was looking at " . Settings::getInstance()->SETTINGS['URL'] . "blog/ and I thought you would like to look at this blog entry:\r\n" . sprintf(URL_BLOG_ENTRY, $entry['link']));?>"><img src="/img/Email_001.jpg" alt=""> <h4>Via. Email</h4> Send a link to this post via Email.</a></article>
        </div>

    <?php } else { ?>

       <p class="uk-alert uk-alert-danger" data-uk-alert>
           <a href="" class="uk-alert-close uk-close"></a>
           You cannot share an unpublished blog entry.
       </p>

    <?php } ?>
    </div>

<?php } else { ?>

    <h1>Blog Error</h1>

    <div class="uk-alert uk-alert-danger">
        <p>The selected blog entry could not be found.</p>
    </div>

<?php } ?>
