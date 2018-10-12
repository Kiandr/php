<?php

// Require Module Turned On...
if (empty(Settings::getInstance()->MODULES['REW_BLOG_INSTALLED'])) return;

// Display Title
if (!empty($title)) echo '<h2>' . $title . '</h2>';

?>
<div class="module articleset posts">
	<?php foreach ($items as $item) { ?>
		<article>
			<header>
				<h4><a href="<?=$item['link']; ?>"<?=$target; ?> title="<?=$item['title']; ?>"><?=$item['title']; ?></a></h4>
				<em><time itemprop="datePublished" datetime="<?=date('c', strtotime($item['pubDate'])); ?>"><?=date('l\, F jS\, Y \a\t g:ia', strtotime($item['pubDate'])); ?></time></em>
			</header>
		    <p><?=$item['description']; ?> <a href="<?=$item['link']; ?>" <?=!empty($target) ? 'target="' . $target . '"' : ''; ?> title="Read More">Read More</a></p>
		</article>
	<?php } ?>
</div>