<?php


$router->group([], function () use ($router) {
    $router->post('/', 'ExportsController@store');
});