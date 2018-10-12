<?php

// Get the Page Object
$page = $this->getContainer()->getPage();

$quicksearch = $page->container('snippet')->addModule('idx-search');
