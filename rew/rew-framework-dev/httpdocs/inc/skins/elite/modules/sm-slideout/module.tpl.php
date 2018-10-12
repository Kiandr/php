<?php $url = Settings::getInstance()->SETTINGS['URL_RAW'] . $_SERVER['REQUEST_URI']; ?>
<div id="sm-slideout" class="uk-offcanvas">
    <div class="uk-offcanvas-bar uk-offcanvas-bar-flip">
        <div id="sm-slideout-wrap">

            <h3 class="sm-slide">Connect With Us</h3>
            <ul class="uk-list">
                <?php foreach ((new Backend_Agent(Settings::getInstance()->SETTINGS['cms']['agent']))->getSocialNetworks() as $network) { ?>
                    <li>
                        <a href="<?= Format::htmlspecialchars($network['url']); ?>">
                            <i class="uk-icon uk-icon-<?= $network['slug']; ?> uk-icon-justify uk-margin-right"></i>
                            <span class="uk-text-uppercase"> <?= Format::htmlspecialchars($network['name']); ?></span>
                        </a>
                    </li>
                <?php } ?>
            </ul>

            <?php if (!empty($user) && $user->isValid()) { ?>
                <h3>Welcome, <?=Format::htmlspecialchars($user->info('first_name')); ?>!</h3>
                <ul class="uk-list uk-list-space">
                    <li><a data-modal="dashboard"><i class="uk-icon uk-icon-cog uk-icon-justify uk-margin-right"></i> Dashboard</a></li>
                    <li><a class="slideout-link popup icon-star" data-dashboard="favorites"><i class="uk-icon uk-icon-star uk-icon-justify uk-margin-right"></i> <?=Locale::spell('Favorites'); ?></a></li>
                    <li><a class="slideout-link popup icon-save" data-dashboard="searches"><i class="uk-icon uk-icon-save uk-icon-justify uk-margin-right"></i> Saved Searches</a></li>
                    <li><a class="slideout-link popup icon-comment" data-dashboard="messages"><i class="uk-icon uk-icon-comment uk-icon-justify uk-margin-right"></i> Messages</a></li>
                    <li><a class="slideout-link popup icon-gears" data-dashboard="preferences"><i class="uk-icon uk-icon-gears uk-icon-justify uk-margin-right"></i> Preferences</a></li>
                    <li><a data-modal="logout"><i class="uk-icon uk-icon-sign-out uk-icon-justify uk-margin-right"></i> Sign Out</a></li>
                </ul>

            <?php } else { ?>
                <h3>Dashboard</h3>
                <ul class="uk-list">
                    <li><a class="uk-text-uppercase" data-modal="register"><i class="uk-icon uk-icon-pencil uk-icon-justify uk-margin-right"></i> Register</a></li>
                    <li><a class="uk-text-uppercase" data-modal="login"><i class="uk-icon uk-icon-user uk-icon-justify uk-margin-right"></i> Sign In</a></li>
                </ul>
            
                <?php if (!empty($networks)) { ?>
                    <h3>Login using...</h3>
                    <div class="social-media-login-buttons">
                            <?php foreach ($networks as $id => $network) { ?>
                                <a class="network-login <?=$id; ?> uk-icon-button uk-icon-<?=$id; ?>" href="javascript:var w = window.open('<?= Format::htmlspecialchars($network['connect']); ?>', 'socialconnect', 'toolbar=0,status=0,scrollbars=1,width=600,height=450,left='+(screen.availWidth/2-225)+',top='+(screen.availHeight/2-250)); w.focus();"></a>
                          <?php } ?>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    </div>
</div>
