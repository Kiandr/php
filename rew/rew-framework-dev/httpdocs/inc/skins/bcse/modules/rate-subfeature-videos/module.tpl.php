<?php if (!empty($videos)) { ?>
 	<div id="<?=$this->getUID(); ?>">
		<h2><?=Format::htmlspecialchars($this->config('heading')); ?></h2>
		<h3 class="small-caps"><?=Format::htmlspecialchars($this->config('subheading')); ?></h3>
		<?php if ($this->config('linkUrl') && $this->config('linkText')) { ?>
			<a class="buttonstyle absolute-right view-communities-btn small-caps" href="<?=$this->config('linkUrl'); ?>">
				<?=Format::htmlspecialchars($this->config('linkText')); ?>
			</a>
		<?php } ?>
		<div class="articleset">
			<?php foreach ($videos as $video) { ?>
				<article>
					<a data-video-id="<?=$video['id']; ?>" data-video-title="<?=Format::stripTags($video['title']); ?>" data-video-type="<?=$video['type']; ?>">
						<?php if ($video['type'] == 'youtube') { ?>
							<img data-src="https://img.youtube.com/vi/<?=$video['id']; ?>/0.jpg">
						<?php } else { ?>
							<img data-src="<?=$video['url']; ?>">
						<?php } ?>
					</a>
					<p class="video-description"><?=Format::stripTags($video['title'], '<strong><b><em><i><u><a>'); ?></p>
				</article>
			<?php } ?>
		</div>
	</div>
<?php } ?>