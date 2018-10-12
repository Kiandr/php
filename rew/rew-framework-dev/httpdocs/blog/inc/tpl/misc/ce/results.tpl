<div class="articleset blog -pad-vertical -pad-right-lg" itemscope itemtype="http://schema.org/Blog">
    <?php foreach ($entries as $entry) { ?>
        <article class="article  -mar-bottom-lg -mar-bottom-xxl" itemprop="blogPosts" itemscope itemtype="http://schema.org/BlogPosting">
            <header class="article__head -pad-bottom">
                <h1 itemprop="name"><a itemprop="url" href="<?=sprintf(URL_BLOG_ENTRY, $entry['link']); ?>"><?=Format::htmlspecialchars($entry['title']); ?></a></h1>
                <time itemprop="datePublished" datetime="<?=date('c', strtotime($entry['timestamp_published'])); ?>"><?=date('l\, F jS\, Y \a\t g:ia', strtotime($entry['timestamp_published'])); ?>.</time>
                <?php if (!empty($entry['author'])) { ?>
                    <div class="author" itemprop="author" itemscope itemtype="http://schema.org/Person" class="auth pleft">
                        <?php if (!empty($entry['author']['blog_picture'])) { ?>
                            <a href="<?=$entry['author']['link']; ?>"><img data-src="<?=URL_BLOG_AUTHOR_THUMBS . $entry['author']['blog_picture']; ?>" border="0"></a><br>
                        <?php } ?>
                        <a href="<?=$entry['author']['link']; ?>" rel="author" class="url" itemprop="url"><span itemprop="name" class="fn"><?=Format::htmlspecialchars($entry['author']['name']); ?></span></a>
                    </div>
                <?php } ?>
            </header>
            <div class="article__body" itemprop="articleBody">
                <?=$entry['body']; ?>
            </div>
            <footer class="article__foot">
                <?=Format::number($entry['views']) . ' ' . Format::plural($entry['views'], __('Views'), __('View')) . ', ' . Format::number($entry['comments']) . ' ' . Format::plural($entry['comments'], __('Comments'), __('Comment')); ?>
            </footer>
            <div class="buttons -pad-top-lg">
                <a class="button" href="<?=sprintf(URL_BLOG_ENTRY, $entry['link']); ?>"><?= __('Read Full Post'); ?> &raquo;</a>
            </div>
        </article>
    <?php } ?>
</div>
