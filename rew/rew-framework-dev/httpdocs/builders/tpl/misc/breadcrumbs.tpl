<?php namespace BDX; ?>

<?php if (!empty($breadcrumbOptions) && is_array($breadcrumbOptions)) { ?>
	<div class="bdx-breadcrumbs">
		<ul>
			<li><a href="<?=Settings::getInstance()->SETTINGS['URL_BUILDERS'];?>/">All New Homes</a></li>
			<?php $last_key = end(array_keys($breadcrumbOptions)); ?>
			<?php foreach ($breadcrumbOptions as $key => $value) { ?>
				<?php if ($key == $last_key) { ?>
					<li><?=$value['Title'];?></li>
				<?php } else { ?>
					<li><a href="<?=$value['Link'];?>"><?=$value['Title'];?></a></li>
				<?php } ?>
			<?php } ?>
		</ul>
	</div>
<?php } ?>
