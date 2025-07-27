<?php
class Order {
    private $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    // Ambil semua pesanan (admin)
    public function getAll() {
        $result = $this->db->query("SELECT * FROM tb_orders ORDER BY created_at DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Ambil semua pesanan berdasarkan user_id
    public function getAllByUser($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM tb_orders WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Ambil pesanan berdasarkan ID
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tb_orders WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Ambil pesanan berdasarkan status
    public function getByStatus($status) {
        $stmt = $this->db->prepare("SELECT * FROM tb_orders WHERE status = ?");
        $stmt->bind_param("s", $status);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Buat pesanan baru
    public function create($data) {
        $user_id = $data['user_id'];
        $shipping_address_id = $data['shipping_address_id'];
        $total_amount = $data['total_amount'];
        $status = $data['status'] ?? 'pending';
        $payment_method = $data['payment_method'] ?? null;
        $tracking_number = $data['tracking_number'] ?? null;

        $stmt = $this->db->prepare("
            INSERT INTO tb_orders 
            (user_id, shipping_address_id, total_amount, status, payment_method, tracking_number, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->bind_param(
            "iidsss",
            $user_id,
            $shipping_address_id,
            $total_amount,
            $status,
            $payment_method,
            $tracking_number
        );

        if ($stmt->execute()) {
            return $this->getById($this->db->insert_id);
        }
        return false;
    }


    // Update pesanan secara umum
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE tb_orders SET 
            shipping_address_id = ?, 
            total_amount = ?, 
            status = ?, 
            payment_method = ?, 
            tracking_number = ?, 
            updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->bind_param(
            "idsssi",
            $data['shipping_address_id'],
            $data['total_amount'],
            $data['status'],
            $data['payment_method'],
            $data['tracking_number'],
            $id
        );
        return $stmt->execute();
    }

    // Update hanya status
    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE tb_orders SET status = ?, updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    // Hapus pesanan
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM tb_orders WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
