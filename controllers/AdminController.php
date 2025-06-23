<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Sale.php';
require_once __DIR__ . '/../config/database.php';

class AdminController {
    private $db;
    private $product;
    private $user;
    private $sale;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->product = new Product($this->db);
        $this->user = new User($this->db);
        $this->sale = new Sale($this->db);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'];
            $password = $_POST['password'];

            $user = $this->user->login($username, $password);
            if ($user) {
                $_SESSION['user'] = $user;
                header('Location: admin/index.php');
                exit();
            } else {
                $_SESSION['error'] = 'Tên đăng nhập hoặc mật khẩu không đúng!';
                header('Location: index.php?action=login');
                exit();
            }
        }
        require_once __DIR__ . '/../views/admin/login.php';
    }

    public function logout() {
        session_destroy();
        header('Location: /web-3/index.php');
        exit();
    }

    public function index() {
        // Kiểm tra đăng nhập
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;
        $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'created_at';
        $sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'DESC';
        
        $result = $this->product->getAll($page, 10, $search, $category_id, $sort_by, $sort_order);
        $products = $result['data'];
        $total_pages = $result['total_pages'];
        
        // Lấy thống kê
        $stats = $this->product->getStatistics();

        // Lấy danh sách danh mục cho bộ lọc
        require_once __DIR__ . '/../models/Category.php';
        $categoryModel = new Category($this->db);
        $categories = $categoryModel->getAll();

        require_once __DIR__ . '/../views/admin/list.php';
    }

    public function create() {
        require_once __DIR__ . '/../models/Category.php';
        $categoryModel = new Category($this->db);
        $categories = $categoryModel->getAll();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $quantity = $_POST['quantity'];
            $category_id = $_POST['category_id'];
            // Xử lý upload ảnh
            $image = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../uploads/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $new_filename = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image = $new_filename;
                }
            }
            if ($this->product->create($name, $description, $price, $quantity, $image, $category_id)) {
                $_SESSION['message'] = 'Thêm sản phẩm thành công!';
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Có lỗi xảy ra khi thêm sản phẩm!';
                $_SESSION['message_type'] = 'danger';
            }
            header('Location: index.php');
            exit();
        }
        require_once __DIR__ . '/../views/admin/form.php';
    }

   

    public function edit() {
        require_once __DIR__ . '/../models/Category.php';
        $categoryModel = new Category($this->db);
        $categories = $categoryModel->getAll();
        if (isset($_GET['id'])) {
            $product = $this->product->getById($_GET['id']);
            if ($product) {
                require_once __DIR__ . '/../views/admin/form.php';
            } else {
                header('Location: index.php');
                exit();
            }
        }
    }

    public function update() {
        require_once __DIR__ . '/../models/Category.php';
        $categoryModel = new Category($this->db);
        $categories = $categoryModel->getAll();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $quantity = $_POST['quantity'];
            $category_id = $_POST['category_id'];
            // Xử lý upload ảnh
            $image = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../uploads/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $new_filename = uniqid() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image = $new_filename;
                    // Xóa ảnh cũ nếu có
                    $old_product = $this->product->getById($id);
                    if ($old_product && $old_product['image']) {
                        $old_image_path = __DIR__ . '/../uploads/' . $old_product['image'];
                        if (file_exists($old_image_path)) {
                            unlink($old_image_path);
                        }
                    }
                }
            }
            if ($this->product->update($id, $name, $description, $price, $quantity, $image, $category_id)) {
                $_SESSION['message'] = 'Cập nhật sản phẩm thành công!';
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Có lỗi xảy ra khi cập nhật sản phẩm!';
                $_SESSION['message_type'] = 'danger';
            }
            header('Location: index.php');
            exit();
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            if ($this->product->delete($_GET['id'])) {
                $_SESSION['message'] = 'Xóa sản phẩm thành công!';
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Có lỗi xảy ra khi xóa sản phẩm!';
                $_SESSION['message_type'] = 'danger';
            }
            header('Location: index.php');
            exit();
        }
    }

    public function profile() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $user = $this->user->getById($_SESSION['user']['id']);
        require_once __DIR__ . '/../views/admin/profile.php';
    }

    public function updateProfile() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_SESSION['user']['id'];
            $full_name = $_POST['full_name'];
            $email = $_POST['email'];
            $current_password = $_POST['current_password'];
            $new_password = $_POST['new_password'];

            // Kiểm tra mật khẩu hiện tại
            $user = $this->user->login($_SESSION['user']['username'], $current_password);
            if (!$user) {
                $_SESSION['error'] = 'Mật khẩu hiện tại không đúng!';
                header('Location: index.php?action=profile');
                exit();
            }

            // Cập nhật thông tin
            if ($this->user->update($id, $full_name, $email, $_SESSION['user']['role'])) {
                if (!empty($new_password)) {
                    $this->user->changePassword($id, $new_password);
                }
                $_SESSION['user']['full_name'] = $full_name;
                $_SESSION['user']['email'] = $email;
                $_SESSION['message'] = 'Cập nhật thông tin thành công!';
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Có lỗi xảy ra khi cập nhật thông tin!';
                $_SESSION['message_type'] = 'danger';
            }
            header('Location: index.php?action=profile');
            exit();
        }
    }

    public function reports() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit();
        }

        // Mặc định lấy báo cáo trong 30 ngày gần nhất
        $end_date = date('Y-m-d');
        $start_date = date('Y-m-d', strtotime('-30 days'));
        $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';

        // Nếu có tham số từ form
        if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
            $start_date = $_GET['start_date'];
            $end_date = $_GET['end_date'];
        }

        // Lấy thống kê (bổ sung category_id nếu cần)
        $stats = $this->sale->getSalesStatistics($start_date, $end_date, $category_id);

        // Lấy danh sách danh mục cho bộ lọc
        require_once __DIR__ . '/../models/Category.php';
        $categoryModel = new Category($this->db);
        $categories = $categoryModel->getAll();

        // Lấy doanh số cho biểu đồ tròn
        if (!empty($category_id)) {
            $stats['category_revenue'] = $this->sale->getRevenueByProduct($start_date, $end_date, $category_id);
            $stats['category_revenue_type'] = 'product';
        } else {
            $stats['category_revenue'] = $this->sale->getRevenueByCategory($start_date, $end_date, $category_id);
            $stats['category_revenue_type'] = 'category';
        }
        // Lấy doanh số theo tháng trong năm hiện tại
        $stats['monthly_revenue'] = $this->sale->getRevenueByMonth(date('Y'), $category_id);

        // Lấy số lượng sản phẩm theo danh mục
        $stats['category_product_count'] = $this->product->getProductCountByCategory($category_id);

        // Lấy số lượng tồn kho từng sản phẩm
        $stats['product_stock'] = $this->product->getAllProductStock($category_id);

        // Lấy sản phẩm sắp hết (KHÔNG lọc theo danh mục, luôn lấy tất cả sản phẩm sắp hết)
        $stats['low_stock_products'] = $this->product->getLowStockProducts();

        require_once __DIR__ . '/../views/admin/reports.php';
    }

    public function addSale() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product_id = $_POST['product_id'];
            $quantity = $_POST['quantity'];
            $price = $_POST['price'];
            $sale_date = $_POST['sale_date'];
            $created_by = $_SESSION['user']['id'];

            if ($this->sale->create($product_id, $quantity, $price, $sale_date, $created_by)) {
                $_SESSION['message'] = 'Thêm đơn hàng thành công!';
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = 'Có lỗi xảy ra khi thêm đơn hàng!';
                $_SESSION['message_type'] = 'danger';
            }
            header('Location: index.php?action=reports');
            exit();
        }
    }

    public function categories() {
        require_once __DIR__ . '/../models/Category.php';
        $categoryModel = new Category($this->db);
        $categories = $categoryModel->getAll();
        require_once __DIR__ . '/../views/admin/categories.php';
    }

    public function addCategory() {
        require_once __DIR__ . '/../models/Category.php';
        $categoryModel = new Category($this->db);
        $name = $_POST['name'];
        $description = $_POST['description'];
        $categoryModel->create($name, $description);
        header('Location: index.php?action=categories');
        exit();
    }

    public function editCategory() {
        require_once __DIR__ . '/../models/Category.php';
        $categoryModel = new Category($this->db);
        $id = $_GET['id'];
        $categories = $categoryModel->getAll();
        $editCategory = null;
        foreach ($categories as $cat) {
            if ($cat['id'] == $id) {
                $editCategory = $cat;
                break;
            }
        }
        require_once __DIR__ . '/../views/admin/categories.php';
    }

    public function updateCategory() {
        require_once __DIR__ . '/../models/Category.php';
        $categoryModel = new Category($this->db);
        $id = $_POST['id'];
        $name = $_POST['name'];
        $description = $_POST['description'];
        $categoryModel->update($id, $name, $description);
        header('Location: index.php?action=categories');
        exit();
    }

    public function deleteCategory() {
        require_once __DIR__ . '/../models/Category.php';
        $categoryModel = new Category($this->db);
        $id = $_GET['id'];
        $categoryModel->delete($id);
        header('Location: index.php?action=categories');
        exit();
    }

    public function exportReport() {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?action=login');
            exit();
        }

        $type = isset($_GET['type']) ? $_GET['type'] : 'sales';
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
        $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
        $format = isset($_GET['format']) ? $_GET['format'] : 'pdf';

       

     

    }
}
?> 