<!DOCTYPE html>
<!--[if lt IE 7 ]>
<html lang="<?=Settings::getInstance()->LANG;?>" class="ie ie6"> <![endif]-->
<!--[if IE 7 ]>
<html lang="<?=Settings::getInstance()->LANG;?>" class="ie ie7"> <![endif]-->
<!--[if IE 8 ]>
<html lang="<?=Settings::getInstance()->LANG;?>" class="ie ie8"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="<?=Settings::getInstance()->LANG;?>"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <?php global $page_subtitle; ?>
        <title>REW Backend<?=!empty($page_subtitle) ? ' - ' . htmlspecialchars($page_subtitle) : '';?></title>
        <?php
        if(!empty($this->getFaviconUrl())) { ?>
            <link rel="icon" href="<?=$this->getFaviconUrl(); ?>"/>
        <?php } ?>
        <meta name="format-detection" content="telephone=no">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://fonts.googleapis.com/css?family=Lato:400,700" rel="stylesheet">
        <?php foreach ($this->getStylesheets() as $stylesheet) {
            echo $stylesheet->execute() . PHP_EOL;
        } ?>
        <style>
            @media (min-width: 992px) {
                body {
                    padding-right: 320px;
                }
            }
        </style>
    </head>

    <body class="preload <?=$body_class; ?>">

        <?php

			global $authuser;
			global $reminders;

			$info 			= $authuser->getInfo();
        if(!empty($info['image'])) {
        $img = '/thumbs/200x200/uploads/agents/' . $info['image'];
        } else {
        $img = '/thumbs/200x200/uploads/agents/na.png';
        }
        $num_reminders = count($reminders);

        ?>

        <?php if (!empty($reminders)) : ?>
        <div id="app_notices">
            <div id="app_notices_win">
                <ul>
                    <?php foreach ($reminders as $k => $reminder) :?>
                    <?php if (!empty($reminder['action_plan'])) { ?>
                    <li
                    <?=$k == 0 ? ' class="active"' : ''; ?>></li>
                    <?php } else { ?>
                    <li data-id="<?=$reminder['id']; ?>"
                    <?=$k == 0 ? ' class="active"' : ''; ?>>
                    <i class="app-icon icon-app-alert S3 ani--shake"></i>

                    <p><span class="status"><?=$reminder['type']; ?></span></p>
                    <p><a href="<?=$reminder['url']; ?>" class="lead">
                            <small>with</small>
                            <?=$reminder['name']; ?></a></p>
                    <p><?=$reminder['details']; ?></p>


                    <div class="btns">
                        <a href="javascript:void(0);" class="btn btn--positive check">
                            <svg class="icon icon-check mar0">
                                <use xlink:href="/backend/img/icos.svg#icon-check"/>
                            </svg>
                            OK</a>
                        <a href="javascript:void(0);" class="btn">Snooze</a>
                    </div>


                    </li>
                    <?php } ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <?php endif; ?>

        <div id="app">

            <?php global $pageTimeline; ?>
            <?php global $timelineMode; ?>
            <div id="app__history" data-timeline-page='<?=$pageTimeline; ?>' data-timeline-mode='<?=$timelineMode; ?>'></div>
            <a class="app_menu_trigger" href="javascript:void(0);" onclick="$('body').toggleClass('open-sidebar-yo')">
                <svg class="icon icon-menu mar0">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-menu"></use>
                </svg>
            </a>

            <?php
    			/* Section */
    			list($app, $app_page) = explode('/', $_GET['page'], 2);
                $display_head = $display_sidebar = true;
    			if ($app == "reset") {
    			    $display_head = $display_sidebar = false;
                }
    		?>


            <div class="menu menu--drop hidden -right" id="menu--user">
                <ul class="menu__list">
                    <li class="menu__item"><a class="menu__link" href="/backend/agents/agent/edit/?id=1">
                            Preferences</a></li>
                    <li class="menu__item"><a class="menu__link" href="/backend/logout/" title="Sign Out">Sign Out</a>
                    </li>
                    <li class="menu__item"><a class="menu__link" href="/backend/help/">Help</a></li>
                </ul>
            </div>
            <?php if ($display_head) { ?>
            <div class="bar" id="app__head">
                <?php echo $page->config('app_logo'); ?>
                <div class="bar__actions">
                    <a class="bar__action" id="search-overlay-show">
                        <svg class="icon">
                            <use xlink:href="/backend/img/icos.svg#icon-search"></use>
                        </svg>
                    </a>
                    <?php echo $page->config('app_header'); ?>
                </div>
            </div>
            <?php } ?>

            <div id="app__body">
                <icon-set></icon-set>
                <?php if ($display_sidebar) { ?>
                <div id="app__sidebar">
                    <?php $page->container('app_sidebar')->loadModules(); ?>
                    <?php echo $page->config('app_sidebar'); ?>
                </div>
                <?php } ?>
                <div id="app__main">
                    <?php echo $page->config('content'); ?>
                </div>
                <?php if ($page->info('display_flyout')) { ?>
                    <flyout-feed></flyout-feed>
                <?php } ?>
                <div class="modals">
                    <modal>
                        <modal-body>
                            <modal-title></modal-title>
                            <modal-content></modal-content>
                        </modal-body>
                    </modal>
                </div>
            </div>
        </div>

        <?php $this->includeFile('inc/tpl/app/search-overlay.tpl.php'); ?>
        <?php $this->includeFile('inc/tpl/app/notifications.tpl.php'); ?>

        <?php foreach ($this->getJavascripts() as $javascipt) echo $javascipt->execute() . PHP_EOL; ?>

        <?php $authuser = Auth::get(); ?>

        <?php if (empty(Settings::get('google.maps.api_key')) && $authuser->isSuperAdmin() &&
        empty($_SESSION['apiWarning'])) { ?>
        <?php $_SESSION['apiWarning'] = true; ?>
        <script>
            $('<div title="Google Maps API Key Required">\
                    <h3>Action Required:</h3>\
                    <p>Configure site to enable mapping features.</p>\
                    <p><a href="/backend/settings/#gapi">Click here to go to backend settings</a></p>\
                </div>').dialog();
        </script>
        <?php } ?>

        <script>
            (function (i, s, o, g, r, a, m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
            (i[r].q=i[r].q||[]).push(arguments)}, i[r].l = 1 * new Date();
            a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
            a.async = 1;
            a.src = g;
            m.parentNode.insertBefore(a, m)
            })
            (window, document, 'script', 'https://www.google-analytics.com/analytics.js', 'ga');

            ga('create', 'UA-96547243-1', 'auto');
            ga('send', 'pageview');
        </script>
    </body>
</html>
