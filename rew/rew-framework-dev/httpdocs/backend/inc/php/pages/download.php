<?php

// Selected FIle
if (isset($_GET['id'])) {
    // Increase Memory Limit
    ini_set('memory_limit', 48 * 1024 * 1024);

    // Locate File
    $query = "SELECT `id`, `name`, `type`, `size`, `password`, `data` FROM `cms_files` WHERE `id` = '" . mysql_real_escape_string($_GET['id']) . "' LIMIT 1;";
    if ($result = mysql_query($query)) {
        $file = mysql_fetch_assoc($result);

        // Require File
        if (!empty($file)) {
            // Allow Thumb Preview for Protected Images
            $ext = substr($file['name'], strrpos($file['name'], '.') + 1);
            if (in_array($ext, array('jpg', 'jpeg', 'png', 'gif'))) {
                if ($_SERVER['SERVER_ADDR'] === $_SERVER['REMOTE_ADDR']) {
                    // Bypass Authentication
                    unset($file['password']);
                }
            }

            // Password Protected
            if (!empty($file['password']) && ($file['password'] !== $_POST['password'])) {
?>
<form method="post">
    <div class="rewui rewmodule login_form -pad" id="app__main">
        <h2 class="rewmodule_title -marB8">Password Protected File <span class="version"><?=Format::truncate($file['name'], 35); ?></span></h2>
        <div class="rewmodule_content">
            <fieldset class="-pad">
                <div class="username -marB">
                    <label class="-marR8">Password</label>
                    <input type="password" name="password" autofocus required>
                 </div>
                <div class="rewui buttonset">
                    <button class="btn">Download</button>
                </div>
            </fieldset>
        </div>
    </div>
</form>
<?php
            } else {
                // Update # of Views
                if ($_SERVER['SERVER_ADDR'] !== $_SERVER['REMOTE_ADDR']) {
                    mysql_query("UPDATE `cms_files` SET `views` = `views` + 1 WHERE `id` = '" . mysql_real_escape_string($file['id']) . "';");
                }

                // Turn Off Compression
                // @see https://bugs.php.net/bug.php?id=44164
                ini_set('zlib.output_compression', 'Off');

                // Send Headers for Download
                header('Content-Length: ' . strlen($file['data']));
                header('Content-Type: ' . $file['type']);
                header('Content-Disposition: filename="' . $file['name'] . '"');

                // Return Data
                echo $file['data'];
                exit;
            }
        } else {
            // Unknown File
            header('Location: /404.php');
            exit;
        }
    } else {
        // Query Error
        echo 'Error Occurred';
        exit;
    }
} else {
    // File not Found
    header('Location: /404.php');
    exit;
}