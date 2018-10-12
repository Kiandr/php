<?php $this->includeFile('tpl/misc/header.tpl.php'); ?>

	<?=$this->container('content')->loadModules(); ?>
	<?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>

<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>