<?php

/**
 * Associate summary template
 * @var string $title
 * @var Backend_Associate $associate
 * @var REW\Backend\Auth\Associates\AssociateAuth $associateAuth
 */

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
            <a class="bar__action timeline__back" href="<?=$back ?: '/backend/associates/?back'; ?>">
                <svg class="icon">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a" />
                </svg>
            </a>
        <?php } ?>
    </div>
</div>
<?php } ?>
<?php

// Include assocaite preview
echo $this->render(__DIR__ . '/summary-preview.tpl.php', [
    'firstName' => $associate['first_name'],
    'lastName' => $associate['last_name'],
    'image' => $associate['image'],
    'lastLogon' => $associate['last_logon']
]);