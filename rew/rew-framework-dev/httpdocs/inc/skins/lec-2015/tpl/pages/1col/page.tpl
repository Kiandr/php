<?php $this->includeFile('tpl/misc/header.tpl.php'); ?>
<?php $background = $this->variable('background'); ?>
<?php $heading = $this->variable('heading'); ?>
<div id="body">

	<?php if ($background || $heading) { ?>
		<div class="hero">
			<?php if ($background) { ?>
				<img class="defer" data-src="<?=Format::htmlspecialchars($background); ?>" alt="">
			<?php } ?>
			<div class="wrap">
				<?php if ($heading) { ?>
					<h1><?=Format::htmlspecialchars($heading); ?></h1>
				<?php } else { ?>
					<span class="fill">&nbsp;</span>
				<?php } ?>
			</div>
		</div>
	<?php } ?>

	<?php if (!isset($_GET['popup'])) { ?>
		<?php if ($this->container('sub-feature')->countModules() > 0) { ?>
			<div id="sub-feature">
				<?=$this->container('sub-feature')->loadModules(); ?>
			</div>
		<?php } ?>
	<?php } ?>

	<div class="wrap">
        <?=$this->container('pre-content')->loadModules(); ?>
		<div id="content">
			<?=$this->container('content')->loadModules(); ?>
			<?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
		</div>
	</div>

</div>

<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>