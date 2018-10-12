<?php

$relativeFile = substr($exception->getFile(), strlen($_SERVER['DOCUMENT_ROOT']));

$json = array(
    'type' => get_class($exception),
    'code' => $exception->getCode(),
    'message' => $exception->getMessage(),
    'file' => $relativeFile,
    'line' => $exception->getLine(),
);
