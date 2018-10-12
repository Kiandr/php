<?php

/**
 * @var array $destinations
 * @var \Hook_REW_OutgoingAPI::DESTINATION_TYPE_REW $type_rew
 */

?>
<div class="menu menu--drop hidden" id="menu--filters">
    <ul class="menu__list">
        <li class="menu__item is-current"><a class="menu__link" href="/backend/settings/api/"><?= __('API Applications'); ?></a></li>
    </ul>
</div>

<div class="bar">
    <div class="bar__title" data-drop="#menu--filters"><?= __('Outgoing API'); ?> <svg class="icon icon-drop"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-drop"></use></svg></div>
    <div class="bar__actions">
        <a class="bar__action" href="./add/"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use></svg></a>
    </div>
</div>

<p class="block"> <?= __('The Outgoing API lets your site notify a third-party when certain events occur throughout the system.'); ?><br>
    <?= __('For example, when a lead registers, you can configure a "Destination" that will get notified when this happens. This Destination can be a third-party site or another REW site.'); ?> </p>

<?php if (!empty($destinations)) { ?>
<div class="block">
    <div class="table__wrap">
        <table class="table item_content_summaries">
            <thead>
            <tr>
                <th><?= __('Name'); ?></th>
                <th><?= __('Status'); ?></th>
                <th><?= __('Events'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($destinations as $k => $destination) { ?>
            <tr>
                <td style="text-align: left;">
                    <div class="item_content_summary api_application">
                        <h4 class="item_content_title">
                            <a href="destination/edit/?id=<?=$k + 1; ?>">
                                <?=htmlspecialchars($destination['name']);?>
                            </a>
                        </h4>
                        <div class="item_content_additional -marB">
                            <strong><?= __('URL'); ?>:</strong>
                            <?=htmlspecialchars($destination['url']);?>
                            <br>
                            <?php if ($destination['type'] == $type_rew) { ?>
                            <strong><?= __('API Key'); ?>:</strong>
                            <?=htmlspecialchars($destination['api_key']);?>
                            <?php } ?>
                        </div>
                        <div class="actions compact">
                            <a class="btn edit -marR8 -L" href="destination/edit/?id=<?=$k + 1; ?>"><?= __('Edit'); ?></a>
                            <form class="-L" method="post">
                                <input type="hidden" name="delete" value="<?=$k + 1; ?>" />
                                <button onclick="return confirm('<?= __('Are you sure you would like to delete this destination?'); ?>');" title="<?= __('Delete'); ?>" class="btn btn--negative delete">
                                    <?=__('Delete'); ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </td>
                <td class="groups">
                    <?php if ($destination['enabled'] === 'Y') { ?>
                    <label class="group group_i"> <?= __('Enabled'); ?> </label>
                    <?php } else { ?>
                    <label class="group group_a"> <?= __('Disabled'); ?> </label>
                    <?php } ?>
                </td>
                <td style="text-align: left;">
                    <?php if (!empty($destination['events_human'])) { ?>
                    <ul style="list-style: none; white-space: nowrap;">
                        <?php foreach ($destination['events_human'] as $event) { ?>
                        <li><?=htmlspecialchars($event);?></li>
                        <?php } ?>
                    </ul>
                    <?php } else { ?>
                    <?= __('No Events'); ?>
                    <?php } ?>
                </td>
            </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>
<?php } else { ?>
<p class="block"> <?= __('There are currently no outgoing destinations configured. Add a new destination to notify another site of certain events as they occur on this site.'); ?> </p>
<?php } ?>
