<div class="articleset blog" itemscope itemtype="http://schema.org/Blog">

	<?php foreach ($entries as $entry) { ?>

		<article itemprop="blogPosts" itemscope itemtype="http://schema.org/BlogPosting">

			<header>
				<h2 itemprop="name"><a itemprop="url" href="<?=sprintf(URL_BLOG_ENTRY, $entry['link']); ?>" title="<?=Format::htmlspecialchars($entry['title']); ?>"><?=Format::htmlspecialchars($entry['title']); ?></a></h2>
				<time itemprop="datePublished" datetime="<?=date('c', strtotime($entry['timestamp_published'])); ?>"><?=date('l\, F jS\, Y \a\t g:ia', strtotime($entry['timestamp_published'])); ?>.</time>
				<?php if (!empty($entry['author'])) { ?>
					<div class="author" itemprop="author" itemscope itemtype="http://schema.org/Person" class="auth pleft">
						<?php if (!empty($entry['author']['blog_picture'])) { ?>
							<a href="<?=$entry['author']['link']; ?>"><img src="<?=URL_BLOG_AUTHOR_THUMBS . $entry['author']['blog_picture']; ?>" border="0"></a><br>
						<?php } ?>
						<a href="<?=$entry['author']['link']; ?>" rel="author" class="url" itemprop="url"><span itemprop="name" class="fn"><?=Format::htmlspecialchars($entry['author']['name']); ?></span></a>
					</div>
				<?php } ?>
			</header>

		    <div class="body" itemprop="articleBody">
		    	<?=$entry['body']; ?>
		    </div>

		    <footer>
		        <?=Format::number($entry['views']) . ' ' . Format::plural($entry['views'], 'Views', 'View') . ', ' . Format::number($entry['comments']) . ' ' . Format::plural($entry['comments'], 'Comments', 'Comment'); ?>
		    </footer>

		    <div class="btnset">
		    	<a class="btn" href="<?=sprintf(URL_BLOG_ENTRY, $entry['link']); ?>" title="Read Full Post">Read Full Post &raquo;</a>
		    </div>

		</article>

	<?php } ?>

</div>