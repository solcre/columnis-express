<?php
/**
 * Local Configuration Override
 *
 * This configuration override file is for overriding environment-specific and
 * security-sensitive configuration information. Copy this file without the
 * .dist extension at the end and populate values as needed.
 *
 * @NOTE: This file is ignored from Git by default with the .gitignore included
 * in ZendSkeletonApplication. This is a good practice, as it prevents sensitive
 * credentials from accidentally being committed into version control.
 */
return array(
    'service_manager' => array(
        'shared' => array(
            'Columnis\Service\ApiService' => false,
            'Columnis\Service\PageService' => false,
            'Columnis\Service\TemplateService' => false,
            'Columnis\Model\TemplateAssetsResolver' => false
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__.'/../../_files/public/templates',
        ),
    ),
    'columnis' => array(
        'api_settings' => array(
            'client_number' => '001',
            'api_base_url' => 'http://api.columnis.dev/'
        )
    ),
    'template_assets_resolver' => array(
        'match_patterns' => array(
            'template_name' => '/^templates\/([a-zA-Z0-9-_]+)\/(css|js)\/minified\.(css|js)$/',
            'global_asset' => '/^(css|js)\/global\/.+\.css|js$/'
        ),
        'global_folder_name' => 'global'
    ),
    'asset_manager' => array(
        'resolver_configs' => array(
            'paths' => array(
                __DIR__.'/../../_files/public/css',
                __DIR__.'/../../_files/public/js',
            ),
        ),
        'resolvers' => array(
            'Columnis\Model\TemplateAssetsResolver' => 2000,
        ),
    ),
);
