<?php

/**
 * Lead summary template
 * @var string $title
 * @var string|false $back
 * @var array[] $actions {
 *   @var string $href
 *   @var string $icon
 *   @var string $name
 * }
 * @var array $lead {
 *   @var string $id
 *   @var string $first_name
 *   @var string $last_name
 *   @var string $timestamp_active
 * }
 * @var REW\Backend\Auth\LeadAuth $leadAuth
 */

// Include lead's navigation menu
if (!empty($title) && empty($popup)) {
    echo $this->render(__DIR__ . '/summary-menu.tpl.php', [
        'leadId' => $lead['id'],
        'leadAuth' => $leadAuth
    ]);
}

?>
<?php if (empty($popup)) { ?>
<div class="bar">
    <?php if (!empty($title)) { ?>
        <a class="bar__title" data-drop="#menu--filters" href="javascript:void(0);">
            <?=Format::htmlspecialchars($title); ?>
            <svg class="icon icon-drop">
                <use xlink:href="/backend/img/icos.svg#icon-drop" xmlns:xlink="http://www.w3.org/1999/xlink" />
            </svg>
        </a>
    <?php } ?>
    <div class="bar__actions">
        <?php if (!empty($actions)) { ?>
            <?php foreach ($actions as $action) { ?>
                <a class="bar__action" href="<?=$action['href'] ?: 'javascript:void(0);'; ?>">
                    <svg class="icon">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-<?=$action['icon']; ?>" />
                    </svg>
                </a>
            <?php } ?>
        <?php } ?>
        <?php if ($back !== false) { ?>
            <a class="bar__action timeline__back" href="<?=$back ?: '/backend/leads/?back'; ?>">
                <svg class="icon">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a" />
                </svg>
            </a>
        <?php } ?>
    </div>
</div>
<?php } ?>
<?php

// Include contact preview
echo $this->render(__DIR__ . '/summary-preview.tpl.php', [
    'firstName' => $lead['first_name'],
    'lastName' => $lead['last_name'],
    'leadId' => $lead['id'],
    'lastActive' => $lead['timestamp_active']
]);
