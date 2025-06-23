<?php
$page_title = 'Quản lý sản phẩm';
$current_page = 'products';
require_once __DIR__ . '/layouts/header.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quản lý sản phẩm - Admin Panel</title>
    <link rel="stylesheet" href="/web-3/views/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1 class="admin-title">Quản lý sản phẩm</h1>
            <a href="/web-3/admin/index.php?action=create" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm sản phẩm mới
            </a>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-<?= $_SESSION['message_type'] ?>">
                <?= $_SESSION['message'] ?>
            </div>
            <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
        <?php endif; ?>

        <!-- Thống kê -->
        <div class="stats-container">
            <div class="stat-box">
                <i class="fas fa-box"></i>
                <div class="stat-info">
                    <h3>Tổng sản phẩm</h3>
                    <p><?= $stats['total_products'] ?></p>
                </div>
            </div>
            <div class="stat-box">
                <i class="fas fa-dollar-sign"></i>
                <div class="stat-info">
                    <h3>Tổng giá trị</h3>
                    <p><?= number_format($stats['total_value'], 0, ',', '.') ?> VNĐ</p>
                </div>
            </div>
            <div class="stat-box">
                <i class="fas fa-exclamation-triangle"></i>
                <div class="stat-info">
                    <h3>Sản phẩm sắp hết</h3>
                    <p><?= $stats['low_stock'] ?></p>
                </div>
            </div>
        </div>

        <!-- Tìm kiếm và sắp xếp -->
        <div class="search-sort-container">
            <form action="/web-3/admin/index.php" method="GET" class="search-form" style="display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 20px;">
                <input type="text" name="search" placeholder="Tìm kiếm sản phẩm..." value="<?= htmlspecialchars($search ?? '') ?>">
                <select name="category_id">
                    <option value="">Tất cả danh mục</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= (isset($category_id) && $category_id == $category['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select name="sort_by">
                    <option value="name" <?= ($sort_by ?? '') === 'name' ? 'selected' : '' ?>>Tên</option>
                    <option value="price" <?= ($sort_by ?? '') === 'price' ? 'selected' : '' ?>>Giá</option>
                    <option value="quantity" <?= ($sort_by ?? '') === 'quantity' ? 'selected' : '' ?>>Số lượng</option>
                    <option value="created_at" <?= ($sort_by ?? '') === 'created_at' ? 'selected' : '' ?>>Ngày tạo</option>
                </select>
                <select name="sort_order">
                    <option value="ASC" <?= ($sort_order ?? '') === 'ASC' ? 'selected' : '' ?>>Tăng dần</option>
                    <option value="DESC" <?= ($sort_order ?? '') === 'DESC' ? 'selected' : '' ?>>Giảm dần</option>
                </select>
                <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Tìm kiếm</button>
            </form>
        </div>

        <!-- Danh sách sản phẩm -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Hình ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Mô tả</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Ngày tạo</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                <tr>
                    <td><?= $product['id'] ?></td>
                    <td>
                        <?php if ($product['image']): ?>
                            <img src="/web-3/uploads/<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                        <?php else: ?>
                            <img src="/web-3/views/admin/no-image.png" alt="No image" class="product-image">
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= htmlspecialchars($product['description']) ?></td>
                    <td><?= number_format($product['price'], 0, ',', '.') ?> VNĐ</td>
                    <td><?= $product['quantity'] ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($product['created_at'])) ?></td>
                    <td class="action-links">
                        <a href="/web-3/admin/index.php?action=edit&id=<?= $product['id'] ?>" title="Sửa">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="/web-3/admin/index.php?action=delete&id=<?= $product['id'] ?>" 
                           onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')" 
                           title="Xóa">
                            <i class="fas fa-trash-alt"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Phân trang -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="/web-3/admin/index.php?page=<?= $i ?>&search=<?= urlencode($search ?? '') ?>&sort_by=<?= $sort_by ?? 'created_at' ?>&sort_order=<?= $sort_order ?? 'DESC' ?>" 
                   class="btn <?= $page == $i ? 'btn-primary' : '' ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php require_once __DIR__ . '/layouts/footer.php'; ?> 