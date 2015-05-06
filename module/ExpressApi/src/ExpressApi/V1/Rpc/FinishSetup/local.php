<?php

return array(
    'guzzle_cache' => array(
        'adapter' => 'filesystem',
        'options' => array(
            'cache_dir' => 'data/cache/api'
        )
    ),
    'columnis' => array(
        'api_settings' => array(
            'client_number' => '<%client_number%>',
            'api_base_url' => '<%api_base_url%>',
            'api_version' => '<%api_version%>',
            'user' => '<%user%>',
            'pass' => '<%pass%>',
            'ip' => '75.126.156.130'
        ),
    ),
    'asset_manager' => array(
        'filters' => array(
            'js' => array(
                array(
                    'filter' => 'JSMin',
                ),
            ),
            'css' => array(
                array(
                    'filter' => 'CssMin',
                ),
                array(
                    'filter' => 'CssRewrite',
                ),
            ),
        ),
    ),
    'template_assets_resolver' => array(
        'public_path' => getcwd().DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR
    ),
    'zf-oauth2' => array(
        'allow_implicit' => true,
        'storage' => 'ZF\\OAuth2\\Adapter\\PdoAdapter',
        'db' => array(
            'dsn_type' => 'PDO',
            'dsn' => 'mysql:dbname=<%user%>_<%db_name%>;host=localhost',
            'username' => '<%user%>_<%db_user%>',
            'password' => '<%db_password%>',
        ),
    ),
);
