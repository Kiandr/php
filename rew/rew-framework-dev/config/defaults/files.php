<?php
/**
 * The uploaders are used throughout the framework, these are the default configurations for each file type
 */
return [
    /**
     * Default File upload configuration settings
     */
    'files' => [
        'default' => [
            //'path' => '/uploads/',
            'file_size_limit' => '10m',
            'allowed_extensions' => [
                // Images
                'jpg'  => IMAGETYPE_JPEG,
                'jpeg' => IMAGETYPE_JPEG,
                'png'  => IMAGETYPE_PNG,
                'gif'  => IMAGETYPE_GIF,

                // Word Documents
                'doc'  => null,
                'docx' => null,
                'rtf'  => null,

                // Adobe PDF
                'pdf'  => null,

                // Excel Files
                'xls'  => null,
                'xlsx' => null,
                'csv'  => null,

                // Power Point
                'pptx' => null,
                'ppt'  => null,
                'pps'  => null,
                'ppsx' => null,

                // Open Office
                'odp'  => null,
                'odt'  => null,

                // Plain Text
                'log'  => null,
                'txt'  => null,

                // MP3 Audio
                'mp3'  => null
            ]
        ],
        /**
         * Default Image upload configuration settings
         */
        'images' => [
            'default' => [
                'file_size_limit' => '10m',
                'allowed_extensions' => [
                    'jpg' => IMAGETYPE_JPEG,
                    'jpeg' => IMAGETYPE_JPEG,
                    'png' => IMAGETYPE_PNG,
                    'gif' => IMAGETYPE_GIF
                ]
            ],
            'favicon' => [
                'file_size_limit' => '1m',
                'allowed_extensions' => [
                    'ico' => IMAGETYPE_ICO,
                    'png' => IMAGETYPE_PNG,
                    'gif' => IMAGETYPE_GIF
                ]
            ]
        ]
    ]
];