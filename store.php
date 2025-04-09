<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quản lý cửa hàng</title>
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="assets/css/store.css">
</head>
<body>

<div class="sidebar">
    <div class="logo">GHN</div>
    <div class="user-info">
        <p>Admin<br><small>Chủ cửa hàng</small></p>
    </div>
    <a href="index.php"><i class="fas fa-list"></i> Quản lý đơn hàng</a>
    <a href="products.php"><i class="fas fa-box"></i> Quản lý sản phẩm</a>
    <a href="add.php"><i class="fas fa-truck"></i> Tạo đơn hàng</a>
    <a href="store.php" class="active"><i class="fas fa-store"></i> Quản lý cửa hàng</a>
    <a href="report.php"><i class="fas fa-money-bill"></i> Báo cao doanh thu</a>
    <a href="#"><i class="fas fa-question-circle"></i> Yêu cầu hỗ trợ</a>
    <a href="#"><i class="fas fa-users"></i> Quản lý nhân viên</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
</div>

<div class="container-main">
    <div class="row">
        <!-- Thống kê khách hàng -->
        <div class="col-md-3">
            <div class="stat-card bg-success">
                <div class="stat-card-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-title text-danger">TỔNG KHÁCH HÀNG</div>
                    <div class="stat-card-number">58 khách hàng</div>
                </div>
            </div>
        </div>

        <!-- Thống kê sản phẩm -->
        <div class="col-md-3">
            <div class="stat-card bg-info">
                <div class="stat-card-icon">
                    <i class="fas fa-box"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-title">TỔNG SẢN PHẨM</div>
                    <div class="stat-card-number">1850 sản phẩm</div>
                </div>
            </div>
        </div>

        <!-- Thống kê đơn hàng -->
        <div class="col-md-3">
            <div class="stat-card bg-warning">
                <div class="stat-card-icon">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-title">TỔNG ĐƠN HÀNG</div>
                    <div class="stat-card-number">247 đơn hàng</div>
                </div>
            </div>
        </div>

        <!-- Sắp hết hàng -->
        <div class="col-md-3">
            <div class="stat-card bg-danger">
                <div class="stat-card-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="stat-card-info">
                    <div class="stat-card-title">SẮP HẾT HÀNG</div>
                    <div class="stat-card-number">4 sản phẩm</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Dữ liệu 6 tháng đầu vào</h3>
                </div>
                <div class="panel-body">
                    <canvas id="revenueChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Thống kê 6 tháng doanh thu</h3>
                </div>
                <div class="panel-body">
                    <canvas id="barChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Tình trạng đơn hàng</h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID đơn hàng</th>
                                    <th>Tên khách hàng</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>AL3947</td>
                                    <td>Phạm Thị Ngọc</td>
                                    <td>19.770.000 đ</td>
                                    <td><span class="label label-warning">Chờ giao</span></td>
                                </tr>
                                <tr>
                                    <td>ER3835</td>
                                    <td>Nguyễn Thị Mỹ Yến</td>
                                    <td>16.770.000 đ</td>
                                    <td><span class="label label-success">Hoàn thành</span></td>
                                </tr>
                                <tr>
                                    <td>MD0837</td>
                                    <td>Triệu Thanh Phú</td>
                                    <td>9.400.000 đ</td>
                                    <td><span class="label label-primary">Đã hoàn tiền</span></td>
                                </tr>
                                <tr>
                                    <td>MT9835</td>
                                    <td>Đặng Hoàng Phúc</td>
                                    <td>40.650.000 đ</td>
                                    <td><span class="label label-danger">Đã hủy</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script>
// Biểu đồ đường
var ctx = document.getElementById('revenueChart').getContext('2d');
var revenueChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6'],
        datasets: [{
            label: 'Doanh thu',
            data: [30, 45, 35, 50, 40, 60],
            borderColor: '#007bff',
            backgroundColor: 'rgba(0, 123, 255, 0.1)',
            fill: true
        },
        {
            label: 'Chi phí',
            data: [20, 35, 25, 45, 30, 50],
            borderColor: '#ffc107',
            backgroundColor: 'rgba(255, 193, 7, 0.1)',
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});

// Biểu đồ cột
var barCtx = document.getElementById('barChart').getContext('2d');
var barChart = new Chart(barCtx, {
    type: 'bar',
    data: {
        labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6'],
        datasets: [{
            label: 'Doanh thu',
            data: [65, 59, 80, 81, 56, 55],
            backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6c757d']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>

</body>
</html>
