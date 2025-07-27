<?php
require_once __DIR__ . '/../models/Product.php';

class ProductController
{
    private $model;

    public function __construct()
    {
        $this->model = new Product();
    }

    public function handleRequest()
    {
        $method = $_SERVER['REQUEST_METHOD'];

        try {
            switch ($method) {
                case 'GET':
                    $id = $_GET['id'] ?? null;
                    if ($id) {
                        $product = $this->model->getById($id);
                        echo json_encode($product);
                    } else {
                        $products = $this->model->getAll();
                        echo json_encode($products);
                    }
                    break;

                case 'POST':
                    $data = $_POST;
                    $requiredFields = ['set_id', 'rarity_id', 'name', 'price', 'stock_quantity'];
                    foreach ($requiredFields as $field) {
                        if (empty($data[$field])) {
                            http_response_code(400);
                            echo json_encode(['error' => "Validasi Gagal: Field '{$field}' tidak boleh kosong."]);
                            return;
                        }
                    }

                    if (isset($_FILES['image_url']) && $_FILES['image_url']['error'] === UPLOAD_ERR_OK) {
                        $targetDir = __DIR__ . '/../uploads/';
                        if (!is_dir($targetDir)) {
                            mkdir($targetDir, 0777, true);
                        }
                        $fileName = uniqid() . '-' . basename($_FILES["image_url"]["name"]);
                        $targetFile = $targetDir . $fileName;
                        if (move_uploaded_file($_FILES["image_url"]["tmp_name"], $targetFile)) {
                            $data['image_url'] = 'http://localhost/api-tcg/uploads/' . $fileName;
                        }
                        if (!move_uploaded_file($_FILES["image_url"]["tmp_name"], $targetFile)) {
                            error_log("Gagal memindahkan file: " . $_FILES["image_url"]["tmp_name"] . " ke " . $targetFile);
                            error_log("Error code: " . $_FILES["image_url"]["error"]);
                        }
                    }

                    if (isset($data['_method']) && $data['_method'] === 'PUT' && isset($data['id'])) {
                        $id = $data['id'];
                        unset($data['_method'], $data['id']);
                        $success = $this->model->update($id, $data);
                        echo json_encode(['success' => $success]);
                    } else {
                        $product = $this->model->create($data);
                        http_response_code(201);
                        echo json_encode(['success' => true, 'data' => $product]);
                    }
                    break;

                case 'DELETE':
                    $id = $_GET['id'] ?? null;
                    if ($id) {
                        $success = $this->model->delete($id);
                        echo json_encode(['success' => $success]);
                    } else {
                        http_response_code(400);
                        echo json_encode(['error' => 'ID is required']);
                    }
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
