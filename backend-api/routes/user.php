<?php
use BackendApi\Controllers\UserController;

// User routes
$router->get('/users', [UserController::class, 'index']);
$router->get('/users/{id}', [UserController::class, 'show']);
$router->post('/users', [UserController::class, 'create']);
$router->put('/users/{id}', [UserController::class, 'update']);
$router->delete('/users/{id}', [UserController::class, 'delete']);
$router->get('/users/{id}/activity', [UserController::class, 'getActivity']);
$router->get('/users/{id}/stats', [UserController::class, 'getStats']);
$router->post('/users/login', [UserController::class, 'login']);
$router->post('/users/register', [UserController::class, 'register']);
$router->post('/users/logout', [UserController::class, 'logout']);
$router->post('/users/forgot-password', [UserController::class, 'forgotPassword']);
$router->post('/users/reset-password', [UserController::class, 'resetPassword']);