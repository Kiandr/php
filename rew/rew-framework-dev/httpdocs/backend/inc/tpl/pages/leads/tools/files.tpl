<div class="menu menu--drop hidden" id="menu--filters" style="min-width: 0">
    <ul class="menu__list">
        <?php if ($leadsAuth->canViewFiles($authuser)) { ?>
            <li class="menu__item"><a class="menu__link" href="<?=Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/tools/files'; ?>"<?=!isset($_GET['personal']) ? ' class="current"' : ''; ?>><?= __('All Files'); ?></a></li>
            <li class="menu__item"><a class="menu__link" href="<?=Settings::getInstance()->URLS['URL_BACKEND'] . 'leads/tools/files?personal'; ?>"<?=isset($_GET['personal']) ? ' class="current"' : ''; ?>><?= __('Your Files'); ?></a></li>
            <li class="menu__item divider"></li>
        <?php } ?>
        <li class="menu__item"><a class="menu__link menu__link--negative" data-action="delete" disabled><?= __('Delete'); ?></a></li>
    </ul>
</div>
<form action="?submit" method="post">
    <?php if ($leadsAuth->canViewFiles($authuser) && isset($_GET['personal'])) { ?>
        <input type="hidden" name="personal" value=true/>
    <?php } ?>
    <div class="bar">
        <div class="bar__title" data-drop="#menu--filters" style="cursor: pointer;">
            <?= __('File Manager'); ?><?=($leadsAuth->canViewFiles($authuser) && isset($_GET['personal']))
            ? ': ' . htmlspecialchars($authuser->info('first_name').' '.$authuser->info('last_name')) : ''; ?>
            <svg class="icon icon-drop">
                <use xlink:href="/backend/img/icos.svg#icon-drop" xmlns:xlink="http://www.w3.org/1999/xlink"/>
            </svg>
        </div>
        <div class="bar__actions">
            <?php if (isset($_REQUEST['popup']) && isset($_REQUEST['email'])) { ?>
                <button type="button" class="btn btn--ico" data-action="attach"><?= __('Attach Files'); ?></button>
            <?php }?>
            <?php

                // Uploader
                echo sprintf(
                    '<div id="file-uploader" class="bar__action %s" data-exts=\'%s\'></div>',
                    $max_exceed ? ' hidden': '',
                    json_encode($allowed_exts)
                );

            ?>
        </div>
    </div>

    <div class="block">
        <div id="storage-exceed"<?=empty($max_exceed) ? ' class="hidden"' : ''; ?>>
            <strong><?= __('Storage Limit Exceeded:'); ?></strong>
            <span class="storage-usage"><?=$usage; ?></span>
        </div>
    </div>

        <?php if (isset($_GET['email'])) { ?>
            <table id="file-attachment-manager" class="item_content_summaries">
                <tbody id="email_files">
                    <?php

                        // Display Files
                        if (!empty($email_files)) {
                            foreach ($email_files as $file) {
                                $generateRowHTML ($file);
                            }

                        // No Files
                        } else {
                            echo '<tr class="block">';
                            echo '<td colspan="6">' . __('There are currently no uploaded email attachment files.') . '</td>';
                            echo '</tr>';
                        }

                    ?>
                </tbody>
            </table>
        <?php } ?>
        <?=$attachment_pagination ? $this->view->render('inc/tpl/partials/pagination.tpl.php', $attachment_pagination) : ''; ?>
        <div id="file-manager">
            <div class="padH">
                <label class="toggle">
                    <input type="checkbox" name="all">
                    <span class="toggle__label"><?= __('Select All'); ?></span>
                </label>
            </div>
            <div class="nodes">
                <ul class="nodes__list">
                    <?php

                        // Display Files
                        if (!empty($files)) {
                            foreach ($files as $file) {
                                // Display Table Row
                                $generateRowHTML ($file);
                            }

                        // No Files
                        } else {
                            echo '<li class="nodes__branch none"><div class="nodes__wrap">' . __('There are currently no uploaded files.') . '</div></li>';
                        }

                    ?>
                </ul>
            </div>
            <p class="block storage-usage"><?=$usage; ?></p>
        </div>
	    <?=$pagination ? $this->view->render('inc/tpl/partials/pagination.tpl.php', $pagination) : ''; ?>

</form>