<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$response = ['success' => false, 'error' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents("php://input"), true) ?? $_POST;
        
        // Validasi input
        $requiredFields = ['name', 'email', 'password', 'phone_number'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Field $field harus diisi");
            }
        }

        $userModel = new User();
        
        // Cek apakah email sudah terdaftar
        if ($userModel->getByEmail($data['email'])) {
            throw new Exception("Email sudah terdaftar");
        }

        // Create user dengan role default 'user'
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone_number' => $data['phone_number'],
            'role' => 'user' // Default role
        ];
        
        $user = $userModel->create($userData);

        if (!$user) {
            throw new Exception("Gagal membuat user baru");
        }

        // Siapkan response
        unset($user['password']);
        $response = [
            'success' => true,
            'user' => $user,
            'message' => 'Registrasi berhasil'
        ];
    } else {
        throw new Exception("Method tidak diizinkan", 405);
    }
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
    http_response_code($e->getCode() ?: 400);
}

echo json_encode($response);