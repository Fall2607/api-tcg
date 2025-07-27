<?php
class Address {
    private $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM tb_addresses";
        $result = $this->db->query($query);

        $addresses = [];
        while ($row = $result->fetch_assoc()) {
            $addresses[] = $row;
        }

        return $addresses;
    }

    public function getAllByUser($user_id) {
        $stmt = $this->db->prepare("SELECT * FROM tb_addresses WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tb_addresses WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO tb_addresses 
            (user_id, label, recipient_name, phone_number, address_line_1, city, province, postal_code, is_default, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");

        $stmt->bind_param(
            "isssssssi",
            $data['user_id'],
            $data['label'],
            $data['recipient_name'],
            $data['phone_number'],
            $data['address_line_1'],
            $data['city'],
            $data['province'],
            $data['postal_code'],
            $data['is_default']
        );

        if ($stmt->execute()) {
            return $this->getById($this->db->insert_id);
        }

        return false;
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE tb_addresses SET 
                label = ?, 
                recipient_name = ?, 
                phone_number = ?, 
                address_line_1 = ?, 
                city = ?, 
                province = ?, 
                postal_code = ?, 
                is_default = ?, 
                updated_at = NOW() 
            WHERE id = ?
        ");

        $stmt->bind_param(
            "sssssssii",
            $data['label'],
            $data['recipient_name'],
            $data['phone_number'],
            $data['address_line_1'],
            $data['city'],
            $data['province'],
            $data['postal_code'],
            $data['is_default'],
            $id
        );

        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM tb_addresses WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
