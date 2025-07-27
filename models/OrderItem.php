<?php
class OrderItem {
    private $db;

    public function __construct() {
        $this->db = getDBConnection(); // Pastikan fungsi getDBConnection() sudah tersedia
    }

    // Ambil semua data order item
    public function getAll() {
        $query = "SELECT * FROM tb_order_items";
        $result = $this->db->query($query);

        $orderItems = [];
        while ($row = $result->fetch_assoc()) {
            $orderItems[] = $row;
        }

        return $orderItems;
    }

    // Ambil order item berdasarkan ID
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tb_order_items WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Ambil semua order item berdasarkan order_id (untuk menampilkan isi pesanan tertentu)
    public function getByOrderId($order_id) {
        $stmt = $this->db->prepare("SELECT * FROM tb_order_items WHERE order_id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Tambahkan order item baru
    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO tb_order_items 
            (order_id, product_id, quantity, price_per_unit, created_at, updated_at) 
            VALUES (?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->bind_param(
            "iiid",
            $data['order_id'],
            $data['product_id'],
            $data['quantity'],
            $data['price_per_unit']
        );

        if ($stmt->execute()) {
            return [
                'id' => $stmt->insert_id,
                'order_id' => $data['order_id'],
                'product_id' => $data['product_id'],
                'quantity' => $data['quantity'],
                'price_per_unit' => $data['price_per_unit']
            ];
        } else {
            throw new Exception("Gagal menyimpan order item: " . $stmt->error);
        }
    }

    // Update order item berdasarkan ID
    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE tb_order_items SET 
                order_id = ?, 
                product_id = ?, 
                quantity = ?, 
                price_per_unit = ?, 
                updated_at = NOW() 
            WHERE id = ?
        ");
        $stmt->bind_param(
            "iiidi",
            $data['order_id'],
            $data['product_id'],
            $data['quantity'],
            $data['price_per_unit'],
            $id
        );

        return $stmt->execute();
    }

    // Hapus order item berdasarkan ID
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM tb_order_items WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // Hapus semua order item berdasarkan order_id (misalnya jika order dihapus)
    public function deleteByOrderId($order_id) {
        $stmt = $this->db->prepare("DELETE FROM tb_order_items WHERE order_id = ?");
        $stmt->bind_param("i", $order_id);
        return $stmt->execute();
    }
}
