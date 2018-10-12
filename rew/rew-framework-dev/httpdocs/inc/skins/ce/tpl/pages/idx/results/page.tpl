<?php $this->includeFile('tpl/misc/header.tpl.php'); ?>

<?php if ($this->container('quick-search')->countModules() > 0) { ?>
    <?=$this->container('quick-search')->loadModules(); ?>
<?php } ?>

<div id="body">
    <div id="content">
        <?=$this->container('content')->loadModules(); ?>
		<div class="container">
            <?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
		</div>
    </div>
</div>

<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>