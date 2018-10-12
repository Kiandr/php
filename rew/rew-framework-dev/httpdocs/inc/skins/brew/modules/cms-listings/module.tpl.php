<?php

// Set Snippet
$_REQUEST['snippet'] = true;

// Page
$page = $this->getContainer()->getPage();

// Load IDX Search Page for CMS Feed
$search = $page->load('idx', 'search', 'cms');

// Print Output
echo $search['category_html'];
unset($_REQUEST['snippet']);