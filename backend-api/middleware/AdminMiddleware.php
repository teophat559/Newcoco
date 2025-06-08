<?php
namespace App\Middleware;

use App\Controllers\BaseController;

class AdminMiddleware extends BaseController {
    public function handle($request, $next) {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $this->jsonResponse([
                'error' => 'Access denied. Admin privileges required.'
            ], 403);
        }

        return $next($request);
    }
}