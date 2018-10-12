<?php if (!empty($entry)) { ?>

    <h1><?=Format::htmlspecialchars($entry['title']); ?><?=($entry['published'] == 'false') ? ' (Unpublished)' : ''; ?></h1>

	<p>
	    <em>
	    	Posted
	    	<?php if (!empty($author)) { ?>
	    		by <a href="<?=sprintf(URL_BLOG_AUTHOR, $author['link']); ?>"><?=Format::htmlspecialchars($author['name']); ?></a>
    		<?php } ?>
	    	on <?=($entry['published'] == 'false') ? '(Unpublished)' : date('l\, F jS\, Y \a\t g:ia', strtotime($entry['timestamp_published'])); ?>.
	    </em>
    </p>

    <hr>

    <div class="body"><?=$entry['body']; ?></div>

    <hr>

    <?php if (!empty($comments)) { ?>

	    <div id="blog-comments">

	        <h3>Comments</h3>

	        <?php foreach ($comments as $comment) { ?>

	            <div class="comment">
	                <?php if (!empty($comment['website'])) { ?>
	                    <strong><a href="<?=Format::htmlspecialchars($comment['website']); ?>" rel="external nofollow" target="_blank"><?=Format::htmlspecialchars($comment['name']); ?></a> wrote:</strong>
	                <?php } else { ?>
	                    <strong><?=Format::htmlspecialchars($comment['name']); ?> wrote:</strong>
	                <?php } ?>
	                <br>
	                <?=nl2br(trim(Format::htmlspecialchars($comment['comment']), "\n ")); ?>
	                <p><em>Posted on <?=date('l\, F jS\, Y \a\t g:ia', strtotime($comment['timestamp_created'])); ?>.</em></p>
	            </div>

	        <?php } ?>

	    </div>

	    <hr>

    <?php } ?>

    <script>
        window.onload = window.print;
    </script>

	<?php exit; ?>

<?php } else { ?>

    <h1>Blog Error</h1>

    <div class="msg negative">
        <p>The selected blog entry could not be found.<p>
    </div>

<?php } ?>