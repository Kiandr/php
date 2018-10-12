<?php

// Redirect to Homepage
header('Location: ' . Settings::getInstance()->SETTINGS['URL']);
exit;
