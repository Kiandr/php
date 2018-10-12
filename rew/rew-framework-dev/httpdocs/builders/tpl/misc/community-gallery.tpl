<div class="bdx-slideshow">
	<div class="flexslider">
		<ul class="slides">
			<?php if (!empty($community['Images']) && is_array($community['Images'])) { ?>
				<?php for ($i = 0;  $i < count($community['Images']); $i++) { ?>
					<?php $alt = "";
					if($i == 0) { 
						$alt = $community['Alt'];
					} elseif($i == 1 || $i == 2) { 
						$alt = $community['Alt' . $i];
					} ?>
					<li><img alt="<?=$alt;?>" data-src="<?=$community['Images'][$i];?>" src="/builders/res/img/35mm_landscape.gif"></li>
				<?php } ?>
			<?php } else { ?>
				<li><img data-src="/builders/res/img/no-image.gif" src="/builders/res/img/35mm_landscape.gif"></li>
			<?php } ?>
		</ul>
	</div>
</div>
