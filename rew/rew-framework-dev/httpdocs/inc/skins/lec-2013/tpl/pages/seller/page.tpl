<?php $this->includeFile('tpl/misc/header.tpl.php'); ?>

<div id="body">
	<div class="wrap">
		<?=$this->container('body')->loadModules(); ?>
		<div id="content">
			<?=$this->container('content')->loadModules(); ?>
			<?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
		</div>

	</div>
</div>

<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>