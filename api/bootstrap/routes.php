<?php

use App\Controller\UsersController;
use App\Controller\RolesController;
use App\Controller\OrdersController;
use App\Controller\SessionsController;
use Illuminate\Auth\Middleware\Authenticate as AuthenticateMiddleware;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Setup The Application Router
|--------------------------------------------------------------------------
*/

Route::pattern('id', '[1-9][0-9]*');

Route::middleware([
    AuthenticateMiddleware::class,
])->group(function ($router) {
    $router->get('/users', [UsersController::class, 'search']);
    $router->get('/users/show/{id}', [UsersController::class, 'show']);
    $router->post('/users/create', [UsersController::class, 'create']);
    $router->post('/users/edit/{id}', [UsersController::class, 'edit']);
    $router->post('/users/delete/{id}', [UsersController::class, 'delete']);

    $router->get('/roles', [RolesController::class, 'search']);
    $router->get('/roles/show/{id}', [RolesController::class, 'show']);
    $router->post('/roles/create', [RolesController::class, 'create']);
    $router->post('/roles/edit/{id}', [RolesController::class, 'edit']);
    $router->post('/roles/delete/{id}', [RolesController::class, 'delete']);

    $router->get('/orders', [OrdersController::class, 'search']);
    $router->get('/orders/show/{id}', [OrdersController::class, 'show']);
    $router->post('/orders/create', [OrdersController::class, 'create']);
    $router->post('/orders/edit/{id}', [OrdersController::class, 'edit']);
    $router->post('/orders/apply/{id}', [OrdersController::class, 'apply']);
    $router->post('/orders/approve/{id}', [OrdersController::class, 'approve']);
    $router->post('/orders/reject/{id}', [OrdersController::class, 'reject']);
    $router->post('/orders/cancel/{id}', [OrdersController::class, 'cancel']);
    $router->post('/orders/delete/{id}', [OrdersController::class, 'delete']);
});

Route::post('/login', [SessionsController::class, 'login']);
Route::post('/logout', [SessionsController::class, 'logout']);
