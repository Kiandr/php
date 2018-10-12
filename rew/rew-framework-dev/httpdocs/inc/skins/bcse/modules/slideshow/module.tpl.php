<div id="<?=$this->getUID(); ?>">
	<?php foreach ($slideshow as $i => $image) { ?>
		<?php if (!empty($image['link'])) { ?>
			<a class="slide<?=($i === 0 ? ' active' : ''); ?>" href="<?=$image['link']; ?>" style="background-image: url('<?=$image['image']; ?>'); filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=$image['image']; ?>', sizingMethod='scale');"></a>
		<?php } else {?>
			<div class="slide<?=($i === 0 ? ' active' : ''); ?>" style="background-image: url('<?=$image['image']; ?>'); filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='<?=$image['image']; ?>', sizingMethod='scale');"></div>
		<?php } ?>
	<?php } ?>
</div>