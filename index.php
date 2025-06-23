<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config/database.php';
require_once 'controllers/AdminController.php';
require_once __DIR__ . '/models/Product.php';
require_once __DIR__ . '/models/Category.php';

$db = (new Database())->getConnection();
$controller = new AdminController();
$productModel = new Product($db);
$categoryModel = new Category($db);

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'login':
        $controller->login();
        break;
    
    default:
        break;
}

$categories = $categoryModel->getAll();

$category_id = isset($_GET['category_id']) ? $_GET['category_id'] : null;

if ($category_id) {
    $featuredProducts = $productModel->getByCategory($category_id);
} else {
    $featuredProducts = $productModel->getAll(1, 8)['data'];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ - Đặc sản Tây Bắc</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/web-3/views/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&display=swap" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; }
        .header { background: #388e3c; color: #fff; padding: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.07); }
        .header .container { display: flex; align-items: center; justify-content: space-between; max-width: 1200px; margin: auto; padding: 0 16px; height: 64px; }
        .logo { font-size: 1.7em; font-weight: bold; letter-spacing: 2px; display: flex; align-items: center; gap: 8px; }
        .menu { display: flex; gap: 28px; align-items: center; }
        .menu a { color: #fff; text-decoration: none; font-weight: 500; font-size: 1.08em; transition: color 0.2s; }
        .menu a:hover { color: #ffe082; }
        .search-bar { display: flex; align-items: center; margin-left: 24px; }
        .search-bar input { padding: 8px 14px; border-radius: 24px 0 0 24px; border: none; outline: none; font-size: 1em; }
        .search-bar button { padding: 8px 14px; border: none; background: #fff; color: #388e3c; border-radius: 0 24px 24px 0; cursor: pointer; font-size: 1em; transition: background 0.2s; }
        .search-bar button:hover { background: #c8e6c9; }
        .user-actions { display: flex; gap: 18px; align-items: center; }
        .user-actions a { color: #fff; text-decoration: none; font-size: 1.08em; transition: color 0.2s; }
        .user-actions a:hover { color: #ffe082; }
        .banner {
            position: relative;
            background: #e8f5e9;
            height: 340px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .banner-main-img {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            max-width: 100%;
            max-height: 100%;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 1;
        }
        .banner-slogan-overlay {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            z-index: 2;
            color: #fff;
            font-size: 2.5em;
            font-family: 'Pacifico', cursive, Arial, sans-serif;
            text-shadow: 0 2px 12px rgba(0,0,0,0.35);
            text-align: center;
            max-width: 80vw;
            pointer-events: none;
        }
        .section { max-width: 1200px; margin: 40px auto; background: #fff; border-radius: 12px; padding: 28px; box-shadow: 0 2px 12px rgba(0,0,0,0.07); }
        .section-title { font-size: 1.5em; margin-bottom: 24px; color: #388e3c; font-weight: bold; }
        .categories { display: flex; gap: 24px; flex-wrap: wrap; justify-content: center; }
        .category-box { background: #f1f8e9; border-radius: 16px; padding: 22px 32px; text-align: center; flex: 1 1 180px; min-width: 180px; box-shadow: 0 1px 6px rgba(56,142,60,0.07); transition: transform 0.18s, box-shadow 0.18s; cursor: pointer; font-size: 1.1em; }
        .category-box i { font-size: 2.2em; color: #388e3c; margin-bottom: 10px; }
        .category-box:hover { transform: translateY(-6px) scale(1.04); box-shadow: 0 4px 16px rgba(56,142,60,0.13); background: #e8f5e9; }
        .products { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 28px; }
        .product-card { background: #fafafa; border-radius: 14px; box-shadow: 0 1px 8px rgba(56,142,60,0.07); padding: 18px; text-align: center; transition: transform 0.18s, box-shadow 0.18s; }
        .product-card:hover { transform: translateY(-6px) scale(1.03); box-shadow: 0 4px 16px rgba(56,142,60,0.13); }
        .product-card img { width: 100%; height: 170px; object-fit: cover; border-radius: 10px; margin-bottom: 12px; transition: transform 0.2s; }
        .product-card:hover img { transform: scale(1.06); }
        .product-card .name { font-weight: bold; margin-bottom: 8px; font-size: 1.08em; }
        .product-card .price { color: #d32f2f; font-weight: bold; margin-bottom: 12px; font-size: 1.08em; }
        .product-card .btn { background: #388e3c; color: #fff; border: none; padding: 9px 20px; border-radius: 24px; cursor: pointer; font-size: 1em; transition: background 0.2s; }
        .product-card .btn:hover { background: #2e7031; }
        .footer-textonly {
            background: linear-gradient(90deg, #388e3c 0%, #4caf50 100%);
            color: #fff;
            text-align: center;
            padding: 32px 10px 18px 10px;
            margin-top: 40px;
            border-radius: 0 0 8px 8px;
            font-size: 1.08em;
            box-shadow: 0 -2px 12px rgba(56,142,60,0.07);
        }
        .footer-slogan {
            font-size: 1.25em;
            font-weight: bold;
            margin-bottom: 10px;
            letter-spacing: 0.5px;
            font-family: 'Segoe UI', 'Arial', sans-serif;
        }
        .footer-contact {
            margin-bottom: 8px;
            font-size: 1em;
            opacity: 0.95;
        }
        .footer-links {
            margin-bottom: 8px;
            font-size: 1em;
        }
        .footer-links a {
            color: #ffe082;
            text-decoration: none;
            margin: 0 4px;
            transition: color 0.2s;
        }
        .footer-links a:hover {
            color: #fff;
            text-decoration: underline;
        }
        .footer-copyright {
            font-size: 0.98em;
            opacity: 0.93;
            margin-top: 6px;
        }
        @media (max-width: 600px) {
            .footer-textonly { font-size: 0.98em; padding: 18px 2px 10px 2px; }
            .footer-slogan { font-size: 1em; }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="logo"><i class="fas fa-mountain"></i> Tây Bắc Store</div>
            <nav class="menu">
                <a href="index.php">Trang chủ</a>
                <a href="#categories">Danh mục</a>
                <a href="#products">Sản phẩm</a>
                
            </nav>
            <div class="search-bar">
                <form action="index.php" method="get" style="display:flex;">
                    <input type="text" name="search" placeholder="Tìm kiếm sản phẩm...">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <div class="user-actions">
                <a href="index.php?action=login"><i class="fas fa-user"></i> Đăng nhập</a>
            </div>
        </div>
    </div>
    <div class="banner">
        <img src="/web-3/uploads/banner.webp" alt="Banner chính" class="banner-main-img">
        
    </div>
    <div class="section" id="categories">
        <div class="section-title">Danh mục nổi bật</div>
        <div class="categories">
            <?php foreach ($categories as $cat): ?>
                <a href="?category_id=<?= $cat['id'] ?>" class="category-box">
                    <i class="fas fa-leaf"></i><br>
                    <?= htmlspecialchars($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="section" id="products">
        <div class="section-title">Sản phẩm nổi bật</div>
        <div class="products">
            <?php if (!empty($featuredProducts)): ?>
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="product-card">
                        <img src="/web-3/uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                        <div class="name"><?= htmlspecialchars($product['name']) ?></div>
                        <div class="price"><?= number_format($product['price'], 0, ',', '.') ?> VNĐ</div>
                        <button class="btn">Thêm vào giỏ</button>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div>Không có sản phẩm nào trong danh mục này.</div>
            <?php endif; ?>
        </div>
    </div>
    
    

    <div class="footer-textonly">
        <div class="footer-slogan">Đặc sản Tây Bắc - Chất lượng từ thiên nhiên, kết nối vùng cao đến mọi miền!</div>
        <div class="footer-contact">
            Địa chỉ: Bản Khuông, xã Đoài Dương, huyện Trùng Khánh, Cao Bằng | Hotline: 0962 515 436 | Email: info@taybacstore.com
        </div>
        <div class="footer-links">
            <a href="index.php">Trang chủ</a> |
            <a href="#products">Sản phẩm</a> |
            <a href="#categories">Danh mục</a> |
            <a href="#">Giới thiệu</a> |
            <a href="#">Liên hệ</a>
        </div>
        <div class="footer-copyright">
             <?= date('Y') ?> Tây Bắc Store
        </div>
    </div>
</body>
</html>
