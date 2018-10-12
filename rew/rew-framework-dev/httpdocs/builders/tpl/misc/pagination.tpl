<?php if (!empty($pagination['links'])) { ?>
	<div class="pagination<?=(!empty($pagination['extra']) ? ' ' . $pagination['extra'] : '');?>">
	<?php if (!empty($pagination['prev'])) { ?>
		<a class="prev" rel="prev" href="<?=$pagination['prev']['url'];?>">&#171;</a>
	<?php } ?>
	<?php if (!empty($pagination['links'])) { ?>
		<?php foreach ($pagination['links'] as $link) { ?>
			<?php if (!empty($link['active'])) { ?>
				<a class="current" href="<?=$link['url'];?>"><?=$link['link'];?></a>
			<?php } else { ?>
				<a href="<?=$link['url'];?>"><?=$link['link'];?></a>
			<?php }
		}
	}
	if (!empty($pagination['next'])) { ?>
		<a class="next" rel="next" href="<?=$pagination['next']['url'];?>">&#187;</a>
	<?php } ?>
	</div>
<?php } ?>