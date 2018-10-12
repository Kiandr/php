<?php $this->includeFile('tpl/misc/header.tpl.php'); ?>

<div id="body">
	<div class="wrap">
		<?=$this->container('body')->loadModules(); ?>
		<?=$this->container('pre-content')->loadModules(); ?>
		<div id="content">

			<div id="content-primary">
				<?=$this->container('content')->loadModules(); ?>
			</div>

			<div id="content-secondary">
				<?php $this->container('secondary')->loadModules(); ?>
			</div>

			<?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>

		</div>

		<?php if ($this->container('sidebar')->countModules() > 0) { ?>
			<aside id="sidebar">
				<?=$this->container('sidebar')->loadModules(); ?>
			</aside>
		<?php } ?>

	</div>
</div>

<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>