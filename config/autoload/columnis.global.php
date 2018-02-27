<?php

use App\Domain\Service\TemplateAssetsResolver;

return [
    'asset_manager'            => [
        'resolvers' => [
            TemplateAssetsResolver::class => 2000,
        ]
    ],
    'template_assets_resolver' => [
        'match_patterns'     => [
            'template_name' => '/^templates\/([a-zA-Z0-9-_]+)\/(css|js)\/minified\.(css|js)$/',
            'global_asset'  => '/^(css|js)\/fixed\/.+\.(css|js)$/'
        ],
        'global_folder_name' => 'fixed',
        'search_exclude'     => [
            '/templates\/([a-zA-Z0-9-_]+)\/(css|js)\/minified\.(css|js)$/',
            '/(css|js)\/fixed\/minified\.(css|js)$/',
        ],
    ],
];
