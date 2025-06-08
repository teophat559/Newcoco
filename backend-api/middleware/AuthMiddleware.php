<?php
namespace App\Middleware;

class AuthMiddleware {
    public function handle($request, $next) {
        session_start();

        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        return $next($request);
    }
}