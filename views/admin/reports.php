
<?php
$page_title = 'Báo cáo và Thống kê';
$current_page = 'reports';
$extra_js = ['https://cdn.jsdelivr.net/npm/chart.js'];
require_once __DIR__ . '/layouts/header.php';
?>

<!-- Bộ lọc thời gian -->
<div class="filter-container">
    <form method="GET" action="/web-3/admin/index.php" class="filter-form">
        <input type="hidden" name="action" value="reports">
        <div class="form-group">
            <label for="start_date">Từ ngày</label>
            <input type="date" id="start_date" name="start_date" class="form-control" value="<?= htmlspecialchars($start_date ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="end_date">Đến ngày</label>
            <input type="date" id="end_date" name="end_date" class="form-control" value="<?= htmlspecialchars($end_date ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="category_id">Danh mục</label>
            <div style="position:relative;">
                <select id="category_id" name="category_id" class="form-control" style="padding-left:32px;">
                    <option value="">Tất cả danh mục</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= (isset($category_id) && $category_id == $category['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <i class="fas fa-list" style="position:absolute;left:8px;top:50%;transform:translateY(-50%);color:#43a047;"></i>
            </div>
        </div>
        <div class="form-group">
            <label>&nbsp;</label>
            <button type="submit" class="btn btn-success" style="width:100%;display:flex;align-items:center;justify-content:center;gap:6px;">
                <i class="fas fa-filter"></i> Lọc
            </button>
        </div>
    </form>
</div>

<!-- Thống kê tổng quan -->
<div class="stats-container">
    <div class="stat-box">
        <i class="fas fa-money-bill-wave"></i>
        <div class="stat-info">
            <h3>Tổng doanh số</h3>
            <p><?php echo number_format($stats['general']['total_revenue'], 0, ',', '.'); ?> VNĐ</p>
        </div>
    </div>
    <div class="stat-box">
        <i class="fas fa-shopping-cart"></i>
        <div class="stat-info">
            <h3>Tổng đơn hàng</h3>
            <p><?php echo number_format($stats['general']['total_sales']); ?></p>
        </div>
    </div>
    <div class="stat-box">
        <i class="fas fa-chart-line"></i>
        <div class="stat-info">
            <h3>Trung bình/đơn</h3>
            <p><?php echo number_format($stats['general']['average_sale'], 0, ',', '.'); ?> VNĐ</p>
        </div>
    </div>
</div>

<!-- Biểu đồ doanh số -->
<div class="chart-container">
    <h2>Doanh số theo ngày</h2>
    <canvas id="salesChart" height="80"></canvas>
</div>

<!-- Biểu đồ doanh số theo tháng -->
<div class="chart-container">
    <h2>Doanh số theo tháng (<?= date('Y') ?>)</h2>
    <canvas id="monthlyBarChart" height="80"></canvas>
</div>

<!-- Biểu đồ doanh số theo danh mục -->
<div class="chart-container" style="max-width:500px; margin:auto;">
    <h2>
        <?php if (!empty($category_id)): ?>
            Tỉ lệ doanh số theo sản phẩm trong danh mục
        <?php else: ?>
            Tỉ lệ doanh số theo danh mục
        <?php endif; ?>
    </h2>
    <canvas id="categoryPieChart" width="400" height="400"></canvas>
</div>

<!-- Top sản phẩm bán chạy -->
<div class="top-products">
    <h2>Top sản phẩm bán chạy</h2>
    <table>
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th>Số lượng</th>
                <th>Doanh số</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stats['top_products'] as $product): ?>
            <tr>
                <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                <td><?php echo number_format($product['total_quantity']); ?></td>
                <td><?php echo number_format($product['total_revenue'], 0, ',', '.'); ?> VNĐ</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Thống kê số lượng sản phẩm theo danh mục -->
<div class="top-products">
    <h2>Số lượng sản phẩm theo danh mục</h2>
    <table>
        <thead>
            <tr>
                <th>Danh mục</th>
                <th>Số lượng sản phẩm</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stats['category_product_count'] as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['category_name']) ?></td>
                <td><?= number_format($row['product_count']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="top-products">
    <h2>Sản phẩm sắp hết </h2>
    <table>
        <thead>
            <tr>
                <th>Tên sản phẩm</th>
                <th>Danh mục</th>
                <th>Số lượng còn</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($stats['low_stock_products'])): ?>
                <?php foreach ($stats['low_stock_products'] as $product): ?>
                <tr>
                    <td><?= htmlspecialchars($product['name']) ?></td>
                    <td><?= htmlspecialchars($product['category_name']) ?></td>
                    <td><?= number_format($product['quantity']) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3" style="text-align:center;">Không có sản phẩm nào sắp hết.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Thống kê tồn kho từng sản phẩm -->
<div class="top-products">
    <h2>Số lượng tồn kho từng sản phẩm</h2>
    <table>
        <thead>
            <tr>
                <th>Tên sản phẩm</th>
                <th>Danh mục</th>
                <th>Số lượng còn</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($stats['product_stock'] as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['product_name']) ?></td>
                <td><?= htmlspecialchars($row['category_name']) ?></td>
                <td><?= number_format($row['quantity']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>



<!-- Sản phẩm sắp hết -->


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    var ctx = document.getElementById('salesChart').getContext('2d');
    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($stats['daily_stats'], 'sale_date')) ?>,
            datasets: [{
                label: 'Doanh số theo ngày',
                data: <?= json_encode(array_column($stats['daily_stats'], 'daily_revenue')) ?>,
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: true }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
    // Biểu đồ tròn doanh số theo danh mục
    var ctxPie = document.getElementById('categoryPieChart').getContext('2d');
    var pieChart = new Chart(ctxPie, {
        type: 'pie',
        data: {
            <?php if (!empty($category_id)): ?>
                labels: <?= json_encode(array_column($stats['category_revenue'], 'product_name')) ?>,
                datasets: [{
                    data: <?= json_encode(array_map('floatval', array_column($stats['category_revenue'], 'revenue'))) ?>,
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#C9CBCF', '#B2FF66', '#FF66B2', '#66B2FF'
                    ]
                }]
            <?php else: ?>
                labels: <?= json_encode(array_column($stats['category_revenue'], 'category_name')) ?>,
                datasets: [{
                    data: <?= json_encode(array_map('floatval', array_column($stats['category_revenue'], 'revenue'))) ?>,
                    backgroundColor: [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#C9CBCF', '#B2FF66', '#FF66B2', '#66B2FF'
                    ]
                }]
            <?php endif; ?>
        },
        options: {
            responsive: true,
            plugins: { legend: { display: true } }
        }
    });
    // Biểu đồ cột doanh số theo tháng
    var ctxBar = document.getElementById('monthlyBarChart').getContext('2d');
    var barChart = new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: [
                'Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
                'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'
            ],
            datasets: [{
                label: 'Doanh số (VNĐ)',
                data: (function() {
                    var arr = Array(12).fill(0);
                    <?php foreach ($stats['monthly_revenue'] as $m): ?>
                        arr[<?= $m['month']-1 ?>] = <?= $m['revenue'] ?>;
                    <?php endforeach; ?>
                    return arr;
                })(),
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

<?php require_once __DIR__ . '/layouts/footer.php'; ?> 