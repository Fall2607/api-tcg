<?php
class Order {
    private $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM tb_orders_simple";
        $result = $this->db->query($query);

        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }

        return $orders;
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tb_orders_simple WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function getByUserId($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM tb_orders_simple WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();

        $result = $stmt->get_result();
        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        return $orders;
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO tb_orders_simple 
            (user_id, recipient_name, phone_number, address, city, postal_code, 
             product_id, product_name, quantity, price_per_unit, total_price, 
             payment_method, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "isssssisiiiss",
            $data['user_id'],
            $data['recipient_name'],
            $data['phone_number'],
            $data['address'],
            $data['city'],
            $data['postal_code'],
            $data['product_id'],
            $data['product_name'],
            $data['quantity'],
            $data['price_per_unit'],
            $data['total_price'],
            $data['payment_method'],
            $data['status']
        );

        if ($stmt->execute()) {
            return $this->getById($this->db->insert_id);
        } else {
            throw new Exception("Failed to create order: " . $stmt->error);
        }
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE tb_orders_simple SET 
                recipient_name = ?,
                phone_number = ?,
                address = ?,
                city = ?,
                postal_code = ?,
                product_id = ?,
                product_name = ?,
                quantity = ?,
                price_per_unit = ?,
                total_price = ?,
                payment_method = ?,
                status = ?
            WHERE id = ?
        ");

        $stmt->bind_param(
            "sssssisiiissi",
            $data['recipient_name'],
            $data['phone_number'],
            $data['address'],
            $data['city'],
            $data['postal_code'],
            $data['product_id'],
            $data['product_name'],
            $data['quantity'],
            $data['price_per_unit'],
            $data['total_price'],
            $data['payment_method'],
            $data['status'],
            $id
        );

        if (!$stmt->execute()) {
            throw new Exception("Failed to update order: " . $stmt->error);
        }
        
        return $this->getById($id);
    }

    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("
            UPDATE tb_orders_simple SET 
                status = ?
            WHERE id = ?
        ");

        $stmt->bind_param("si", $status, $id);

        if (!$stmt->execute()) {
            throw new Exception("Failed to update order status: " . $stmt->error);
        }
        
        return $this->getById($id);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM tb_orders_simple WHERE id = ?");
        $stmt->bind_param("i", $id);

        if (!$stmt->execute()) {
            throw new Exception("Failed to delete order: " . $stmt->error);
        }
        
        return true;
    }
}