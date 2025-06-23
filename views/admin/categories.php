<?php
$page_title = 'Quản lý danh mục';
$current_page = 'categories';
require_once __DIR__ . '/layouts/header.php';
?>
<div class="content-container">
    <h2>Danh sách danh mục</h2>
    <?php if (isset($editCategory)): ?>
    <form action="index.php?action=updateCategory" method="POST" style="margin-bottom:20px; display: flex; gap: 10px; align-items: flex-end;">
        <input type="hidden" name="id" value="<?= $editCategory['id'] ?>">
        <div>
            <input type="text" name="name" placeholder="Tên danh mục" required class="form-control" value="<?= htmlspecialchars($editCategory['name']) ?>">
        </div>
        <div>
            <input type="text" name="description" placeholder="Mô tả" class="form-control" value="<?= htmlspecialchars($editCategory['description']) ?>">
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="index.php?action=categories" class="btn btn-danger">Hủy</a>
    </form>
    <?php else: ?>
    <form action="index.php?action=addCategory" method="POST" style="margin-bottom:20px; display: flex; gap: 10px; align-items: flex-end;">
        <div>
            <input type="text" name="name" placeholder="Tên danh mục" required class="form-control">
        </div>
        <div>
            <input type="text" name="description" placeholder="Mô tả" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Thêm danh mục</button>
    </form>
    <?php endif; ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên danh mục</th>
                <th>Mô tả</th>
                <th>Ngày tạo</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($categories as $cat): ?>
            <tr>
                <td><?= $cat['id'] ?></td>
                <td><?= htmlspecialchars($cat['name']) ?></td>
                <td><?= htmlspecialchars($cat['description']) ?></td>
                <td><?= $cat['created_at'] ?></td>
                <td class="action-links" style="display: flex; gap: 8px; justify-content: center;">
                    <a href="index.php?action=editCategory&id=<?= $cat['id'] ?>"
                       class="btn btn-primary btn-sm"
                       style="display: flex; align-items: center; gap: 4px; min-width: 40px; justify-content: center; background: #fff; color: #4caf50; border: 1.5px solid #4caf50; font-weight: 500;"
                       title="Sửa">
                        <i class="fas fa-edit" style="color: #4caf50;"></i>
                        <span>Sửa</span>
                    </a>
                    <a href="index.php?action=deleteCategory&id=<?= $cat['id'] ?>"
                       class="btn btn-danger btn-sm"
                       style="display: flex; align-items: center; gap: 4px; min-width: 40px; justify-content: center; background: #fff; color: #f44336; border: 1.5px solid #f44336; font-weight: 500;"
                       onclick="return confirm('Bạn có chắc chắn muốn xóa?')"
                       title="Xóa">
                        <i class="fas fa-trash-alt" style="color: #f44336;"></i>
                        <span>Xóa</span>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php require_once __DIR__ . '/layouts/footer.php'; ?> 