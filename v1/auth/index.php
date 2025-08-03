<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/User.php';

$response = ['success' => false, 'error' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!$data || !isset($data['email']) || !isset($data['password'])) {
        $response['error'] = 'Email dan password diperlukan';
        echo json_encode($response);
        exit;
    }

    try {
        $userModel = new User();
        $user = $userModel->verifyCredentials($data['email'], $data['password']);
        
        if (!$user) {
            $response['error'] = 'Email atau password salah';
            echo json_encode($response);
            exit;
        }

        // Hapus password sebelum dikirim
        unset($user['password']);
        
        $response = [
            'success' => true,
            'user' => $user,
            'token' => base64_encode(json_encode([
                'user_id' => $user['id'],
                'exp' => time() + (60 * 60 * 24) // 24 jam
            ]))
        ];

    } catch (Exception $e) {
        $response['error'] = 'Terjadi kesalahan server';
    }
}

echo json_encode($response);