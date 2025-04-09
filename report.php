<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quản lý đơn hàng</title>
    <link rel="stylesheet" href="assets/css/reset.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/report.css">
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
    <a href="store.php"><i class="fas fa-store"></i> Quản lý cửa hàng</a>
    <a href="report.php" class="active"><i class="fas fa-money-bill"></i> Báo cáo doanh thu</a>
    <a href="#"><i class="fas fa-question-circle"></i> Yêu cầu hỗ trợ</a>
    <a href="#"><i class="fas fa-users"></i> Quản lý nhân viên</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
</div>

<div class="container-main">
  <h4>Báo Cáo Doanh Thu</h4>

  <div class="stats-row">
    <div class="stat-box green">
      <div class="icon-box">
        <i class="fas fa-users"></i>
      </div>
      <div class="stat-info">
        <h3>TỔNG NHÂN VIÊN</h3>
        <p>26 nhân viên</p>
      </div>
    </div>

    <div class="stat-box blue">
      <div class="icon-box">
        <i class="fas fa-tag"></i>
      </div>
      <div class="stat-info">
        <h3>TỔNG SẢN PHẨM</h3>
        <p>8580 sản phẩm</p>
      </div>
    </div>

    <div class="stat-box orange">
      <div class="icon-box">
        <i class="fas fa-shopping-cart"></i>
      </div>
      <div class="stat-info">
        <h3>TỔNG ĐƠN HÀNG</h3>
        <p>457 đơn hàng</p>
      </div>
    </div>

    <div class="stat-box red">
      <div class="icon-box">
        <i class="fas fa-exclamation-circle"></i>
      </div>
      <div class="stat-info">
        <h3>BỊ CẤM</h3>
        <p>4 nhân viên</p>
      </div>
    </div>
  </div>

  <div class="stats-row">
    <div class="stat-box green">
      <div class="icon-box">
        <i class="fas fa-chart-line"></i>
      </div>
      <div class="stat-info">
        <h3>TỔNG THU NHẬP</h3>
        <p>104.890.000 đ</p>
      </div>
    </div>

    <div class="stat-box blue">
      <div class="icon-box">
        <i class="fas fa-user-plus"></i>
      </div>
      <div class="stat-info">
        <h3>NHÂN VIÊN MỚI</h3>
        <p>3 nhân viên</p>
      </div>
    </div>

    <div class="stat-box orange">
      <div class="icon-box">
        <i class="fas fa-box-open"></i>
      </div>
      <div class="stat-info">
        <h3>HẾT HÀNG</h3>
        <p>1 sản phẩm</p>
      </div>
    </div>

    <div class="stat-box red">
      <div class="icon-box">
        <i class="fas fa-ban"></i>
      </div>
      <div class="stat-info">
        <h3>ĐƠN HÀNG HỦY</h3>
        <p>2 đơn hàng</p>
      </div>
    </div>
  </div>

  <div class="content-section">
    <h2>SẢN PHẨM BÁN CHẠY</h2>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>Mã sản phẩm</th>
            <th>Tên sản phẩm</th>
            <th>Giá tiền</th>
            <th>Danh mục</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>DH1744164267</td>
            <td>Card màn hình ASUS TUF Gaming GeForce RTX 5090 32GB GDDR7</td>
            <td>416 VND</td>
            <td>Card màn hình</td>
          </tr>
          <tr>
            <td>DH1744164310</td>
            <td>Card màn hình ASUS TUF Gaming GeForce RTX 5090 32GB GDDR7</td>
            <td>208 VND</td>
            <td>Card màn hình</td>
          </tr>
          <tr>
            <td>DH1744184293</td>
            <td>Card màn hình MSI GeForce RTX 5090 32G VANGUARD SO</td>
            <td>495 VND</td>
            <td>Card màn hình</td>
          </tr>
          <tr>
            <td>DH1744184360</td>
            <td>Card màn hình GIGABYTE AORUS GeForce RTX 5090 MASTER ICE 32G</td>
            <td>109 VND</td>
            <td>Card màn hình</td>
          </tr>
          <tr>
            <td>DH1744184340  </td>
            <td>Card màn hình MSI GeForce RTX 5090 32G VENTUS 3X OC</td>
            <td>89 VND</td>
            <td>Card màn hình</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="content-section">
    <h2>TỔNG ĐƠN HÀNG</h2>
    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>ID đơn hàng</th>
            <th>Khách hàng</th>
            <th>Đơn hàng</th>
            <th>Số lượng</th>
            <th>Tổng tiền</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>DH1744164267</td>
            <td>Triệu Thanh Phú</td>
            <td>Card màn hình ASUS TUF Gaming GeForce RTX 5090 32GB GDDR7</td>
            <td>4 sản phẩm</td>
            <td>416 VND</td>
          </tr>
          <tr>
            <td>DH1744164310</td>
            <td>Nguyễn Thị Ngọc Cẩm</td>
            <td>Card màn hình ASUS TUF Gaming GeForce RTX 5090 32GB GDDR7</td>
            <td>2 sản phẩm</td>
            <td>208 VND</td>
          </tr>
          <tr>
            <td>DH1744184293</td>
            <td>Đặng Hoàng Phúc</td>
            <td>Card màn hình MSI GeForce RTX 5090 32G VANGUARD SO</td>
            <td>5 sản phẩm</td>
            <td>495 VND</td>
          </tr>
          <tr>
            <td>DH1744184340</td>
            <td>Nguyễn Thị Mỹ Yến</td>
            <td>Card màn hình MSI GeForce RTX 5090 32G VENTUS 3X OC</td>
            <td>1 sản phẩm</td>
            <td>89 VND</td>
          </tr>
          <tr>
            <td>DH1744184360</td>
            <td>Phạm Thị Ngọc</td>
            <td>Card màn hình GIGABYTE AORUS GeForce RTX 5090 MASTER ICE 32G</td>
            <td>1 sản phẩm</td>
            <td>109 VND</td>
          </tr>
          <tr>
            <td colspan="4" style="text-align: right; font-weight: bold;">Tổng cộng:</td>
            <td style="font-weight: bold;">1200 VND</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</body>
</html>
