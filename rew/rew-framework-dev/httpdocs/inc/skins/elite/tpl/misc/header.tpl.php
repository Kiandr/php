<!DOCTYPE html>
<!--[if lt IE 7]>
<html lang="<?=Settings::getInstance()->LANG; ?>" class="ie ie6 lte9 lte8 lte7"> <![endif]-->
<!--[if IE 7]>
<html lang="<?=Settings::getInstance()->LANG; ?>" class="ie ie7 lte9 lte8 lte7"> <![endif]-->
<!--[if IE 8]>
<html lang="<?=Settings::getInstance()->LANG; ?>" class="ie ie8 lte9 lte8"> <![endif]-->
<!--[if IE 9]>
<html lang="<?=Settings::getInstance()->LANG; ?>" class="ie ie9 lte9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html lang="<?= Settings::getInstance()->LANG; ?>"><!--<![endif]-->
<head>
    <?php $this->includeFile('tpl/header.tpl.php'); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="format-detection" content="telephone=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!--[if lte IE 8]>
    <script src="<?=$this->getUrl(); ?>/js/lib/respond-1.4.2.js"></script><![endif]-->
</head>
<body class="<?= $this->getPage()->info('class'); ?>">
<?php if (isset($_GET['popup']) || isset($_GET['iframe'])) return; ?>
<div class="content">
    <?php if (!isset($_GET['popup'])) { ?>
        <?= $this->container('sm-slideout')->loadModules(); ?>
    <?php } ?>
    <header class="site-header">
        <div class="fw fw-header uk-position-relative uk-position-z-index" id="mast-wrap">
            <div class="uk-grid">
                <div class="uk-width-large-2-10 uk-width-medium-2-10 uk-width-small-7-10 uk-width-7-10">
                    <a href="/" class="logo">
                        <?=$this->getPage()->info('logoMarkupHeader'); ?>
                    </a>
                </div>
                <div class="uk-width-small-3-10 uk-width-3-10 uk-visible-small">
                    <div class="header-login">
                        <?php if (User_Session::get()->isValid()) { ?>
                            <a class="login" data-modal="logout" href="<?= Format::htmlspecialchars(Settings::getInstance()->SETTINGS['URL_IDX_LOGOUT']); ?>"><i class="uk-icon-sign-out"></i></a>
                        <?php } else { ?>
                            <a class="login" data-modal="login" href="<?= Format::htmlspecialchars(Settings::getInstance()->SETTINGS['URL_IDX_LOGIN']); ?>"><i class="uk-icon-user"></i></a>
                        <?php } ?>
                        <a class="menu" href="#sm-slideout" data-uk-offcanvas><i class="uk-icon-cog"></i></a>
                    </div><!-- /.login -->
                </div>
                <div class="uk-width-large-7-10 uk-width-medium-7-10 uk-width-small-1-1 uk-width-1-1">
                    <?php $this->container('quicksearch')->loadModules(); ?>
                </div>
                <div class="uk-width-large-1-10 uk-width-medium-1-10 uk-hidden-small">
                    <div class="header-login">
                        <?php if (User_Session::get()->isValid()) { ?>
                            <a href="<?= Format::htmlspecialchars(Settings::getInstance()->SETTINGS['URL_IDX_LOGOUT']); ?>" class="login" data-modal="logout"><i class="uk-icon-sign-out"></i><span class="uk-visible-large">Logout</span></a>
                        <?php } else { ?>
                            <a href="<?= Format::htmlspecialchars(Settings::getInstance()->SETTINGS['URL_IDX_LOGIN']); ?>" class="login" data-modal="login"><i class="uk-icon-user"></i><span class="uk-visible-large">Login</span></a>
                        <?php } ?>
                        <a href="#sm-slideout" data-uk-offcanvas class="menu"><i class="uk-icon-cog"></i></a>
                    </div><!-- /.login -->
                </div>
            </div><!-- /.uk-grid /.uk-grid-small -->
        </div><!-- /.fw /.fw-header -->

        <div class="uk-grid mobile-advanced-search-bar uk-hidden-large">
            <div class="uk-width-1-1">
                <a class="search-filter js-advanced-search-trigger mobile">
                    ADVANCED SEARCH <i class="uk-icon uk-icon-angle-up"></i>
                </a>
            </div>
        </div>
        <div class="fw-header-nav uk-clearfix">
            <div class="header-nav-menu uk-visible-small uk-float-left">
                <a href="#off-canvas" data-uk-offcanvas><i class="uk-icon-bars"></i></a>
            </div>
            <div id="off-canvas" class="uk-offcanvas">
                <div class="uk-offcanvas-bar"><?php rew_snippet('mobile-navigation'); ?></div>
            </div>
            <div class="header-phone uk-float-right">
                <?php rew_snippet('phone-number'); ?>
            </div>
            <nav class="uk-hidden-small">
                <?php rew_snippet('navigation'); ?>
            </nav>
        </div><!-- /.fw /.fw-header-nav -->
    </header>
</div>

<?php if (!isset($_GET['popup'])) { ?>
    <?php $this->container('quicksearch-advanced')->loadModules(); ?>
<?php } ?>
