<?php

return [
    'view' => [
        'twig' => [
            'debug' => false,
            'auto_reload' => false,
            'base_template_class' => 'Twig_Template',
            'cache' => __DIR__ . '/../cache/twig',
            'charset' => 'utf-8',
            'autoescape' => 'name',
            'optimizations' => -1,
            'strict_variables' => false
        ]
    ]
];
