<?php
    $this->includeFile('tpl/misc/header.tpl.php');

    if (!empty($_REQUEST['snippet'])) {
        $hideMap = true;
        if (Settings::getInstance()->MODULES['REW_IDX_MAPPING']) {
            require Page::locateTemplate('idx', 'misc', 'map-container');
        }
    }

?>
<div class="fw fw-idx-listings">
    <div class="uk-container uk-container-center">
        <?= $this->container('content')->loadModules(); ?>
        <?php \Container::getInstance()->get(\REW\Core\Interfaces\IDX\ComplianceInterface::class)->showDisclaimer(); ?>
    </div>
</div>

<a href="#" id="rew-back2top" class="uk-icon-chevron-up" data-uk-smooth-scroll></a>
<?php $this->includeFile('tpl/misc/footer.tpl.php'); ?>
