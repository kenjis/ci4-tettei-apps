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
$routes->setAutoRoute(false);
$routes->setPrioritize();

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
