<?php

/**
 * Agent Summary template
* @var string $title
* @var \Backend_Agent $agent
* @var REW\Backend\Auth\Agents\AgentAuth $agentAuth
*/

// Include agent's navigation menu
if (!empty($title) && empty($popup)) {
    echo $this->render(__DIR__ . '/summary-menu.tpl.php', [
        'agentId' => $agent['id'],
        'agentAuth' => $agentAuth
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
        <a class="bar__action timeline__back" href="<?='/backend/agents/?back'; ?>">
            <svg class="icon">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a" />
            </svg>
        </a>
    </div>
</div>
<?php } ?>
<?php

// Include agent preview
echo $this->render(__DIR__ . '/summary-preview.tpl.php', [
    'agent' => $agent
]);
