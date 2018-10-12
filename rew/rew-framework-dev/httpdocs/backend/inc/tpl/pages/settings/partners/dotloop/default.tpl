<form action="?<?=(isset($_GET['setup'])) ? 'setup' : ''; ?>" method="post" class="rew_check">

    <div class="bar">
        <div class="bar__title"><?= __('DotLoop'); ?></div>
        <div class="bar__actions">
            <?php if (isset($_GET['setup'])) { ?>
                <a class="bar__action" href="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>settings/partners/dotloop/"><svg class="icon"><use xlink:href="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>img/icos.svg#icon-left-a"/></svg></a>
            <?php } else { ?>
                <a class="bar__action" href="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>settings/partners/"><svg class="icon"><use xlink:href="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>img/icos.svg#icon-left-a"/></svg></a>
            <?php } ?>
        </div>
    </div>

    <div class="block">

        <?php if (empty($logins_valid)) { ?>
            <div class="help">
                <img src="/backend/img/hlp/setup.png"/>
                <p class="text text--mute"><?= __('Dotloop is a third party REW partner that replaces your form creation, e-sign, and real estate transaction management systems with a single end-to-end solution, while helping you streamline your business with real-time visibility into your transactions.'); ?></p>
                <?php if (!empty($authuser->info('partners.dotloop.token_updated'))) { ?>
                    <h1><?= __('Refresh DotLoop API Access Token'); ?></h1>
                    <p class="text text--negative"><?= __('Your DotLoop API access token has %s. Please request a new token to reactivate DotLoop features.', '<strong>' . __('expired') . '</strong>'); ?></p>
                    <p>
                        <a href="<?=$api->generateApprovalLink(); ?>" class="btn btn--positive">
                            <svg class="icon icon-add mar0">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use>
                            </svg> <?= __('Request a New Token'); ?>
                        </a>
                    </p>
                <?php } else { ?>
                    <h1><?= __('Set Up DotLoop Integration'); ?></h1>
                    <p><?= __('DotLoop integration is currently %s. To use this transaction system, you must approve application access to your DotLoop account.', '<strong>' . __('inactive') . '</strong>'); ?></p>
                    <p>
                        <a href="<?=$api->generateApprovalLink(); ?>" class="btn btn--positive">
                            <svg class="icon icon-add mar0">
                                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use>
                            </svg> <?= __('Set Up DotLoop Integration'); ?>
                        </a>
                    </p>
                <?php } ?>
            </div>
        <?php  } else { ?>

            <div class="btns btns--stickyB">
                <span class="R">
                    <button class="btn btn--positive" type="submit"><svg class="icon"><use xlink:href="<?=Settings::getInstance()->URLS['URL_BACKEND']; ?>img/icos.svg#icon-check"/></svg> <?= __('Save'); ?></button>
                </span>
            </div>

            <div class="divider">
                <span class="divider__label divider__label--left text"><?= __('Sync Settings'); ?></span>
            </div>

            <div class="group_swatches partner">
                <p><?= __('DotLoop integration is currently configured and %s.', '<strong>' . __('active') . '</strong>'); ?></p>
            </div>

            <p><a class="btn delete" href="../?disconnect=dotloop" onclick="javascript:return  confirm('<?= __('Are you sure you want to disable the integration with this partner?'); ?>');"><?= __('Disable Integration'); ?></a></p>

        <?php } ?>

    </div>

</form>