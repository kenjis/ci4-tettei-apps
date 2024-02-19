<?php

use Config\Services;
use CodeIgniter\Router\RouteCollection;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// リダイレクトを設定します。
// URLがbbsで終わる場合、セグメントが足りずページネーションが動作しない
// ため、bbs/indexにリダイレクトさます。
$routes->addRedirect('bbs', 'bbs/index');
// URLがshopで終わる場合、セグメントが足りずページネーションが動作しない
// ため、shop/index/1にリダイレクトさせます。
$routes->addRedirect('shop', 'shop/index/1');

/*
 * Attribute Routes
 *
 * To update the route file, run the following command:
 * $ php spark route:update
 *
 * @see https://github.com/kenjis/ci4-attribute-routes
 */
if (file_exists(APPPATH . 'Config/RoutesFromAttribute.php')) {
    require APPPATH . 'Config/RoutesFromAttribute.php';
}
