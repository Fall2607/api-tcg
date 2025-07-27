<?php
class Rarity {
    private $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    public function getAll() {
        $query = "SELECT id, name, slug, description, created_at, updated_at FROM tb_rarities";
        $result = $this->db->query($query);

        $rarities = [];
        while ($row = $result->fetch_assoc()) {
            $rarities[] = $row;
        }

        return $rarities;
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tb_rarities WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO tb_rarities 
            (name, slug, description, created_at, updated_at) 
            VALUES (?, ?, ?, NOW(), NOW())
        ");

        $slug = $this->generateSlug($data['name']);
        $description = $data['description'] ?? null;

        $stmt->bind_param("sss", $data['name'], $slug, $description);

        if ($stmt->execute()) {
            return $this->getById($this->db->insert_id);
        } else {
            return false;
        }
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE tb_rarities SET 
                name = ?, 
                slug = ?,
                description = ?,
                updated_at = NOW()
            WHERE id = ?
        ");

        $slug = $this->generateSlug($data['name']);
        $description = $data['description'] ?? null;

        $stmt->bind_param("sssi", $data['name'], $slug, $description, $id);

        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM tb_rarities WHERE id = ?");
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    private function generateSlug($name) {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    }
}
