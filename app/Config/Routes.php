<?php

use CodeIgniter\Router\RouteCollection;
use App\Storage\MinioStorage;

/**
 * @var RouteCollection $routes
 */

 $routes->get('/minio', function(){
   $minio = new MinioStorage();

//    $minio->putJSON('{"name": "John", "age": 30}', 'test');

    echo '<pre>';
    print_r($minio->listObjects('pr-articles'));
    echo '</pre>';

 });
$routes->get('/', 'Home::index');
$routes->get('/articles', 'ArticleController::index');
$routes->get('/articles/create', 'ArticleController::create');
$routes->post('/articles/store', 'ArticleController::store');
$routes->get('/articles/edit/(:num)', 'ArticleController::edit/$1');
$routes->post('/articles/update/(:num)', 'ArticleController::update/$1');
$routes->get('/articles/delete/(:num)', 'ArticleController::delete/$1');
$routes->get('/articles/show/(:num)', 'ArticleController::show/$1');

$routes->get("api/articles", "ArticleController::api");
$routes->get("api/articles/(:num)", "ArticleController::api/$1");
