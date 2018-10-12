<?php $this->includeFile('tpl/misc/header.tpl.php'); ?>

<?php $this->container('feature')->loadModules(); ?>

<div id="body">
	<div class="wrap">

		<?php $this->container('body')->loadModules(); ?>

		<div id="content">
			<?php $this->container('content')->loadModules(); ?>
			<?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
		</div>

		<aside id="sidebar">
			<?=$this->container('sidebar')->loadModules(); ?>
		</aside>

	</div>
</div>

<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>