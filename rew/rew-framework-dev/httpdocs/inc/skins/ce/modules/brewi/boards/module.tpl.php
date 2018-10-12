<?php if(!empty($boards) && is_array($boards)) { ?>

<div class="columns" id="<?=$this->getUID();?>">

	<?php foreach($boards as $board) { ?>
	<a href="<?=$board['url'];?>" class="hero hero--portrait column -width-1/4 -width-1/2@md -width-1/1@sm -width-1/1@xs">
		<div class="hero__fg">
    		<div class="hero__head">
        		<div class="divider">
        			<span class="divider__label -left -text-upper -text-xs"><?=$board['title'];?></span>
        		</div>
    		</div>
    		<div class="hero__body -flex">
        		<div class="-bottom">
            		<h2 class="-text-upper"><?=$board['heading'];?></h2>
    				<?php if($board['description']) { ?>
    				<p class="-text-xs -text-upper"><?=$board['description'];?></p>
    				<?php } ?>
        		</div>
		    </div>
		</div>
		<div class="hero__bg">
			<div class="cloak cloak--dusk"></div>
		    <?php if(!empty($board['image'])) { ?>
		    <img class="hero__bg-content" data-src="<?=$board['image'];?>" alt="">
			<?php } ?>
		</div>
	</a>
	<?php } ?>

</div>

<?php } ?>
