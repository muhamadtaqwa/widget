<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ===== Public =====
$routes->get('/', 'Home::index');
$routes->get('preview', 'Home::preview');

// ===== Auth =====
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::login');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::register');
$routes->get('logout', 'Auth::logout');

// ===== Dashboard (auth) =====
$routes->get('dashboard', 'Dashboard::index');

// ===== Journal (auth) =====
$routes->get('journal/edit', 'Journal::edit');
$routes->get('journal/edit/(:num)', 'Journal::edit/$1');
$routes->get('journal/edit/(:num)/builder', 'Journal::builder/$1');
$routes->post('journal/save', 'Journal::save');
$routes->get('journal/preview/(:num)', 'Journal::preview/$1');
$routes->get('journal/delete/(:num)', 'Journal::delete/$1');

// ===== Admin (auth) =====
$routes->get('admin', 'Admin::index');
$routes->get('admin/journals', 'Admin::journals');
$routes->get('admin/approve/(:num)', 'Admin::approve/$1');
$routes->get('admin/ban/(:num)', 'Admin::ban/$1');
$routes->get('admin/delete/(:num)', 'Admin::delete/$1');
$routes->get('admin/delete-journal/(:num)', 'Admin::deleteJournal/$1');

// ===== API =====
$routes->post('api/save-widget', 'Api::saveWidget');
$routes->get('api/fetch', 'Api::fetch');

// ===== Widget Loader (public) =====
$routes->get('widget_loader', 'Widget::loader');

// ===== Widget Templates (public) =====
$routes->get('widgets/statistik', 'Widget::statistik');
$routes->get('widgets/graph', 'Widget::graph');
$routes->get('widgets/scimago', 'Widget::scimago');
$routes->get('widgets/indexing', 'Widget::indexing');
$routes->get('widgets/tools', 'Widget::tools');
$routes->get('widgets/templates', 'Widget::templates');
