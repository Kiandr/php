<div class="bar">
    <div class="bar__title"><?= __('Partners'); ?></div>
</div>

<div class="block">

    <div class="cols">
        <?php if (in_array('grasshopper', $authorized)) { ?>
            <div class="col w1/3 marB"><a href="grasshopper/"><img class="logo bdr dB C" src="<?=Settings::getInstance()->URLS['URL_BACKEND'];?>img/partners/hg-logo.png"></a></div>
        <?php } ?>
        <?php if (in_array('bombbomb', $authorized)) { ?>
            <div class="col w1/3 marB"><a href="bombbomb/"><img class="logo bdr dB C" src="<?=Settings::getInstance()->URLS['URL_BACKEND'];?>img/partners/bombbomb-logo.png"></a></div>
        <?php } ?>
        <?php if (in_array('followupboss', $authorized)) { ?>
            <div class="col w1/3 marB"><a href="followupboss/"><img class="logo bdr dB C" src="<?=Settings::getInstance()->URLS['URL_BACKEND'];?>img/partners/fub-splash.png"></a></div>
        <?php } ?>
        <?php if (in_array('wiseagent', $authorized)) { ?>
            <div class="col w1/3 marB"><a href="wiseagent/"><img class="logo bdr dB C" src="<?=Settings::getInstance()->URLS['URL_BACKEND'];?>img/partners/wiseagent-logo.png"></a></div>
        <?php } ?>
        <?php if (in_array('espresso', $authorized)) { ?>
            <div class="col w1/3 marB"><a href="espresso/"><img class="logo bdr dB C" src="<?=Settings::getInstance()->URLS['URL_BACKEND'];?>img/partners/rew-dialer-logo.png"></a></div>
        <?php } ?>
        <?php if (in_array('zillow', $authorized)) { ?>
            <div class="col w1/3 marB"><a href="zillow/"><img class="logo bdr dB C" src="<?=Settings::getInstance()->URLS['URL_BACKEND'];?>img/partners/zillow-logo.png"></a></div>
        <?php } ?>
        <?php if (in_array('firstcallagent', $authorized)) { ?>
            <div class="col w1/3 marB"><a href="firstcallagent/"><img class="logo bdr dB C" src="<?=Settings::getInstance()->URLS['URL_BACKEND'];?>img/partners/firstcallagent-logo.png"></a></div>
        <?php } ?>
        <?php if (in_array('dotloop', $authorized)) { ?>
            <div class="col w1/3 marB"><a href="dotloop/"><img class="logo bdr dB C" src="<?=Settings::getInstance()->URLS['URL_BACKEND'];?>img/partners/dotloop-logo.png"></a></div>
        <?php } ?>
    </div>

    <p><?= __('Please be aware that these features are dependent on the operation and cooperation of third party services. As such, Real Estate Webmasters can\'t guarantee continuous availability of these features.'); ?></p>

</div>