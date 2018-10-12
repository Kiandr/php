<?php

try {
    $slides = $this->config('slides');
    $slides_count = count($slides);

// Error Occurred
} catch (Exception $e) {
    Log::error($e);
}
