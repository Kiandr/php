<?php $this->includeFile('tpl/misc/header.tpl.php'); ?>

<?php if ($this->container('quick-search')->countModules() > 0) { ?>
    <?=$this->container('quick-search')->loadModules(); ?>
<?php } ?>

<div id="body">
    <div class="container">
        <div id="content" class="-pad-vertical-lg">
            <?=$this->container('content')->loadModules(); ?>
            <?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
        </div>
    </div>
</div>

<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>