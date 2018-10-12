<div id="gallery">
	<div class="gallery">
		<div class="slideset">
	        <?php foreach ($images as $count => $photo) { ?>
	        	<div class="slide" data-slide="<?=$count; ?>"><img src="/img/util/35mm_landscape.gif" data-src="<?=$photo; ?>" alt=""></div>
	        <?php } ?>
	        <img src="/img/util/dig_landscape.gif" class="ph">
		</div>
		<?php if (!empty($this->config['compliance'])) { ?>
			<div class="compliance hidden">
				<?=$this->config['compliance'];?>
			</div>
		<?php } ?>
		<?php if (count($images) > 1) { ?>
		    <a class="prev" href="javascript:void(0);"><i class="icon-chevron-left"></i></a>
		    <a class="next" href="javascript:void(0);"><i class="icon-chevron-right"></i></a>
		<?php } ?>
	</div>
	<?php if (count($images) > 0) { ?>
		<div class="carousel hidden">
			<div class="slideset">
		        <?php foreach ($images as $count => $photo) { ?>
		        	<div class="slide" data-slide="<?=$count; ?>">
		        		<a>
		        			<img src="/thumbs/200x200/img/util/35mm_landscape.gif" data-src="<?=IDX_Feed::thumbUrl($photo, IDX_Feed::IMAGE_SIZE_SMALL); ?>" alt="">
		        		</a>
		        	</div>
		        <?php } ?>
			</div>
		    <a class="prev" href="javascript:void(0);"><i class="icon-chevron-left"></i></a>
		    <a class="next" href="javascript:void(0);"><i class="icon-chevron-right"></i></a>
		</div>
		<div class="btnset mini hidden-phone">
			<?php

				// Enlarge Photos
				if (!empty($enlarge)) {
					echo '<a class="btn all" href="javascript:void(0);">All Photos</a>';
				}


				// Extra Links
				if (!empty($links)) {
					foreach ($links as $link) {
						echo '<a'
	        				. (!empty($link['class'])	? ' class="' . $link['class'] . '"'		: ' class="btn"')
	        				. (!empty($link['href'])	? ' href="' . $link['href'] . '"'		: ' href="#"')
	        				. (!empty($link['target'])	? ' target="' . $link['target'] . '"'	: '')
        				. '>' . $link['text'] . '</a>' . PHP_EOL;
					}
				}

			?>
		</div>
	<?php } ?>
</div>