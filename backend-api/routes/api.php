<?php
require_once __DIR__ . '/../bootstrap.php';

// Contest routes
$router->get('/contests', 'ContestController@index');
$router->get('/contests/{id}', 'ContestController@show');
$router->post('/contests', 'ContestController@create');
$router->put('/contests/{id}', 'ContestController@update');
$router->delete('/contests/{id}', 'ContestController@delete');
$router->get('/contests/{id}/stats', 'ContestController@stats');

// Contestant routes
$router->get('/contestants', 'ContestantController@index');
$router->get('/contestants/{id}', 'ContestantController@show');
$router->post('/contestants', 'ContestantController@create');
$router->put('/contestants/{id}', 'ContestantController@update');
$router->delete('/contestants/{id}', 'ContestantController@delete');
$router->post('/contestants/{id}/vote', 'ContestantController@vote');

// User routes
$router->post('/auth/login', 'UserController@login');
$router->post('/auth/register', 'UserController@register');
$router->post('/auth/logout', 'UserController@logout');
$router->get('/auth/me', 'UserController@me');
$router->put('/auth/profile', 'UserController@updateProfile');

// Notification routes
$router->get('/notifications', 'NotificationController@index');
$router->get('/notifications/{id}', 'NotificationController@show');
$router->post('/notifications', 'NotificationController@create');
$router->put('/notifications/{id}', 'NotificationController@update');
$router->delete('/notifications/{id}', 'NotificationController@delete');
$router->get('/notifications/unread', 'NotificationController@getUnread');
$router->post('/notifications/{id}/read', 'NotificationController@markAsRead');

// Activity routes
$router->get('/activities', 'ActivityController@index');
$router->get('/activities/{id}', 'ActivityController@show');
$router->get('/activities/user/{userId}', 'ActivityController@getUserActivities');

// Media routes
$router->post('/media/upload', 'MediaController@upload');
$router->delete('/media/{id}', 'MediaController@delete');
$router->get('/media/{id}', 'MediaController@show');