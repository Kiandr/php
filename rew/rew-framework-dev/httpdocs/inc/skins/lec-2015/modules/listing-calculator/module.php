<?php

// Interest compounding (semi-annually vs monthly)
$this->config('compounding', Settings::getInstance()->LANG === 'en-CA' ? 2 : 12);

// Mortgage calculator settings
$down_percent   = (int) $this->config('down_percent');
$mortgage_term  = (int) $this->config('mortgage_term');
$interest_rate  = (float) $this->config('interest_rate');

// Mortgage calculator defaults
$down_percent   = $down_percent > 0 ? $down_percent : 20;
$interest_rate  = $interest_rate > 0 ? $interest_rate : 3.5;
$mortgage_term  = $mortgage_term > 0 ? $mortgage_term : 25;

// Update configuration options
$this->config('down_percent', $down_percent);
$this->config('interest_rate', $interest_rate);
$this->config('mortgage_term', $mortgage_term);

// Available options
$interest_rates = array(3, 3.25, 3.5, 3.75, 4, 4.25, 4.5, 4.75, 5);
$mortgage_terms = array(5, 10, 15, 20, 25, 30, 35);

// Initial down payment amount
$listing_price  = $this->config('listing_price') ?: 0;
$down_payment   = $listing_price * ($down_percent / 100);
