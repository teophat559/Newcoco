<?php
require_once __DIR__ . '/config.php';

class SystemTest {
    private $db;
    private $testResults = [];
    private $adminToken;
    private $userToken;

    public function __construct($db) {
        $this->db = $db;
    }

    public function runAllTests() {
        echo "Starting system tests...\n\n";

        $this->testAuthentication();
        $this->testContests();
        $this->testVoting();
        $this->testAdminFunctions();
        $this->testSecurity();
        $this->testErrorHandling();

        $this->printResults();
    }

    private function testAuthentication() {
        echo "Testing Authentication...\n";

        // Test login with invalid credentials
        $this->testLogin('invalid', 'invalid', false);

        // Test login with valid user
        $this->testLogin(TEST_USERNAME, TEST_PASSWORD, true);
        $this->userToken = $this->getLastToken();

        // Test login with admin
        $this->testLogin(TEST_ADMIN_USERNAME, TEST_ADMIN_PASSWORD, true);
        $this->adminToken = $this->getLastToken();

        // Test token validation
        $this->testTokenValidation($this->userToken, true);
        $this->testTokenValidation('invalid_token', false);
    }

    private function testContests() {
        echo "\nTesting Contests...\n";

        // Test get contests
        $this->testGetContests();

        // Test create contest (admin only)
        $this->testCreateContest($this->adminToken, true);
        $this->testCreateContest($this->userToken, false);

        // Test get contest details
        $this->testGetContestDetails(1);
    }

    private function testVoting() {
        echo "\nTesting Voting...\n";

        // Test submit vote
        $this->testSubmitVote($this->userToken, 1, 1, true);

        // Test duplicate vote
        $this->testSubmitVote($this->userToken, 1, 1, false);

        // Test vote on ended contest
        $this->testSubmitVote($this->userToken, 3, 1, false);
    }

    private function testAdminFunctions() {
        echo "\nTesting Admin Functions...\n";

        // Test update contest
        $this->testUpdateContest($this->adminToken, 1, true);
        $this->testUpdateContest($this->userToken, 1, false);

        // Test delete contest
        $this->testDeleteContest($this->adminToken, 2, true);
        $this->testDeleteContest($this->userToken, 1, false);
    }

    private function testSecurity() {
        echo "\nTesting Security...\n";

        // Test rate limiting
        $this->testRateLimiting();

        // Test CORS
        $this->testCORS();

        // Test SQL injection
        $this->testSQLInjection();
    }

    private function testErrorHandling() {
        echo "\nTesting Error Handling...\n";

        // Test invalid request
        $this->testInvalidRequest();

        // Test server error
        $this->testServerError();
    }

    private function testLogin($username, $password, $shouldSucceed) {
        $response = $this->makeRequest('POST', '/auth/login.php', [
            'username' => $username,
            'password' => $password
        ]);

        $this->addTestResult(
            "Login with $username",
            $shouldSucceed ? $response['status'] === 200 : $response['status'] === 401
        );
    }

    private function getLastToken() {
        $stmt = $this->db->query("SELECT TOP 1 token FROM user_tokens ORDER BY created_at DESC");
        return $stmt->fetchColumn();
    }

    private function testTokenValidation($token, $shouldSucceed) {
        $response = $this->makeRequest('GET', '/auth/validate.php', null, $token);
        $this->addTestResult(
            "Validate token",
            $shouldSucceed ? $response['status'] === 200 : $response['status'] === 401
        );
    }

    private function testGetContests() {
        $response = $this->makeRequest('GET', '/contests.php');
        $this->addTestResult(
            "Get contests",
            $response['status'] === 200 && !empty($response['data'])
        );
    }

    private function testCreateContest($token, $shouldSucceed) {
        $response = $this->makeRequest('POST', '/contests.php', [
            'title' => 'Test Contest',
            'description' => 'Test Description',
            'start_date' => date('Y-m-d H:i:s'),
            'end_date' => date('Y-m-d H:i:s', strtotime('+7 days'))
        ], $token);

        $this->addTestResult(
            "Create contest",
            $shouldSucceed ? $response['status'] === 200 : $response['status'] === 403
        );
    }

    private function testGetContestDetails($contestId) {
        $response = $this->makeRequest('GET', "/contests.php?id=$contestId");
        $this->addTestResult(
            "Get contest details",
            $response['status'] === 200 && !empty($response['data'])
        );
    }

    private function testSubmitVote($token, $contestId, $contestantId, $shouldSucceed) {
        $response = $this->makeRequest('POST', '/votes.php', [
            'contest_id' => $contestId,
            'contestant_id' => $contestantId
        ], $token);

        $this->addTestResult(
            "Submit vote for contest $contestId",
            $shouldSucceed ? $response['status'] === 200 : $response['status'] === 400
        );
    }

    private function testUpdateContest($token, $contestId, $shouldSucceed) {
        $response = $this->makeRequest('PUT', "/contests.php?id=$contestId", [
            'title' => 'Updated Contest',
            'description' => 'Updated Description'
        ], $token);

        $this->addTestResult(
            "Update contest $contestId",
            $shouldSucceed ? $response['status'] === 200 : $response['status'] === 403
        );
    }

    private function testDeleteContest($token, $contestId, $shouldSucceed) {
        $response = $this->makeRequest('DELETE', "/contests.php?id=$contestId", null, $token);
        $this->addTestResult(
            "Delete contest $contestId",
            $shouldSucceed ? $response['status'] === 200 : $response['status'] === 403
        );
    }

    private function testRateLimiting() {
        $responses = [];
        for ($i = 0; $i < 101; $i++) {
            $responses[] = $this->makeRequest('GET', '/contests.php');
        }

        $this->addTestResult(
            "Rate limiting",
            $responses[100]['status'] === 429
        );
    }

    private function testCORS() {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, API_URL . '/contests.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Origin: http://localhost:3000']);
        curl_exec($ch);
        $headers = curl_getinfo($ch);
        curl_close($ch);

        $this->addTestResult(
            "CORS headers",
            isset($headers['access-control-allow-origin'])
        );
    }

    private function testSQLInjection() {
        $response = $this->makeRequest('GET', "/contests.php?id=1' OR '1'='1");
        $this->addTestResult(
            "SQL injection prevention",
            $response['status'] === 400
        );
    }

    private function testInvalidRequest() {
        $response = $this->makeRequest('POST', '/contests.php', [
            'invalid' => 'data'
        ]);
        $this->addTestResult(
            "Invalid request handling",
            $response['status'] === 400
        );
    }

    private function testServerError() {
        $response = $this->makeRequest('GET', '/error.php');
        $this->addTestResult(
            "Server error handling",
            $response['status'] === 500
        );
    }

    private function makeRequest($method, $endpoint, $data = null, $token = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, API_URL . $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        if ($token) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json'
            ]);
        }

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return [
            'status' => $status,
            'data' => json_decode($response, true)
        ];
    }

    private function addTestResult($testName, $passed) {
        $this->testResults[] = [
            'name' => $testName,
            'passed' => $passed
        ];
    }

    private function printResults() {
        echo "\nTest Results:\n";
        echo "=============\n";

        $total = count($this->testResults);
        $passed = count(array_filter($this->testResults, function($result) {
            return $result['passed'];
        }));

        foreach ($this->testResults as $result) {
            echo $result['name'] . ': ' . ($result['passed'] ? 'PASSED' : 'FAILED') . "\n";
        }

        echo "\nSummary: $passed/$total tests passed\n";
    }
}

// Run tests
$test = new SystemTest($db);
$test->runAllTests();