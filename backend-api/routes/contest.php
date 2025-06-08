<?php
use App\Controllers\ContestController;

// Contest routes
$router->get('/contests', [ContestController::class, 'index']);
$router->get('/contests/{id}', [ContestController::class, 'show']);
$router->post('/contests', [ContestController::class, 'create']);
$router->put('/contests/{id}', [ContestController::class, 'update']);
$router->delete('/contests/{id}', [ContestController::class, 'delete']);
$router->get('/contests/{id}/stats', [ContestController::class, 'getStats']);
$router->get('/contests/{id}/top-contestants', [ContestController::class, 'getTopContestants']);