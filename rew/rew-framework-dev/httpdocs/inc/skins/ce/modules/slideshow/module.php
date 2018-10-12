<?php

$registrationRequired = $this->config('registration_on_more_pics') ?: false;

try {
    // Slideshow Images
    $images = $this->config('images');
    $vr = $this->config('360');

    // Load Images
    foreach ($images as $image) {
        // Add to Slideshow
        $slideshow[]['image'] = $image;

        if ($registrationRequired) {
            break;
        }
    }

    $this->config('slideshow', $slideshow);
    
// Error Occurred
} catch (Exception $e) {
    Log::error($e);
}
