<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin cá nhân - Admin Panel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="views/admin.css">
</head>
<body>
    <div class="admin-panel">
        <div class="sidebar">
            <div class="logo">
                <i class="fas fa-box"></i>
                <span>Admin Panel</span>
            </div>
        
        </div>
        <div class="main-content">
            <div class="header">
                <h1>Thông tin cá nhân</h1>
            </div>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                    <?php 
                    echo $_SESSION['message'];
                    unset($_SESSION['message']);
                    unset($_SESSION['message_type']);
                    ?>
                </div>
            <?php endif; ?>

            <div class="content-container" style="max-width: 480px; margin: 0 auto;">
                <form action="index.php?action=updateProfile" method="POST" style="display: flex; flex-direction: column; gap: 18px;">
                    <div style="text-align: center; margin-bottom: 10px;">
                        <i class="fas fa-user-circle" style="font-size: 64px; color: #4caf50; margin-bottom: 8px;"></i>
                        <div style="font-size: 1.1em; color: #666;">Cập nhật thông tin cá nhân của bạn</div>
                    </div>
                    <div class="form-group">
                        <label for="username"><i class="fas fa-user"></i> Tên đăng nhập</label>
                        <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="full_name"><i class="fas fa-id-card"></i> Họ và tên</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="role"><i class="fas fa-user-tag"></i> Vai trò</label>
                        <input type="text" class="form-control" id="role" value="<?php echo $user['role'] === 'admin' ? 'Quản trị viên' : 'Nhân viên'; ?>" disabled>
                    </div>
                    <div class="form-group">
                        <label for="current_password"><i class="fas fa-lock"></i> Mật khẩu hiện tại</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required placeholder="Nhập mật khẩu hiện tại">
                    </div>
                    <div class="form-group">
                        <label for="new_password"><i class="fas fa-key"></i> Mật khẩu mới <span style="font-weight: normal; color: #888;">(bỏ trống nếu không đổi)</span></label>
                        <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Nhập mật khẩu mới">
                    </div>
                    <div class="form-actions" style="display: flex; gap: 10px; margin-top: 10px;">
                        <button type="submit" class="btn btn-primary" style="min-width: 120px;">
                            <i class="fas fa-save"></i> Cập nhật
                        </button>
                        <a href="index.php" class="btn btn-secondary" style="min-width: 100px; background: #eee; color: #333;">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html> 