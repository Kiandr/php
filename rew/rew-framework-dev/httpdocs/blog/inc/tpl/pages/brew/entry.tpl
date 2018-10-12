<?php if (!empty($entry)) { ?>

	<header>

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

	</header>

	<div class="body"><?=$entry['body']; ?></div>

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
					<li><a href="<?=Format::htmlspecialchars($entry['link_url1']); ?>" target="_blank"><?=Format::htmlspecialchars($entry['link_title1']); ?></a></li>
				<?php } ?>
				<?php if (!empty($entry['link_url2'])) { ?>
					<li><a href="<?=Format::htmlspecialchars($entry['link_url2']); ?>" target="_blank"><?=Format::htmlspecialchars($entry['link_title2']); ?></a></li>
				<?php } ?>
				<?php if (!empty($entry['link_url3'])) { ?>
					<li><a href="<?=Format::htmlspecialchars($entry['link_url3']); ?>" target="_blank"><?=Format::htmlspecialchars($entry['link_title3']); ?></a></li>
				<?php } ?>
			</ul>
		</div>
	<?php } ?>

	<?php if (!empty($tags)) { ?>
		<div class="nav horizontal">
			<h4>Tags</h4>
			<ul>
				<?php foreach ($tags as $tag) { ?>
				   <li><a href="<?=sprintf(URL_BLOG_TAG, $tag['link']); ?>"><?=Format::htmlspecialchars($tag['title']); ?> (<?=$tag['total']; ?>)</a></li>
				<?php } ?>
			</ul>
		</div>
	<?php } ?>

	<div class="btnset">
		<a class="btn print" rel="nofollow" target="_blank" href="<?=sprintf(URL_BLOG_ENTRY_PRINT, $entry['link']); ?>"><em class="icon-print"></em> Print</a>
		<a class="btn share popup" rel="nofollow" href="<?=sprintf(URL_BLOG_ENTRY_SHARE, $entry['link']); ?>"><em class="icon-share"></em> Share</a>
	</div>

	<a name="comments"></a>

	<div id="blog-comments">

		<?php

			// Success
			if (!empty($message)) {
				echo '<div class="msg positive"><p>' . $message . '</p></div>';
			}

			// Errors
			if (!empty($errors)) {
				echo '<div class="msg negative"><p>' . implode('</p><p>', $errors) . '</p></div>';
			}

		?>

		<?php if (!empty($count_comments['total']) && !empty($comments)) { ?>

			<h4><?=Format::number($count_comments['total']);?> <?=Format::plural($count_comments['total'], 'Responses', 'Response'); ?> to "<?=Format::htmlspecialchars($entry['title']); ?>"</h4>

			<?php foreach ($comments as $comment) { ?>

				<div class="comment">
					<?php if (!empty($comment['website'])) { ?>
						<strong><a href="<?=Format::htmlspecialchars($comment['website']); ?>" rel="external nofollow" target="_blank"><?=Format::htmlspecialchars($comment['name']); ?></a> wrote:</strong>
					<?php } else { ?>
						<strong><?=Format::htmlspecialchars($comment['name']); ?> wrote:</strong>
					<?php } ?>

					<?=nl2br(trim(Format::htmlspecialchars($comment['comment']), "\n ")); ?>
					<p><em>Posted on <?=date('l\, F jS\, Y \a\t g:ia', strtotime($comment['timestamp_created'])); ?>.</em></p>
				</div>

			<?php } ?>

		<?php } ?>

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

	<div class="msg negative">
		<p>The selected blog entry could not be found.</p>
	</div>

<?php } ?>