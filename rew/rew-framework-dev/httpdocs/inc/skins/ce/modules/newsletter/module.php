<?php
// Get current user information
$user = User_Session::get();

$email = $user->info('email');

// Load lead instance from ID
$user_id = $user->getUserId();
$lead = Backend_Lead::load($user_id);

// Opt-in setting before signup
$optIn = $lead->info('opt_marketing') === 'out';
$user->saveInfo('newOptIn', $optIn);

$lead->info('opt_marketing', 'in');
$lead->save();
?>