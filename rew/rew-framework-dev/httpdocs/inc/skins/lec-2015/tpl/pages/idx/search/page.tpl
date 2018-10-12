<?php $this->includeFile('tpl/misc/header.tpl.php'); ?>

<?php if (!isset($_GET['popup'])) { ?>
	<?php if ($this->container('sub-feature')->countModules() > 0) { ?>
		<div id="sub-feature">
			<?=$this->container('sub-feature')->loadModules(); ?>
		</div>
	<?php } ?>
<?php } ?>

<div id="body">
	<div class="wrap">

        <?php if ($_GET['app'] === 'rt') { ?>
            <div class="rt-breadcrumbs">
                <?=$this->container('rt-breadcrumbs')->loadModules(); ?>
            </div>
        <?php } ?>

		<div id="content">
			<?=$this->container('content')->loadModules(); ?>
			<?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
		</div>
	</div>
</div>

<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>