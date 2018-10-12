<?php

/**
 * @var array $applications
 * @var array $pagination
 * @var \REW\Backend\View\Interfaces\FactoryInterface $view
 * @var \REW\Core\Interfaces\SettingsInterface $settings
 */

?>
<div class="menu menu--drop hidden" id="menu--filters">
    <ul class="menu__list">
        <li class="menu__item is-current"><a class="menu__link" href="/backend/settings/api/outgoing/"><?= __('Outgoing API'); ?></a></li>
    </ul>
</div>

<div class="bar">
    <div class="bar__title" data-drop="#menu--filters"><?= __('API Applications '); ?><svg class="icon icon-drop"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-drop"></use></svg></div>
    <div class="bar__actions">
        <a class="bar__action" href="./add/"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use></svg></a>
    </div>
</div>

<div class="block">

    <p>
        <?= __('The REW CRM API allows third-party developers to integrate with your Lead Management system. To use the API, third-parties will need an Application created for them below.'); ?>
        <br>
        <?= __(
            'Once created, send your Application\'s API Key to your developer and point them to the {linkTagStart}API Documentation{linkTagEnd} to get started.',
            [
                '{linkTagStart}' => '<a target="_blank" href="' . $settings->SETTINGS['URL'] . 'api/docs/">',
                '{linkTagEnd}' => '</a>'
            ]
        );
        ?>
    </p>
</div>

<?php if (!empty($applications)) { ?>
<div class="nodes api__settings">
    <ul class="nodes__list">
        <?php foreach ($applications as $app) { ?>
        <li class="nodes__branch">
            <div class="nodes__wrap">
                <div class="article">
                    <div class="article__body">
                        <div class="article__content">

                            <a class="text text--strong" href="app/edit/?id=<?=$app['id']; ?>"><?=htmlspecialchars($app['name']);?></a>
                            <div class="text text--mute api-key"><?=htmlspecialchars($app['api_key']);?></div>


                            <div style="text text--mute" style="white-space: nowrap;">
                                <time datetime="<?= date('c', $app['timestamp_created']); ?>"
                                      title="<?= date('l, F jS Y \@ g:ia',
                                          $app['timestamp_created']); ?>"><?= Format::dateRelative($app['timestamp_created']); ?></time>
                                &bull;
                                <?php if (!empty($app['num_requests'])) { ?>
                                    <?php if (!empty($app['num_requests_ok'])) { ?>
                                        <label class="group group_i" title="<?= __('# of successful requests'); ?>"
                                               style="float: none;">
                                            <?= number_format($app['num_requests_ok']); ?> <?= __('Successful'); ?>
                                        </label>
                                    <?php } ?>
                                    <?php if (!empty($app['num_requests_error'])) { ?>
                                        <label class="group group_a" title="<?= __('# of requests with errors'); ?>"
                                               style="float: none;">
                                            <?= number_format($app['num_requests_error']); ?> <?= __('Errors'); ?>
                                        </label>
                                    <?php } ?>
                                <?php } else { ?>
                                    0 <?= __('Requests'); ?>
                                <?php } ?>

                                <?php if ($app['enabled'] === 'Y') { ?>
                                    &bull; <?= __('Enabled'); ?>
                                <?php } else { ?>
                                    &bull; <?= __('Disabled'); ?>
                                <?php } ?>
                            </div>


                        </div>
                    </div>

                    <div class="nodes__actions">
                        <a class="btn btn--ghost log" href="app/requests/?id=<?=$app['id']; ?>"><?= __('Log'); ?></a>
                        <?php if ($app['id'] != 1) { ?>
                            <form method="post">
                                <input type="hidden" name="delete" value="<?=$app['id']; ?>" />
                                <button onclick="return confirm('<?= __('Are you sure you would like to delete this application? Any solution relying on its API Key will stop working.'); ?>');" title="<?= __('Delete'); ?>" class="btn btn--ghost btn--ico">
                                    <icon name="icon--trash--row"></icon>
                                </button>
                            </form>
                        <?php } ?>
                    </div>

                </div>

            </div>
        </li>
        <?php } ?>
    </ul>
</div>
<?php
    if ($pagination) {
        // Render Pagination
        echo $view->render('::partials/pagination', $pagination);
}
} else { ?>
<p class="block"> <?= __('There are currently no API Applications. Create a new application to allow a third-party to use the API.'); ?> </p>
<?php } ?>
