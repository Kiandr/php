<?php

// Render HTML content from page instance
$page = $this->getContainer()->getPage();
echo $page->info('content');