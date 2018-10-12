<!DOCTYPE html>
<!--[if lt IE 7]>  <html lang="<?=Settings::getInstance()->LANG; ?>" class="ie ie6 lte9 lte8 lte7"> <![endif]-->
<!--[if IE 7]>     <html lang="<?=Settings::getInstance()->LANG; ?>" class="ie ie7 lte9 lte8 lte7"> <![endif]-->
<!--[if IE 8]>     <html lang="<?=Settings::getInstance()->LANG; ?>" class="ie ie8 lte9 lte8"> <![endif]-->
<!--[if IE 9]>     <html lang="<?=Settings::getInstance()->LANG; ?>" class="ie ie9 lte9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html lang="<?=Settings::getInstance()->LANG; ?>"><!--<![endif]-->
<head>
    <?php $this->includeFile('tpl/header.tpl.php'); ?>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <style>
        @font-face {
            font-family: 'Pathway Gothic One';
            font-style: normal;
            font-weight: 400;
            src: url('<?=$this->getUrl(); ?>/fnt/pathway-gothic-one/pathway-gothic-one-v4-latin-ext_latin-regular.eot'); /* IE9 Compat Modes */
            src: local('<?=$this->getUrl(); ?>/fnt/pathway-gothic-one/Pathway Gothic One'), local('<?=$this->getUrl(); ?>/fnt/pathway-gothic-one/PathwayGothicOne-Regular'),
            url('<?=$this->getUrl(); ?>/fnt/pathway-gothic-one/pathway-gothic-one-v4-latin-ext_latin-regular.eot?#iefix') format('embedded-opentype'), /* IE6-IE8 */
            url('<?=$this->getUrl(); ?>/fnt/pathway-gothic-one/pathway-gothic-one-v4-latin-ext_latin-regular.woff2') format('woff2'), /* Super Modern Browsers */
            url('<?=$this->getUrl(); ?>/fnt/pathway-gothic-one/pathway-gothic-one-v4-latin-ext_latin-regular.woff') format('woff'), /* Modern Browsers */
            url('<?=$this->getUrl(); ?>/fnt/pathway-gothic-one/pathway-gothic-one-v4-latin-ext_latin-regular.ttf') format('truetype'), /* Safari, Android, iOS */
            url('<?=$this->getUrl(); ?>/fnt/pathway-gothic-one/pathway-gothic-one-v4-latin-ext_latin-regular.svg#PathwayGothicOne') format('svg'); /* Legacy iOS */
        }
        @font-face {
            font-family: 'Overpass';
            src: url('<?=$this->getUrl(); ?>/fnt/overpass/Overpass-Italic.eot'); /* IE9 Compat Modes */
            src: url('<?=$this->getUrl(); ?>/fnt/overpass/Overpass-Italic.eot?#iefix') format('embedded-opentype'), /* IE6-IE8 */
            url('<?=$this->getUrl(); ?>/fnt/overpass/Overpass-Italic.woff') format('woff'), /* Modern Browsers */
            url('<?=$this->getUrl(); ?>/fnt/overpass/Overpass-Italic.ttf') format('truetype'), /* Safari, Android, iOS */
            url('<?=$this->getUrl(); ?>/fnt/overpass/Overpass-Italic.svg#Overpass-Italic') format('svg'); /* Legacy iOS */
            font-style: italic;
            font-weight: 600;
            text-rendering: optimizeLegibility;
        }
        @font-face {
            font-family: 'Overpass';
            src: url('<?=$this->getUrl(); ?>/fnt/overpass/Overpass-Light.eot'); /* IE9 Compat Modes */
            src: url('<?=$this->getUrl(); ?>/fnt/overpass/Overpass-Light.eot?#iefix') format('embedded-opentype'), /* IE6-IE8 */
            url('<?=$this->getUrl(); ?>/fnt/overpass/Overpass-Light.woff') format('woff'), /* Modern Browsers */
            url('<?=$this->getUrl(); ?>/fnt/overpass/Overpass-Light.ttf') format('truetype'), /* Safari, Android, iOS */
            url('<?=$this->getUrl(); ?>/fnt/overpass/Overpass-Light.svg#Overpass-Light') format('svg'); /* Legacy iOS */
            font-style: normal;
            font-weight: normal;
            text-rendering: optimizeLegibility;
        }
        @font-face {
            font-family: 'Overpass';
            src: url('<?=$this->getUrl(); ?>/fnt/overpass/Overpass-Light-Italic.eot'); /* IE9 Compat Modes */
            src: url('<?=$this->getUrl(); ?>/fnt/overpass/Overpass-Light-Italic.eot?#iefix') format('embedded-opentype'), /* IE6-IE8 */
            url('<?=$this->getUrl(); ?>/fnt/overpass/Overpass-Light-Italic.woff') format('woff'), /* Modern Browsers */
            url('<?=$this->getUrl(); ?>/fnt/overpass/Overpass-Light-Italic.ttf') format('truetype'), /* Safari, Android, iOS */
            url('<?=$this->getUrl(); ?>/fnt/overpass/Overpass-Light-Italic.svg#Overpass-LightItalic') format('svg'); /* Legacy iOS */
            font-style: italic;
            font-weight: normal;
            text-rendering: optimizeLegibility;
        }
        @font-face {
            font-family: 'Overpass';
            src: url('<?=$this->getUrl(); ?>/fnt/overpass/Overpass-ExtraLight.eot'); /* IE9 Compat Modes */
            src: url('<?=$this->getUrl(); ?>/fnt/overpass/Overpass-ExtraLight.eot?#iefix') format('embedded-opentype'), /* IE6-IE8 */
            url('<?=$this->getUrl(); ?>/fnt/overpass/Overpass-ExtraLight.woff') format('woff'), /* Modern Browsers */
            url('<?=$this->getUrl(); ?>/fnt/overpass/Overpass-ExtraLight.ttf') format('truetype'), /* Safari, Android, iOS */
            url('<?=$this->getUrl(); ?>/fnt/overpass/Overpass-ExtraLight.svg#Overpass-ExtraLight') format('svg'); /* Legacy iOS */
            font-style: normal;
            font-weight: 300;
            text-rendering: optimizeLegibility;
        }
        @font-face {
            font-family: 'Overpass';
            src: url('<?=$this->getUrl(); ?>/fnt/overpass/Overpass-ExtraLight-Italic.eot'); /* IE9 Compat Modes */
            src: url('<?=$this->getUrl(); ?>/fnt/overpass/Overpass-ExtraLight-Italic.eot?#iefix') format('embedded-opentype'), /* IE6-IE8 */
            url('<?=$this->getUrl(); ?>/fnt/overpass/Overpass-ExtraLight-Italic.woff') format('woff'), /* Modern Browsers */
            url('<?=$this->getUrl(); ?>/fnt/overpass/Overpass-ExtraLight-Italic.ttf') format('truetype'), /* Safari, Android, iOS */
            url('<?=$this->getUrl(); ?>/fnt/overpass/Overpass-ExtraLight-Italic.svg#Overpass-ExtraLightItalic') format('svg'); /* Legacy iOS */
            font-style: italic;
            font-weight: 300;
            text-rendering: optimizeLegibility;
        }
    </style>
</head>
<body class="<?=$this->getPage()->info('class'); ?>">
    <div id="page">
    <?php if (!isset($_GET['popup'])) { ?>
        <header id="head" class="head">
            <div class="wrp S4">
                <div class="logo-link">
                    <?=$this->getPage()->info('logoMarkupHeader'); ?>
                </div>
                <a id="nav-toggle" class="mnu-trigger R marL">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="21" viewBox="0 0 16 16">
                        <path d="M14.019,15.017h15.99V16.98H14.019V15.017Zm0,6h15.99V22.98H14.019V21.017Zm0,6h15.99V28.98H14.019V27.017Z" transform="translate(-14.031 -15.031)"/>
                    </svg>
                </a>
                <div class="phone R">
                    <?php rew_snippet('site-phone-number'); ?>
                </div>
            </div>
        </header>
    <?php } ?>
