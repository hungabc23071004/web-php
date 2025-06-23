<!DOCTYPE html>
<html>
<head>
    <title><?= isset($product) ? 'Sửa sản phẩm' : 'Thêm sản phẩm mới' ?> - Admin Panel</title>
    <link rel="stylesheet" href="/web-3/views/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            
            <h1 class="admin-title" style="margin-top: 0;"><?= isset($product) ? 'Sửa sản phẩm' : 'Thêm sản phẩm mới' ?></h1>
        </div>

        <form action="index.php?action=<?= isset($product) ? 'update' : 'create' ?>" method="POST" enctype="multipart/form-data">
            <?php if (isset($product)): ?>
                <input type="hidden" name="id" value="<?= $product['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="name">Tên sản phẩm</label>
                <input type="text" 
                       class="form-control" 
                       id="name" 
                       name="name" 
                       value="<?= isset($product) ? htmlspecialchars($product['name']) : '' ?>" 
                       required>
            </div>

            <div class="form-group">
                <label for="description">Mô tả</label>
                <textarea class="form-control" 
                          id="description" 
                          name="description" 
                          rows="4"><?= isset($product) ? htmlspecialchars($product['description']) : '' ?></textarea>
            </div>

            <div class="form-group">
                <label for="price">Giá (VNĐ)</label>
                <input type="number" 
                       class="form-control" 
                       id="price" 
                       name="price" 
                       value="<?= isset($product) ? $product['price'] : '' ?>" 
                       required>
            </div>

            <div class="form-group">
                <label for="quantity">Số lượng</label>
                <input type="number" 
                       class="form-control" 
                       id="quantity" 
                       name="quantity" 
                       value="<?= isset($product) ? $product['quantity'] : '' ?>" 
                       required>
            </div>

            <div class="form-group">
                <label for="category_id">Danh mục</label>
                <select class="form-control" id="category_id" name="category_id" required>
                    <option value="">-- Chọn danh mục --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= (isset($product) && $product['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="image">Ảnh sản phẩm</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                <?php if (isset($product) && $product['image']): ?>
                    <div style="margin-top:10px;">
                        <img src="/web-3/uploads/<?= $product['image'] ?>" alt="Ảnh hiện tại" class="product-image">
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-actions" style="display: flex; gap: 10px; margin-top: 10px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> 
                    <?= isset($product) ? 'Cập nhật' : 'Thêm mới' ?>
                </button>
                <a href="index.php" class="btn btn-primary back-btn">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </form>
    </div>
</body>
</html> 