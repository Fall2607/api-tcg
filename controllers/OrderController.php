<?php
require_once __DIR__ . '/../models/Order.php';

class OrderController
{
    private $model;

    public function __construct()
    {
        $this->model = new Order();
    }

    public function handleRequest()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        try {
            switch ($method) {
                case 'GET':
                    $this->handleGetRequest();
                    break;

                case 'POST':
                    $this->handlePostRequest();
                    break;

                case 'PUT':
                    $this->handlePutRequest();
                    break;

                case 'DELETE':
                    $this->handleDeleteRequest();
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

    private function handleGetRequest()
    {
        $id = $_GET['id'] ?? null;
        $user_id = $_GET['user_id'] ?? null;

        if ($id) {
            $order = $this->model->getById($id);
            echo json_encode($order);
        } elseif ($user_id) {
            $orders = $this->model->getByUserId($user_id);
            echo json_encode($orders);
        } else {
            $orders = $this->model->getAll();
            echo json_encode($orders);
        }
    }

    private function handlePostRequest()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON or empty body']);
            return;
        }

        // Set default status if not provided
        if (!isset($data['status'])) {
            $data['status'] = 'pending';
        }

        $order = $this->model->create($data);
        http_response_code(201);
        echo json_encode($order);
    }

    private function handlePutRequest()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing ID in query']);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        if (!$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON or empty body']);
            return;
        }

        // Special handling for status-only updates
        if (isset($data['status']) && count($data) === 1) {
            $order = $this->model->updateStatus($id, $data['status']);
        } else {
            $order = $this->model->update($id, $data);
        }

        echo json_encode($order);
    }

    private function handleDeleteRequest()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing ID in query']);
            return;
        }

        $success = $this->model->delete($id);
        echo json_encode(['success' => $success]);
    }
}
