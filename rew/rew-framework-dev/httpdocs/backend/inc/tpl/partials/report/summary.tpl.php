<?php

/**
 * Report Summary template
 * @var string $title
 * @var AuthInterface $authuser
 * @var REW\Backend\Auth\ReportsAuth $reportsAuth
 * @var array|null $action {
 *   @var string $href
 *   @var string $name
 * }
 */

// Include report's navigation menu
if (!empty($title)) {
    echo $this->render(__DIR__ . '/summary-menu.tpl.php', [
        'authuser' => $authuser,
        'reportsAuth' => $reportsAuth
    ]);
}

?>
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
        <?php if (!empty($action)) { ?>
            <a class="bar__action" href="<?=$action['href']; ?>"><?=$action['name']; ?></a>
        <?php } ?>
        <a class="bar__action timeline__back" href="<?='/backend/reports/?back'; ?>">
            <svg class="icon">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a" />
            </svg>
        </a>
    </div>
</div>