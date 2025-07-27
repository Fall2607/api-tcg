<?php
require_once __DIR__ . '/../models/Address.php';

class AddressController {
    private $model;
    
    public function __construct() {
        $this->model = new Address();
    }
    
    public function handleRequest() {
        $method = $_SERVER['REQUEST_METHOD'];
        
        try {
            switch ($method) {
                case 'GET':
                    $id = $_GET['id'] ?? null;
                    if ($id) {
                        $Address = $this->model->getById($id);
                        echo json_encode($Address);
                    } else {
                        $Addresss = $this->model->getAll();
                        echo json_encode($Addresss);
                    }
                    break;
                    
                case 'POST':
                    $data = json_decode(file_get_contents("php://input"), true);
                    if (!$data) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Invalid JSON or empty body']);
                        return;
                    }
                    $Address = $this->model->create($data);
                    http_response_code(201);
                    echo json_encode(['success' => true, 'data' => $Address]);
                    break;
                    
                case 'PUT':
                    $id = $_GET['id'] ?? null;
                    if (!$id) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing ID in query']);
                        return;
                    }
                    $data = json_decode(file_get_contents("php://input"), true);
                    $success = $this->model->update($id, $data);
                    echo json_encode(['success' => $success]);
                    break;
                    
                case 'DELETE':
                    $id = $_GET['id'] ?? null;
                    if (!$id) {
                        http_response_code(400);
                        echo json_encode(['error' => 'Missing ID in query']);
                        return;
                    }
                    $success = $this->model->delete($id);
                    echo json_encode(['success' => $success]);
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
}
