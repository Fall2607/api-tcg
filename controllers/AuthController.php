<?php
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $model;
    
    public function __construct() {
        $this->model = new User();
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        try {
            switch ($method) {
                case 'POST':
                    $this->handleLogin();
                    break;
                    
                default:
                    http_response_code(405);
                    echo json_encode(['error' => 'Method not allowed']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
    
    private function handleLogin() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data || !isset($data['email']) || !isset($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Email and password are required']);
            return;
        }
        
        $user = $this->model->verifyCredentials($data['email'], $data['password']);
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
            return;
        }
        
        // Remove sensitive data before sending response
        unset($user['password']);
        
        // You can generate a JWT token here if you want
        // For simplicity, we'll just return the user data
        echo json_encode([
            'success' => true,
            'user' => $user,
            'token' => $this->generateToken($user['id'])
        ]);
    }
    
    private function generateToken($userId) {
        // Simple token generation - consider using JWT in production
        return base64_encode(json_encode([
            'user_id' => $userId,
            'exp' => time() + (60 * 60 * 24) // 24 hours expiration
        ]));
    }
}