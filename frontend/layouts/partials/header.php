<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VGAShop - Cửa hàng linh kiện máy tính</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/quan-ly-don-hang/frontend/assets/css/base.css">
</head>
<body>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<!-- Header chính -->
<nav class="navbar navbar-expand-lg" >
  <div class="container">
    <!-- Logo -->
    <a class="navbar-brand d-flex align-items-center text-dark" href="/quan-ly-don-hang/frontend/">
      <img src="/quan-ly-don-hang/frontend/assets/images/logo.png" alt="TTGShop Logo" width="48" height="48" class="d-inline-block align-text-top me-2" style="background: #fff; border-radius: 8px; padding: 2px;">
      <span class="fw-bold text-primary" style="font-size: 2rem; letter-spacing: 1px;">
        VGAShop</span>
    </a>
    <!-- Thanh tìm kiếm -->
    <form class="d-flex mx-auto" style="width: 50%;">
      <input class="form-control me-2" type="search" placeholder="Tìm kiếm sản phẩm..." aria-label="Search">
      <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
    </form>
    <!-- Các nút bên phải -->
    <div class="d-flex align-items-center">
      <?php if(isset($_SESSION['user'])): ?>
        <div class="dropdown me-3">
          <a class="text-dark text-decoration-none dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person"></i> <?php echo htmlspecialchars($_SESSION['user']['username']); ?><br>
            <small>Tài khoản của tôi <i class="bi bi-chevron-down"></i></small>
          </a>
          <ul class="dropdown-menu" aria-labelledby="userDropdown">
            <li><a class="dropdown-item" href="/quan-ly-don-hang/frontend/layouts/pages/profile.php">Thông tin cá nhân</a></li>
            <li><a class="dropdown-item" href="/quan-ly-don-hang/frontend/layouts/pages/orders.php">Đơn hàng của tôi</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="/quan-ly-don-hang/backend/api/logout.php">Đăng xuất</a></li>
          </ul>
        </div>
      <?php else: ?>
        <a href="/quan-ly-don-hang/frontend/login.php" class="me-3 text-dark text-decoration-none">
          <i class="bi bi-person"></i> Đăng nhập / Đăng ký<br>
          <small>Tài khoản của tôi <i class="bi bi-chevron-down"></i></small>
        </a>
      <?php endif; ?>
      <a href="/quan-ly-don-hang/frontend/layouts/pages/cart.php" class="position-relative text-dark text-decoration-none">
        <i class="bi bi-bag" style="font-size: 1.5rem;"></i> Giỏ hàng
        <span id="cart-item-count" class="number position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
          <?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>
        </span>
      </a>
    </div>
  </div>
</nav>

<!-- Thanh menu ngang dưới header với bố cục chia 2 cột -->
<div class="container-fluid bg-dark py-2 mt-3">
  <div class="row">
    <!-- DANH MỤC SẢN PHẨM bên trái -->
    <div class="col-md-2 d-flex align-items-center text-white fw-bold ps-4" style="font-size: 1.1rem;">
      <i class="bi bi-list" style="font-size: 1.3rem;"></i>
      <span class="ms-2">DANH MỤC SẢN PHẨM</span>
    </div>

    <div class="col-md-10">
      <div class="d-flex align-items-center gap-4 flex-wrap">
        <span class="d-flex align-items-center text-white">
          <span class="bg-white rounded-circle d-flex justify-content-center align-items-center me-2" style="width: 28px; height: 28px;">
            <i class="bi bi-check-lg text-primary"></i>
          </span>
          Chất lượng đảm bảo
        </span>
        <span class="d-flex align-items-center text-white">
          <span class="bg-white rounded-circle d-flex justify-content-center align-items-center me-2" style="width: 28px; height: 28px;">
            <i class="bi bi-lightning-charge-fill text-primary"></i>
          </span>
          Vận chuyển siêu tốc
        </span>
        <span class="d-flex align-items-center text-white">
          <span class="bg-white rounded-circle d-flex justify-content-center align-items-center me-2" style="width: 28px; height: 28px;">
            <i class="bi bi-telephone-fill text-primary"></i>
          </span>
          Tư vấn Build PC: 0986552233
        </span>
      </div>
    </div>
  </div>
</div>




