<!DOCTYPE html>
<html>
<head>
    <title>Đăng nhập - Admin Panel</title>
    <link rel="stylesheet" href="/web-3/views/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h1 class="login-title">Đăng nhập</h1>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['error'] ?>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <form action="index.php?action=login" method="POST" class="login-form">
                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <input type="text" 
                           class="form-control" 
                           id="username" 
                           name="username" 
                           required>
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" 
                           class="form-control" 
                           id="password" 
                           name="password" 
                           required>
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-sign-in-alt"></i> Đăng nhập
                </button>
            </form>
        </div>
    </div>
</body>
</html> 