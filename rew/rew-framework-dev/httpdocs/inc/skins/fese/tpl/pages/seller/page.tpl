<?php $this->includeFile('tpl/misc/header.tpl.php'); ?>

<?php if ($this->container('quick-search')->countModules() > 0) { ?>
    <div class="block wrp S4">
        <div class="w1/1">
            <?=$this->container('quick-search')->loadModules(); ?>
        </div>
    </div>
<?php } ?>

<div id="body">
    <div id="content">
        <div class="wrp<?=(isset($_GET['popup']) ? '' : ' S4'); ?>">
            <?=$this->container('content')->loadModules(); ?>
            <?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
        </div>
    </div>
</div>

<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>