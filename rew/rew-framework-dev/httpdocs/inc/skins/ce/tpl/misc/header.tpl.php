<!DOCTYPE html>
<!--[if lt IE 7]>  <html lang="<?=Settings::getInstance()->LANG; ?>" class="ie ie6 lte9 lte8 lte7"> <![endif]-->
<!--[if IE 7]>     <html lang="<?=Settings::getInstance()->LANG; ?>" class="ie ie7 lte9 lte8 lte7"> <![endif]-->
<!--[if IE 8]>     <html lang="<?=Settings::getInstance()->LANG; ?>" class="ie ie8 lte9 lte8"> <![endif]-->
<!--[if IE 9]>     <html lang="<?=Settings::getInstance()->LANG; ?>" class="ie ie9 lte9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html lang="<?=Settings::getInstance()->LANG; ?>"><!--<![endif]-->
<head>
    <?php $this->includeFile('tpl/header.tpl.php'); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
    <meta name="format-detection" content="telephone=no"/>
    <script defer src="<?=$this->getUrl(); ?>/node_modules/svgxuse/svgxuse.min.js"></script>
</head>
<body class="<?=$this->getPage()->info('class'); ?>">
    <div id="page">
    <?php if (!isset($_GET['popup'])) { ?>
    <header id="head" class="head ">

        <div class="container">
            <?=$this->getPage()->info('logoMarkupHeader'); ?>

			<a id="nav-toggle" class="nav__toggle -right">
                <svg class="nav__icon" xmlns="http://www.w3.org/2000/svg" width="25" height="21" viewBox="0 0 16 16">
                    <path d="M14.019,15.017h15.99V16.98H14.019V15.017Zm0,6h15.99V22.98H14.019V21.017Zm0,6h15.99V28.98H14.019V27.017Z" transform="translate(-14.031 -15.031)"/>
                </svg>
            </a>

            <nav id="nav" class="nav inactive -right clr">
				<div class="head-contact">
					<div class="container">
						<div class="phone">
							<?php rew_snippet('site-phone-number'); ?>
						</div>
						<div class="social">
							<?php rew_snippet('social-links'); ?>
						</div>
					</div>
				</div>
				<a id="nav-close" class="nav__close">
                    <svg class="close__icon">
						<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/inc/skins/ce/img/assets.svg#icon--close"></use>
					</svg>
                </a>

                <?php rew_snippet('site-navigation'); ?>
                <?=$this->container('user-links')->loadModules(); ?>

			</nav>
        </div>
    </header>
    <?php } ?>