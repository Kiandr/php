
<?php

// Include site's header
$this->includeFile('tpl/misc/header.tpl.php');

$this->container('content')->loadModules();

$this->includeFile('tpl/misc/footer.tpl.php');
