<?php
class User {
    private $db;
    
    public function __construct() {
        $this->db = getDBConnection();
    }
    
    public function getAll() {
        $query = "SELECT id, name, email, role, phone_number, created_at FROM users";
        $result = $this->db->query($query);
        
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        
        return $users;
    }
    
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT id, name, email, role, phone_number, created_at FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        return $stmt->get_result()->fetch_assoc();
    }
    
    // public function create($data) {
    //     $stmt = $this->db->prepare("
    //         INSERT INTO users 
    //         (name, email, password, role, phone_number, created_at, updated_at) 
    //         VALUES (?, ?, ?, ?, ?, NOW(), NOW())
    //     ");
        
    //     $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    //     $stmt->bind_param(
    //         "sssss", 
    //         $data['name'],
    //         $data['email'],
    //         $hashedPassword,
    //         $data['role'] ?? 'user',
    //         $data['phone_number']
    //     );
        
    //     return $stmt->execute() ? $this->getById($this->db->insert_id) : false;
    // }
    
    public function create($data) {
        if (
            empty($data['name']) ||
            empty($data['email']) ||
            empty($data['password']) ||
            empty($data['phone_number'])
        ) {
            http_response_code(400);
            echo json_encode(['error' => 'Data tidak lengkap']);
            return false;
        }

        $name = $data['name'];
        $email = $data['email'];
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $role = $data['role'] ?? 'user';
        $phone = $data['phone_number'];

        $stmt = $this->db->prepare("
            INSERT INTO users 
            (name, email, password, role, phone_number, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())
        ");

        $stmt->bind_param("sssss", $name, $email, $password, $role, $phone);

        return $stmt->execute() ? $this->getById($this->db->insert_id) : false;
    }


    public function update($id, $data) {
        $query = "UPDATE users SET ";
        $params = [];
        $types = "";
        
        if (isset($data['name'])) {
            $query .= "name = ?, ";
            $params[] = $data['name'];
            $types .= "s";
        }

        if (isset($data['email'])) {
        $query .= "email = ?, ";
        $params[] = $data['email'];
        $types .= "s";
        }
        
        if (isset($data['role'])) {
            $query .= "role = ?, ";
            $params[] = $data['role'];
            $types .= "s";
        }
        
        if (isset($data['phone_number'])) {
            $query .= "phone_number = ?, ";
            $params[] = $data['phone_number'];
            $types .= "s";
        }
        
        if (isset($data['password'])) {
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $query .= "password = ?, ";
            $params[] = $hashedPassword;
            $types .= "s";
        }
        
        // Tambahkan field lainnya...
        
        $query = rtrim($query, ", ") . " WHERE id = ?";
        $params[] = $id;
        $types .= "i";
        
        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$params);
        
        return $stmt->execute();
    }
    
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}