<?php
class Set {
    private $db;

    public function __construct() {
        $this->db = getDBConnection();
    }

    public function getAll() {
        $query = "SELECT id, name, slug, code, release_date, created_at, updated_at FROM tb_sets";
        $result = $this->db->query($query);

        $sets = [];
        while ($row = $result->fetch_assoc()) {
            $sets[] = $row;
        }

        return $sets;
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tb_sets WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO tb_sets 
            (name, slug, code, release_date, created_at, updated_at) 
            VALUES (?, ?, ?, ?, NOW(), NOW())
        ");

        $slug = $this->generateSlug($data['name']);
        $stmt->bind_param(
            "ssss", 
            $data['name'],
            $slug,
            $data['code'],
            $data['release_date']
        );

        if ($stmt->execute()) {
            return $this->getById($this->db->insert_id);
        } else {
            return false;
        }
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE tb_sets SET 
                name = ?, 
                slug = ?, 
                code = ?, 
                release_date = ?, 
                updated_at = NOW()
            WHERE id = ?
        ");

        $slug = $this->generateSlug($data['name']);

        $stmt->bind_param(
            "ssssi",
            $data['name'],
            $slug,
            $data['code'],
            $data['release_date'],
            $id
        );

        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM tb_sets WHERE id = ?");
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    private function generateSlug($name) {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    }
}
