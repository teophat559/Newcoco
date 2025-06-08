<?php
require_once __DIR__ . '/../bootstrap.php';

// Admin middleware
$router->group(['middleware' => 'admin'], function($router) {
    // Contest management
    $router->get('/admin/contests', 'AdminController@contests');
    $router->get('/admin/contests/{id}', 'AdminController@contest');
    $router->post('/admin/contests', 'AdminController@createContest');
    $router->put('/admin/contests/{id}', 'AdminController@updateContest');
    $router->delete('/admin/contests/{id}', 'AdminController@deleteContest');
    $router->get('/admin/contests/{id}/stats', 'AdminController@contestStats');

    // Contestant management
    $router->get('/admin/contestants', 'AdminController@contestants');
    $router->get('/admin/contestants/{id}', 'AdminController@contestant');
    $router->post('/admin/contestants', 'AdminController@createContestant');
    $router->put('/admin/contestants/{id}', 'AdminController@updateContestant');
    $router->delete('/admin/contestants/{id}', 'AdminController@deleteContestant');

    // User management
    $router->get('/admin/users', 'AdminController@users');
    $router->get('/admin/users/{id}', 'AdminController@user');
    $router->post('/admin/users', 'AdminController@createUser');
    $router->put('/admin/users/{id}', 'AdminController@updateUser');
    $router->delete('/admin/users/{id}', 'AdminController@deleteUser');
    $router->get('/admin/users/{id}/activity', 'AdminController@userActivity');

    // Notification management
    $router->get('/admin/notifications', 'AdminController@notifications');
    $router->get('/admin/notifications/{id}', 'AdminController@notification');
    $router->post('/admin/notifications', 'AdminController@createNotification');
    $router->put('/admin/notifications/{id}', 'AdminController@updateNotification');
    $router->delete('/admin/notifications/{id}', 'AdminController@deleteNotification');

    // Settings management
    $router->get('/admin/settings', 'AdminController@settings');
    $router->put('/admin/settings', 'AdminController@updateSettings');
    $router->get('/admin/settings/history', 'AdminController@settingsHistory');
    $router->get('/admin/settings/stats', 'AdminController@settingsStats');
});