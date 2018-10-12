<!DOCTYPE html>
<!--[if lt IE 7]>  <html lang="<?=Settings::getInstance()->LANG; ?>" class="ie ie6 lte9 lte8 lte7"> <![endif]-->
<!--[if IE 7]>     <html lang="<?=Settings::getInstance()->LANG; ?>" class="ie ie7 lte9 lte8 lte7"> <![endif]-->
<!--[if IE 8]>     <html lang="<?=Settings::getInstance()->LANG; ?>" class="ie ie8 lte9 lte8"> <![endif]-->
<!--[if IE 9]>     <html lang="<?=Settings::getInstance()->LANG; ?>" class="ie ie9 lte9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html lang="<?=Settings::getInstance()->LANG; ?>"><!--<![endif]-->
	<head>
		<?php $this->includeFile('tpl/header.tpl.php'); ?>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
		<?php $path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $this->getPath()); ?>
		<style type="text/css">
			@font-face {
				font-family:"Proxima N W15 Light";
				src:url("<?=$path; ?>/fnt/53f72e41-ffd4-47d4-b8bf-b1ab3cada2e5.eot?#iefix");
				src:url("<?=$path; ?>/fnt/53f72e41-ffd4-47d4-b8bf-b1ab3cada2e5.eot?#iefix") format("eot"),
					url("<?=$path; ?>/fnt/fb5639f2-f57b-487d-9610-3dc50820ab27.woff") format("woff"),
					url("<?=$path; ?>/fnt/2eafe9b7-5a21-49c0-84ca-54c54f899019.ttf") format("truetype"),
					url("<?=$path; ?>/fnt/0a2fe21c-cfdd-4f40-9dca-782e95c1fa90.svg#0a2fe21c-cfdd-4f40-9dca-782e95c1fa90") format("svg");
			}
			@font-face {
				font-family:"Proxima N W15 Reg";
				src:url("<?=$path; ?>/fnt/ccd538c8-85a6-4215-9f3f-643c415bbb19.eot?#iefix");
				src:url("<?=$path; ?>/fnt/ccd538c8-85a6-4215-9f3f-643c415bbb19.eot?#iefix") format("eot"),
					url("<?=$path; ?>/fnt/e8e438df-9715-40ed-b1ae-58760b01a3c0.woff") format("woff"),
					url("<?=$path; ?>/fnt/baf65064-a8a8-459d-96ad-d315581d5181.ttf") format("truetype"),
					url("<?=$path; ?>/fnt/76bd19c9-c46a-4c27-b80e-f8bd0ecd6057.svg#76bd19c9-c46a-4c27-b80e-f8bd0ecd6057") format("svg");
			}
			@font-face {
				font-family:"Proxima N W15 Bold";
				src:url("<?=$path; ?>/fnt/9682bb7d-efd6-4254-8771-e146c89a72d4.eot?#iefix");
				src:url("<?=$path; ?>/fnt/9682bb7d-efd6-4254-8771-e146c89a72d4.eot?#iefix") format("eot"),
					url("<?=$path; ?>/fnt/a3a867b8-141c-4865-9f8d-6dc5766a6bc5.woff") format("woff"),
					url("<?=$path; ?>/fnt/b9d6d5ca-ba9b-4fa1-a81e-366891676e4a.ttf") format("truetype"),
					url("<?=$path; ?>/fnt/844c48e5-7a2b-488b-9e47-ff8dda98e5e2.svg#844c48e5-7a2b-488b-9e47-ff8dda98e5e2") format("svg");
			}
			@font-face {
				font-family: 'icomoon';
				src:url('<?=$path; ?>/fnt/icomoon.eot');
				src:url('<?=$path; ?>/fnt/icomoon.eot?#iefix') format('embedded-opentype'),
					url('<?=$path; ?>/fnt/icomoon.woff') format('woff'),
					url('<?=$path; ?>/fnt/icomoon.ttf') format('truetype'),
					url('<?=$path; ?>/fnt/icomoon.svg#icomoon') format('svg');
				font-weight: normal;
				font-style: normal;
			}
		</style>
	</head>
	<body class="<?=$this->getPage()->info('class'); ?>">

		<div id="page">

			<?php if (!isset($_GET['popup'])) { ?>
				<header id="head">
					<div class="wrap">
						<?php /* <h3 id="logo"><a href="/"><?php rew_snippet('var-site-name', true, 1); ?></a></h3> */ ?>
						<a href="/" id="logo"><?=$this->getPage()->info('logoMarkupHeader'); ?></a>
						<?php if (Settings::getInstance()->SETTINGS['agent'] === 1) { ?>
							<nav class="horizontal">
								<h4><i class="icon-list"></i></h4>
								<?php rew_snippet('lec-navigation'); ?>
							</nav>
							<div id="phone"><?php rew_snippet('var-phone-number'); ?></div>
						<?php } else { ?>
							<nav class="horizontal">
								<h4><i class="icon-list"></i></h4>
								<?php rew_snippet('lec-nav'); ?>
							</nav>
						<?php } ?>
					</div>
				</header>
			<?php } ?>