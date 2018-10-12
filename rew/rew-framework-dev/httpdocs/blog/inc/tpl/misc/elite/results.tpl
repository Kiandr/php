<div itemscope itemtype="http://schema.org/Blog">

    <?php foreach ($entries as $entry) { ?>

        <article class="uk-article" itemprop="blogPosts" itemscope itemtype="http://schema.org/BlogPosting">

            <header>
                <h2 class="uk-article-title" itemprop="name"><a itemprop="url" href="<?= Format::htmlspecialchars(sprintf(URL_BLOG_ENTRY, $entry['link'])); ?>" title="<?= Format::htmlspecialchars($entry['title']); ?>"><?= Format::htmlspecialchars($entry['title']); ?></a></h2>

                <time class="uk-article-meta" itemprop="datePublished" datetime="<?= date('c', strtotime($entry['timestamp_published'])); ?>"><?= date('l\, F jS\, Y \a\t g:ia', strtotime($entry['timestamp_published'])); ?>.</time>

                <?php if (!empty($entry['author'])) { ?>
                    <div class="uk-hidden uk-article-meta" itemprop="author" itemscope itemtype="http://schema.org/Person">
                        <?php if (!empty($entry['author']['blog_picture'])) { ?>
                            <a href="<?= Format::htmlspecialchars($entry['author']['link']); ?>"><img class="uk-border-circle" src="<?= Format::htmlspecialchars(URL_BLOG_AUTHOR_THUMBS . $entry['author']['blog_picture']); ?>" border="0"></a><br>
                        <?php } ?>
                        <a href="<?= Format::htmlspecialchars($entry['author']['link']); ?>" rel="author" class="url" itemprop="url"><span itemprop="name" class="fn"><?= Format::htmlspecialchars($entry['author']['name']); ?></span></a>
                    </div>
                <?php } ?>

            </header>

            <div itemprop="articleBody">
                <?=$entry['body']; ?>
            </div>

            <a class="uk-button uk-margin-bottom" href="<?=sprintf(URL_BLOG_ENTRY, $entry['link']); ?>" title="Read Full Post">Read Full Post &raquo;</a>

                <?php if (!empty($entry['author'])) { ?>
                    <article class="uk-comment-list">
                        <?php if (!empty($entry['author']['blog_picture'])) { ?>
                            <footer class="uk-comment-header">
                                <a href="<?=$entry['author']['link']; ?>"><img class="uk-comment-avatar uk-border-circle" src="<?=URL_BLOG_AUTHOR_THUMBS . $entry['author']['blog_picture']; ?>" border="0"></a>
                                <h4 class="uk-comment-title">
                                    <a href="<?=$entry['author']['link']; ?>" rel="author" class="url" itemprop="url"><span itemprop="name" class="fn"><?=Format::htmlspecialchars($entry['author']['name']); ?></span></a>
                                </h4>
                                <div class="uk-comment-meta"><?=Format::number($entry['views']) . ' ' . Format::plural($entry['views'], 'Views', 'View') . ', ' . Format::number($entry['comments']) . ' ' . Format::plural($entry['comments'], 'Comments', 'Comment'); ?></div>

                            </footer>
                        <?php } else { ?>

                        <?php } ?>
                    </article>
                <?php } ?>

        </article>
        <hr class="uk-article-divider">

    <?php } ?>

</div>
