<?php
class Product
{
    private $db;

    public function __construct()
    {
        $this->db = getDBConnection();
    }

    public function getAll()
    {
        $query = "
        SELECT 
            p.*, 
            s.name AS set_name, 
            s.code AS set_code,
            s.slug AS set_slug,
            r.name AS rarity_name,
            r.slug AS rarity_slug
        FROM tb_products p
        JOIN tb_sets s ON p.set_id = s.id
        JOIN tb_rarities r ON p.rarity_id = r.id
    ";

        $result = $this->db->query($query);

        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }

        return $products;
    }


    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM tb_products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO tb_products 
            (set_id, rarity_id, name, slug, description, price, stock_quantity, card_condition, image_url, sku, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");

        $slug = $this->generateSlug($data['name']);
        $image_url = $data['image_url'] ?? null;
        $description = $data['description'] ?? null;
        $sku = $data['sku'] ?? null;
        $card_condition = $data['card_condition'] ?? 'Mint';

        $stmt->bind_param(
            "iisssdisss",
            $data['set_id'],
            $data['rarity_id'],
            $data['name'],
            $slug,
            $description,
            $data['price'],
            $data['stock_quantity'],
            $card_condition,
            $image_url,
            $sku
        );

        if ($stmt->execute()) {
            return $this->getById($this->db->insert_id);
        } else {
            return false;
        }
    }

    public function update($id, $data)
    {
        // Bangun query secara dinamis
        $fields = [];
        $params = [];
        $types = '';

        // Daftar kolom yang diizinkan untuk diupdate
        $allowed_fields = ['set_id', 'rarity_id', 'name', 'description', 'price', 'stock_quantity', 'card_condition', 'image_url', 'sku'];

        if (isset($data['name'])) {
            $data['slug'] = $this->generateSlug($data['name']);
            $allowed_fields[] = 'slug';
        }

        foreach ($allowed_fields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
                // Tambahkan tipe data ke string $types
                if (in_array($field, ['set_id', 'rarity_id', 'stock_quantity'])) {
                    $types .= 'i'; // integer
                } elseif ($field === 'price') {
                    $types .= 'd'; // double
                } else {
                    $types .= 's'; // string
                }
            }
        }

        if (empty($fields)) {
            return false; // Tidak ada yang diupdate
        }

        $fields_string = implode(', ', $fields);
        $query = "UPDATE tb_products SET $fields_string, updated_at = NOW() WHERE id = ?";

        // Tambahkan id ke akhir parameter
        $params[] = $id;
        $types .= 'i';

        $stmt = $this->db->prepare($query);
        $stmt->bind_param($types, ...$params);

        return $stmt->execute();
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM tb_products WHERE id = ?");
        $stmt->bind_param("i", $id);

        return $stmt->execute();
    }

    private function generateSlug($name)
    {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    }
}
