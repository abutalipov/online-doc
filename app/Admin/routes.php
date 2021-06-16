<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
    'scheme' => 'https'
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');//главная
    $router->resource('/secret-keys', SecretKeyController::class);//ключи
    $router->resource('/signatures', SignatureController::class);//подписи
    $router->resource('/document-templates', DocumentTemplateController::class);//шаблоны
    $router->resource('/signed-documents', SignedDocumentController::class);//подписанные доки
    $router->resource('/verify', VerifyController::class);//подписанные доки

});
