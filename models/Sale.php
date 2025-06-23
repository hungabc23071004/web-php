<?php
class Sale {
    private $conn;
    private $table = "sales";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($product_id, $quantity, $price, $sale_date, $created_by) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (product_id, quantity, price, sale_date, created_by) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$product_id, $quantity, $price, $sale_date, $created_by]);
    }

    public function getSalesByDateRange($start_date, $end_date) {
        $stmt = $this->conn->prepare("
            SELECT s.*, p.name as product_name, u.full_name as seller_name
            FROM {$this->table} s
            JOIN products p ON s.product_id = p.id
            LEFT JOIN users u ON s.created_by = u.id
            WHERE s.sale_date BETWEEN ? AND ?
            ORDER BY s.sale_date DESC
        ");
        $stmt->execute([$start_date, $end_date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSalesStatistics($start_date, $end_date, $category_id = null) {
        // Tổng doanh số
        $where = "WHERE sale_date BETWEEN ? AND ?";
        $params = [$start_date, $end_date];
        if ($category_id) {
            $where .= " AND p.category_id = ?";
            $params[] = $category_id;
        }
        $stmt = $this->conn->prepare("
            SELECT 
                SUM(s.quantity * s.price) as total_revenue,
                COUNT(*) as total_sales,
                AVG(s.quantity * s.price) as average_sale
            FROM {$this->table} s
            JOIN products p ON s.product_id = p.id
            $where
        ");
        $stmt->execute($params);
        $general_stats = $stmt->fetch(PDO::FETCH_ASSOC);

        // Top sản phẩm bán chạy
        $stmt = $this->conn->prepare("
            SELECT 
                p.name as product_name,
                SUM(s.quantity) as total_quantity,
                SUM(s.quantity * s.price) as total_revenue
            FROM {$this->table} s
            JOIN products p ON s.product_id = p.id
            $where
            GROUP BY s.product_id
            ORDER BY total_quantity DESC
            LIMIT 5
        ");
        $stmt->execute($params);
        $top_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Doanh số theo ngày
        $stmt = $this->conn->prepare("
            SELECT 
                sale_date,
                SUM(s.quantity * s.price) as daily_revenue,
                COUNT(*) as daily_sales
            FROM {$this->table} s
            JOIN products p ON s.product_id = p.id
            $where
            GROUP BY sale_date
            ORDER BY sale_date
        ");
        $stmt->execute($params);
        $daily_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'general' => $general_stats,
            'top_products' => $top_products,
            'daily_stats' => $daily_stats
        ];
    }

    public function getProductPerformance($product_id, $start_date, $end_date) {
        $stmt = $this->conn->prepare("
            SELECT 
                sale_date,
                SUM(quantity) as total_quantity,
                SUM(quantity * price) as total_revenue
            FROM {$this->table}
            WHERE product_id = ? AND sale_date BETWEEN ? AND ?
            GROUP BY sale_date
            ORDER BY sale_date
        ");
        $stmt->execute([$product_id, $start_date, $end_date]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Doanh số theo danh mục
    public function getRevenueByCategory($start_date, $end_date, $category_id = null) {
        $where = "WHERE s.sale_date BETWEEN ? AND ?";
        $params = [$start_date, $end_date];
        if ($category_id) {
            $where .= " AND p.category_id = ?";
            $params[] = $category_id;
        }
        $stmt = $this->conn->prepare("
            SELECT c.name as category_name, SUM(s.quantity * s.price) as revenue
            FROM {$this->table} s
            JOIN products p ON s.product_id = p.id
            JOIN categories c ON p.category_id = c.id
            $where
            GROUP BY c.id
        ");
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Doanh số theo tháng trong năm
    public function getRevenueByMonth($year, $category_id = null) {
        $where = "WHERE YEAR(s.sale_date) = ?";
        $params = [$year];
        if ($category_id) {
            $where .= " AND p.category_id = ?";
            $params[] = $category_id;
        }
        $stmt = $this->conn->prepare("
            SELECT MONTH(s.sale_date) as month, SUM(s.quantity * s.price) as revenue
            FROM {$this->table} s
            JOIN products p ON s.product_id = p.id
            $where
            GROUP BY MONTH(s.sale_date)
            ORDER BY month
        ");
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRevenueByProduct($start_date, $end_date, $category_id) {
        $stmt = $this->conn->prepare("
            SELECT p.name as product_name, SUM(s.quantity * s.price) as revenue
            FROM {$this->table} s
            JOIN products p ON s.product_id = p.id
            WHERE s.sale_date BETWEEN ? AND ? AND p.category_id = ?
            GROUP BY s.product_id
        ");
        $stmt->execute([$start_date, $end_date, $category_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?> 