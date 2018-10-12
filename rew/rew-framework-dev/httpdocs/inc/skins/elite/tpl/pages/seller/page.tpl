<?php $this->includeFile('tpl/misc/header.tpl.php'); ?>

<?php if (!isset($_GET['popup'])) { ?>
    <?php if ($this->container('sub-feature')->countModules() > 0) { ?>
        <div id="sub-feature">
            <?= $this->container('sub-feature')->loadModules(); ?>
        </div>
    <?php } ?>
<?php } ?>

<div class="uk-container uk-container-center uk-margin-large-top uk-margin-large-bottom">
        <div class="uk-grid">
            <div class="uk-width-1-1">
            <?=$this->container('content')->loadModules(); ?>
            <?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
        </div>
    </div>
</div>

<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>
