<?php
$adminRouterOptions = [];
$adminUrlPrefix = \Config::get('app.admin_url');

$adminUrlDomain = 'shop.' . parse_url(\Config::get('app.url'))['host'];
if ($adminUrlDomain) {
    $adminRouterOptions['domain'] = $adminUrlDomain;
}
$router->get($adminUrlPrefix . '/', array_merge($adminRouterOptions, [
    'as' => 'get_shop_index',
    'uses' => 'ShopController@shopPage',
]));
$router->get($adminUrlPrefix . '/item/{id}', array_merge($adminRouterOptions, [
    'as' => 'get_shop_index',
    'uses' => 'ShopController@shopPage',
]))->where('id', '[0-9]+');

$adminUrlDomain = \Config::get('app.admin_domain');
if ($adminUrlDomain) {
    $adminUrlDomain .= '.' . parse_url(\Config::get('app.url'))['host'];
    if ($adminUrlDomain) {
        $adminRouterOptions['domain'] = $adminUrlDomain;
    }
}
$router->post($adminUrlPrefix . '/search', array_merge($adminRouterOptions, [
    'as' => 'post_shop_search',
    'uses' => 'ShopController@search',
]));
$router->post($adminUrlPrefix . '/categorias', array_merge($adminRouterOptions, [
    'as' => 'post_shop_categorias',
    'uses' => 'ShopController@categorias',
]));
$router->get($adminUrlPrefix . '/produto/{id}', array_merge($adminRouterOptions, [
    'as' => 'get_shop_produto',
    'uses' => 'ShopController@produto',
]))->where('id', '[0-9]+');
