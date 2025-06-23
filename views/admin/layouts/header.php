<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Admin Panel'; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/web-3/views/admin.css">
    <?php if (isset($extra_css)): ?>
        <?php foreach ($extra_css as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <div class="admin-panel">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="logo">
                <i class="fas fa-box"></i>
                <span>Wren Evans </span>
            </div>
            <nav>
                <a href="/web-3/admin/index.php" <?php echo $current_page === 'products' ? 'class="active"' : ''; ?>>
                    <i class="fas fa-list"></i> Danh sách sản phẩm
                </a>
                <a href="/web-3/admin/index.php?action=categories" <?php echo $current_page === 'categories' ? 'class="active"' : ''; ?>>
                    <i class="fas fa-tags"></i> Quản lý danh mục
                </a>
                <a href="/web-3/admin/index.php?action=reports" <?php echo $current_page === 'reports' ? 'class="active"' : ''; ?>>
                    <i class="fas fa-chart-bar"></i> Báo cáo & Thống kê
                </a>
                <a href="/web-3/admin/index.php?action=profile" <?php echo $current_page === 'profile' ? 'class="active"' : ''; ?>>
                    <i class="fas fa-user"></i> Thông tin cá nhân
                </a>
                <a href="/web-3/admin/index.php?action=logout">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Header quản trị đơn giản -->
            <div class="admin-header">
                <h1><?php echo $page_title ?? 'Quản trị hệ thống'; ?></h1>
                <div class="admin-user">
                    <i class="fas fa-user-shield"></i>
                    <?php if (isset($_SESSION['user'])): ?>
                        <span><?php echo htmlspecialchars($_SESSION['user']['full_name'] ?? 'Administrator'); ?></span>
                    <?php else: ?>
                        <span>Administrator</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Content Container -->
            <div class="content-container">
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['message_type']; ?>">
                        <?php 
                        echo $_SESSION['message'];
                        unset($_SESSION['message']);
                        unset($_SESSION['message_type']);
                        ?>
                    </div>
                <?php endif; ?> 