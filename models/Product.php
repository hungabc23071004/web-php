<?php
class Product {
    private $conn;
    private $table = "products";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($page = 1, $per_page = 10, $search = '', $category_id = null, $sort_by = 'created_at', $sort_order = 'DESC') {
        $offset = ($page - 1) * $per_page;
        
        $where = [];
        $params = [];
        
        if (!empty($search)) {
            $where[] = "(p.name LIKE ? OR p.description LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }
        
        if ($category_id !== null && $category_id !== '') {
            $where[] = "p.category_id = ?";
            $params[] = $category_id;
        }
        
        $where_clause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
        
        // Validate sort_by to prevent SQL injection
        $allowed_sort_columns = ['name', 'price', 'created_at', 'quantity'];
        $sort_by = in_array($sort_by, $allowed_sort_columns) ? $sort_by : 'created_at';
        
        // Validate sort_order
        $sort_order = strtoupper($sort_order) === 'ASC' ? 'ASC' : 'DESC';
        
        $order_by = "ORDER BY $sort_by $sort_order";
        
        // Lấy tổng số sản phẩm
        $count_sql = "SELECT COUNT(*) as total FROM {$this->table} p $where_clause";
        $count_stmt = $this->conn->prepare($count_sql);
        $count_stmt->execute($params);
        $total = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Lấy danh sách sản phẩm
        $sql = "SELECT p.*, c.name as category_name 
                FROM {$this->table} p 
                LEFT JOIN categories c ON p.category_id = c.id 
                $where_clause 
                $order_by 
                LIMIT $per_page OFFSET $offset";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        
        return [
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
            'total_pages' => ceil($total / $per_page)
        ];
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($name, $description, $price, $quantity, $image = null, $category_id = null) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (name, description, price, quantity, image, category_id) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$name, $description, $price, $quantity, $image, $category_id]);
    }

    public function update($id, $name, $description, $price, $quantity, $image = null, $category_id = null) {
        if ($image) {
            $stmt = $this->conn->prepare("UPDATE {$this->table} SET name=?, description=?, price=?, quantity=?, image=?, category_id=? WHERE id=?");
            return $stmt->execute([$name, $description, $price, $quantity, $image, $category_id, $id]);
        } else {
            $stmt = $this->conn->prepare("UPDATE {$this->table} SET name=?, description=?, price=?, quantity=?, category_id=? WHERE id=?");
            return $stmt->execute([$name, $description, $price, $quantity, $category_id, $id]);
        }
    }

    public function delete($id) {
        // Lấy thông tin sản phẩm để xóa ảnh
        $product = $this->getById($id);
        if ($product && $product['image']) {
            $image_path = __DIR__ . '/../uploads/' . $product['image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function getStatistics() {
        $stats = [];
        
        // Tổng số sản phẩm
        $stmt = $this->conn->query("SELECT COUNT(*) as total FROM {$this->table}");
        $stats['total_products'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Tổng giá trị hàng tồn kho
        $stmt = $this->conn->query("SELECT SUM(price * quantity) as total_value FROM {$this->table}");
        $stats['total_value'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_value'];
        
        // Sản phẩm có số lượng thấp (dưới 10)
        $stmt = $this->conn->query("SELECT COUNT(*) as low_stock FROM {$this->table} WHERE quantity < 10");
        $stats['low_stock'] = $stmt->fetch(PDO::FETCH_ASSOC)['low_stock'];
        
       
        return $stats;
    }

    // Thống kê số lượng sản phẩm theo danh mục
    public function getProductCountByCategory($category_id = null) {
        $where = '';
        $params = [];
        if ($category_id) {
            $where = 'WHERE c.id = ?';
            $params[] = $category_id;
        }
        $sql = "SELECT c.name as category_name, COUNT(p.id) as product_count
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                $where
                GROUP BY c.id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thống kê tồn kho từng sản phẩm
    public function getAllProductStock($category_id = null) {
        $where = '';
        $params = [];
        if ($category_id) {
            $where = 'WHERE p.category_id = ?';
            $params[] = $category_id;
        }
        $sql = "SELECT p.name as product_name, c.name as category_name, p.quantity
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                $where
                ORDER BY p.name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCategory($category_id) {
        $stmt = $this->conn->prepare("SELECT * FROM products WHERE category_id = ?");
        $stmt->execute([$category_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFeatured() {
        $stmt = $this->conn->query("SELECT * FROM products LIMIT 8"); // hoặc điều kiện nổi bật
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLowStockProducts() {
        $sql = "SELECT p.*, c.name as category_name FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.quantity < 10";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
