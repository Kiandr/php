<!DOCTYPE html>
<!--[if lt IE 7]>  <html lang="<?=Settings::getInstance()->LANG; ?>" class="ie ie6 lte9 lte8 lte7"> <![endif]-->
<!--[if IE 7]>     <html lang="<?=Settings::getInstance()->LANG; ?>" class="ie ie7 lte9 lte8 lte7"> <![endif]-->
<!--[if IE 8]>     <html lang="<?=Settings::getInstance()->LANG; ?>" class="ie ie8 lte9 lte8"> <![endif]-->
<!--[if IE 9]>     <html lang="<?=Settings::getInstance()->LANG; ?>" class="ie ie9 lte9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html lang="<?=Settings::getInstance()->LANG; ?>"><!--<![endif]-->
	<head>
		<?php $this->includeFile('tpl/header.tpl.php'); ?>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
		<!--[if lte IE 8]><script src="<?=$this->getUrl(); ?>/js/lib/respond-1.4.2.js"></script><![endif]-->
	</head>
	<body class="<?=$this->getPage()->info('class'); ?>">
		<div id="page">

			<?php if (!isset($_GET['popup'])) { ?>
				<?=$this->container('sm-slideout')->loadModules(); ?>
				<div id="mast-wrap">
					<header id="head">
						<div class="top-bar">
							<div class="wrap">
								<a href="/" id="logo"><?=$this->getPage()->info('logoMarkupHeader'); ?></a>
								<div class="phone">
									<?php rew_snippet('phone-number'); ?>
								</div>
								<nav id="primary_nav">
									<h4 class="hamburger"><i class="icon-list"></i></h4>
									<div class="hidden-small hidden-medium">
										<?php rew_snippet('navigation'); ?>
									</div>
								</nav>
							</div>
						</div>
					</header>
				</div>
			<?php } ?>