<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

// リダイレクトを設定します。
// URLがbbsで終わる場合、セグメントが足りずページネーションが動作しない
// ため、bbs/indexにリダイレクトさます。
$routes->addRedirect('bbs', 'bbs/index');
// URLがshopで終わる場合、セグメントが足りずページネーションが動作しない
// ため、shop/index/1にリダイレクトさせます。
$routes->addRedirect('shop', 'shop/index/1');

$routes->get('shop/index/(:num)', 'Shop\Index::index/$1');
$routes->get('shop/index/(:num)/(:num)', 'Shop\Index::index/$1/$2');
$routes->get('shop/cart', 'Shop\Cart::index');
$routes->post('shop/add/(:num)', 'Shop\Cart::add/$1');
$routes->get('shop/product/(:num)', 'Shop\Product::index/$1');
$routes->get('shop/search', 'Shop\Search::index');
$routes->get('shop/search/(:num)', 'Shop\Search::index/$1');
$routes->post('shop/customer_info', 'Shop\CustomerInfo::index');
$routes->post('shop/confirm', 'Shop\CustomerInfo::confirm');
$routes->post('shop/order', 'Shop\Order::index');

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
