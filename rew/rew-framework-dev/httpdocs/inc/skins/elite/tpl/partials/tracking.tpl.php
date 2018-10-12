<?php

// Execute tracking script
if (!empty($trackingScript)) {
    echo '<div id="ppc-iframe"></div>' . PHP_EOL;
    echo '<script type="text/javascript">' . PHP_EOL;
    echo 'var ppc = document.getElementById(\'ppc-iframe\');' . PHP_EOL;
    echo 'var iframe = document.createElement(\'iframe\');' . PHP_EOL;
    echo 'iframe.style.display = \'inline-block\';' . PHP_EOL;
    echo 'iframe.style.border = 0;' . PHP_EOL;
    echo 'ppc.appendChild(iframe);' . PHP_EOL;
    echo 'iframe.contentWindow.document.open();' . PHP_EOL;
    echo sprintf('iframe.contentWindow.document.write(%s);', json_encode($trackingScript)) . PHP_EOL;
    echo 'iframe.contentWindow.document.close();' . PHP_EOL;
    echo '</script>';
}