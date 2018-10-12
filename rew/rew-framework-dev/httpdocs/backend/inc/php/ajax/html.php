<?php

use REW\Core\Interfaces\ModuleInterface;

/**
 * Include Backend Configuration
 */
include_once dirname(__FILE__) . '/../../../common.inc.php';

/**
 * Send as HTML
 */
header("Content-type: text/html");

/**
 * Output Buffering
 */
ob_start();

/**
 * Load Module
 */
if (isset($_GET['module']) && !empty($_GET['module'])) {
    /* Module Options */
    $options = isset($_GET['options']) ? $_GET['options'] : false;

    /* Set AJAX */
    $options['ajax'] = true;

    /* Load Module */
    $container = Container::getInstance();
    $module = null;
    if ($container->has($_GET['module'])) {
        // Load the already-built module, if possible. This is a minor efficiency improvement so that 2 modules don't
        // get created for external packages, but, it is also the only way that said external packages can access
        // options other than superglobals or having a separate controller for ajax and html.
        $module = $container->get($_GET['module']);
        if ($module instanceof ModuleInterface && $module->getId() == $_GET['module']) {
            foreach ($options as $key => $val) {
                $module->config($key, $val);
            }
        } else {
            $module = null;
        }
    }
    if (!$module) {
        // Module Path (Use Backend). We don't want to set the path of an already-loaded module because it probably
        // doesn't exist in rew-framework.
        $options['path'] = dirname(__FILE__) . '/../../../inc/modules/' . escapeshellcmd($_GET['module']) . '/';

        $module = $container->make(ModuleInterface::class, ['id' => $_GET['module'], 'config' => $options]);
    }

    /* Print Module */
    echo $module->display();
}

/**
 * Load Lead Score Details
 */
if (isset($_GET['lead']) && !empty($_GET['lead'])) {
    /* Agent Mode */
    $sql_agent = ($authuser->info('mode') == 'agent') ? "`agent` = '" . $authuser->info('id') . "'" : '';

    /* Select Row */
    $result = mysql_query("SELECT * FROM `" . LM_TABLE_LEADS . "` WHERE `id` = '" . mysql_real_escape_string($_GET['lead']) . "'" . (!empty($sql_agent) ? " AND " . $sql_agent : "") . ";");
    $lead = mysql_fetch_assoc($result);

    /* Require Row */
    if (!empty($lead)) {
        // Use Lead Object
        $lead = new Backend_Lead($lead);

        // Update Score
        $lead->updateScore();

        /* Scoring Details */
        if (!empty($lead['scoring'])) {
?>
<table>
    <thead>
        <tr>
            <th>Category</th>
            <th style="text-align: center;">Weight</th>
            <th style="text-align: center;">Values</th>
            <th style="text-align: center;">Score</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($lead['scoring'] as $type => $score) : ?>
            <tr>
                <td><?=ucwords($type); ?></td>
                <td style="text-align: center;"><?=$score['weight']; ?>
                <td style="text-align: center;"><?=number_format($score['value']); ?> / <?=$score['maximum']; ?></td>
                <td style="text-align: center;"><?=$score['score']; ?> / <?=$score['total']; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3" style="text-align: right;">Total:</th>
            <th style="text-align: center;"><?=$lead['score']; ?></th>
        </tr>
    </tfoot>
</table>
<?php
        }
    }
}

/* Grab Output */
$output = ob_get_clean();

/**
 * Return HTML Response
 */
die($output);

?>