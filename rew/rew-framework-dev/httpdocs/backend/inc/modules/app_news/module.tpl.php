<?php if (!empty($news)) : ?>
<h1><?= __('News'); ?></h1>
<div id="rew-news">
	<ul>
	<?php foreach ($news as $item) : ?>
		<li>
		<span class="news_sprites news_icon_<?=$item['level'];?>"></span>
		<h2 class="<?=$item['level'];?>"><?=$item['title'];?></h2>
		<p><?=$item['message'];?></p>
		<?php if (!empty($item['link'])) :?>
		<a href="<?=$item['link'];?>" target="_blank"><?= __('Read More'); ?></a>
		<?php endif; ?>
		</li>
	<?php endforeach; ?>
	</ul>
</div>
<?php endif; ?>
