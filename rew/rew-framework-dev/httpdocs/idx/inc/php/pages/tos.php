<?php
if (!empty($_COMPLIANCE['terms_required'])) {
    if (isset($_GET['submit']) && $_GET['load_page'] === 'tos') {
        if ($_GET['agree'] == 'true') {
            $_SESSION['compliance_tos'] = true;
            if (!isset($_GET['popup'])) {
                header('Location: ' . Settings::getInstance()->SETTINGS['URL_IDX']);
                exit;
            }
        }
        // JavaScript Callbacks
        ?>
        <script>window.top.location.reload();</script>
        <?php
    }
}
