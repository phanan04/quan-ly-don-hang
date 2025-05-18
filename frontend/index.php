<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TTGShop - Phụ kiện Game Thủ |Tư vấn Build PC| giá rẻ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"> 
    <link rel="stylesheet" href="/frontend/assets/css/style.css">
    <link rel="stylesheet" href="/frontend/assets/css/base.css">
</head>

<body>
    <?php include 'layouts/partials/header.php'; ?>
    
    <div class="container-fluid mt-3">
      <div class="row">
        <!-- Sidebar bên trái -->
        <div class="col-md-2 pe-0">
          <?php include 'layouts/partials/sidebar.php'; ?>
        </div>
        <!-- Nội dung chính bên phải -->
        <div class="col-md-10 ps-3">
          <!-- Banner chính -->
          <div class="row g-2 mb-4">
            <div class="col-md-8">
              <img src="assets/images/banner1.jpg?v=<?php echo time(); ?>" class="img-fluid rounded w-100" alt="Banner chính 1">
            </div>
            <div class="col-md-4 d-flex flex-column gap-2">
              <img src="assets/images/banner2.jpg" class="img-fluid rounded" alt="Banner phụ 2">
              <img src="assets/images/banner3.jpeg" class="img-fluid rounded" alt="Banner phụ 3">
            </div>
          </div>
        </div>
      </div>
    </div>

      <!-- New Products Section -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-header text-white" style="background-color: #212529">
              <h5 class="mb-0">SẢN PHẨM MỚI</h5>
            </div>
            <div class="card-body">
              <div class="row g-3">
                <?php
                // Kết nối database
                require_once __DIR__ . '/../backend/config.php';
                
                // Query lấy sản phẩm mới
                $sql = "SELECT * FROM san_pham 
                        WHERE trang_thai = 'Còn hàng' 
                        ORDER BY id DESC";
                $result = $conn->query($sql);

                if (!$result) {
                    die("Query failed: " . $conn->error);
                }

                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        // Sử dụng ma_san_pham để tạo đường dẫn ảnh dựa trên tên file thực tế
                        $image_path = '/quan-ly-don-hang/frontend/assets/images/product' . $row['ma_san_pham'] . '.webp';
                ?>
                <div class="col-md-2">
                  <div class="card h-100">
                    <img src="<?php echo $image_path; ?>" class="card-img-top" alt="<?php echo $row['ten_san_pham']; ?>">
                    <div class="card-body">
                      <h6 class="card-title"><?php echo $row['ten_san_pham']; ?></h6>
                      <p class="card-text text-danger fw-bold"><?php echo number_format($row['gia_tien']); ?>₫</p>
                    </div>
                  </div>
                </div>
                <?php
                    }
                } else {
                    echo "<div class='col-12 text-center'>Không có sản phẩm mới</div>";
                }
                $conn->close();
                ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php include 'layouts/partials/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>