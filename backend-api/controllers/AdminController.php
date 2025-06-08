namespace BackendApi\Controllers;

class AdminController {
    private $pdo;

    public function __construct() {
        global $pdo;
        $this->pdo = $pdo;
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        $data = json_decode(file_get_contents('php://input'), true);
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($username) || empty($password)) {
            http_response_code(400);
            echo json_encode(['error' => 'Username and password are required']);
            return;
        }

        // TODO: Implement actual login logic
        echo json_encode(['message' => 'Login successful']);
    }

    public function getDashboard() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }

        // TODO: Implement dashboard data retrieval
        echo json_encode(['message' => 'Dashboard data retrieved']);
    }
}