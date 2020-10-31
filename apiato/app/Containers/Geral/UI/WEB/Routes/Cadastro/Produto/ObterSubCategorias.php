<?php

$_localRouterConfigs = require __DIR__ . '/configs.php';

/** @var Route $router */
$router->get($_localRouterConfigs['adminUrlPrefix'] . '/' . $_localRouterConfigs['url'] . '/subcategorias/{id}', array_merge($_localRouterConfigs['adminRouterOptions'], [
    'as' => 'web_' . $_localRouterConfigs['name'] . '_obter_subcategorias',
    'uses' => $_localRouterConfigs['controller'] . '@obterSubCategorias',
    'middleware' => [
        'auth.admin'
    ],
]))->where('id', '[0-9]+');
