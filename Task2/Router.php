<?php
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/NewsController.php';
require __DIR__ . '/NewsModel.php';
require __DIR__ . '/NewsTemplate.php';

use \Bramus\Router\Router as Router;

$router = new Router();

$router->get('/', function ()
{
   header('Location: /create'); exit();
});

$router->get('/create', 'NewsController@create');
$router->post('/create', 'NewsController@create');
$router->get('/delete/id/([0-9]+)', 'NewsController@delete');
$router->get('/news/id/([0-9]+)', 'NewsController@render');
$router->get('/news/q/{q}', 'NewsController@render');

$router->set404(function()
{
   echo 'NotFound';
});

$router->run();