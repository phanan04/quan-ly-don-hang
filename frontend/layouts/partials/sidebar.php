
  <!-- Danh mục sản phẩm -->
  <div class="p-3">
    <ul class="list-group list-group-flush">
      <li class="list-group-item d-flex align-items-center border-0 px-0">
        <i class="bi bi-gpu-card me-2 text-primary"></i>
        <a href="/quan-ly-don-hang/frontend/layouts/pages/gpu.php" class="text-decoration-none text-dark">Card đồ họa (VGA)</a>
      </li>
      <li class="list-group-item d-flex align-items-center border-0 px-0">
        <i class="bi bi-cpu me-2 text-primary"></i>
        <a href="/category/cpu" class="text-decoration-none text-dark">Bộ xử lý (CPU)</a>
      </li>
      <li class="list-group-item d-flex align-items-center border-0 px-0">
        <i class="bi bi-motherboard me-2 text-primary"></i>
        <a href="/category/mainboard" class="text-decoration-none text-dark">Bo mạch chủ</a>
      </li>
      <li class="list-group-item d-flex align-items-center border-0 px-0">
        <i class="bi bi-memory me-2 text-primary"></i>
        <a href="/category/ram" class="text-decoration-none text-dark">Bộ nhớ (RAM)</a>
      </li>
      <li class="list-group-item d-flex align-items-center border-0 px-0">
        <i class="bi bi-lightning-charge me-2 text-primary"></i>
        <a href="/category/psu" class="text-decoration-none text-dark">Nguồn máy tính (PSU)</a>
      </li>
      <li class="list-group-item d-flex align-items-center border-0 px-0">
        <i class="bi bi-display me-2 text-primary"></i>
        <a href="/category/monitor" class="text-decoration-none text-dark">Màn hình</a>
      </li>
    </ul>
  </div>

<?php
// Chỉ hiển thị bộ lọc sản phẩm trên trang gpu.php
if (strpos($_SERVER['PHP_SELF'], '/gpu.php') !== false):
?>
  <!-- Bộ lọc Nhà cung cấp -->
  <div class="p-3 border-bottom">
    <h6 class="text-uppercase text-muted mb-2">Nhà cung cấp</h6>
    <ul class="list-group list-group-flush">
      <li class="list-group-item border-0 px-0">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="Khac" id="supplierKhac">
          <label class="form-check-label" for="supplierKhac">Khác</label>
        </div>
      </li>
      <li class="list-group-item border-0 px-0">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="Gigabyte" id="supplierGigabyte">
          <label class="form-check-label" for="supplierGigabyte">Gigabyte</label>
        </div>
      </li>
      <li class="list-group-item border-0 px-0">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="ASUS" id="supplierASUS">
          <label class="form-check-label" for="supplierASUS">ASUS</label>
        </div>
      </li>
      <li class="list-group-item border-0 px-0">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="Colorful" id="supplierColorful">
          <label class="form-check-label" for="supplierColorful">Colorful</label>
        </div>
      </li>
      <li class="list-group-item border-0 px-0">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="OCPC" id="supplierOCPC">
          <label class="form-check-label" for="supplierOCPC">OCPC</label>
        </div>
      </li>
      <li class="list-group-item border-0 px-0">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="Zotac" id="supplierZotac">
          <label class="form-check-label" for="supplierZotac">Zotac</label>
        </div>
      </li>
      <li class="list-group-item border-0 px-0">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="MSI" id="supplierMSI">
          <label class="form-check-label" for="supplierMSI">MSI</label>
        </div>
      </li>
      <li class="list-group-item border-0 px-0">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="Yeston" id="supplierYeston">
          <label class="form-check-label" for="supplierYeston">Yeston</label>
        </div>
      </li>
    </ul>
  </div>

  <!-- Bộ lọc Lọc giá -->
  <div class="p-3">
    <h6 class="text-uppercase text-muted mb-2">Lọc giá</h6>
    <ul class="list-group list-group-flush">
      <li class="list-group-item border-0 px-0">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="duoi-5m" id="priceDuoi5M">
          <label class="form-check-label" for="priceDuoi5M">Dưới 5.000.000₫</label>
        </div>
      </li>
      <li class="list-group-item border-0 px-0">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="5m-10m" id="price5M10M">
          <label class="form-check-label" for="price5M10M">5.000.000₫ - 10.000.000₫</label>
        </div>
      </li>
      <li class="list-group-item border-0 px-0">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="10m-15m" id="price10M15M">
          <label class="form-check-label" for="price10M15M">10.000.000₫ - 15.000.000₫</label>
        </div>
      </li>
      <li class="list-group-item border-0 px-0">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="15m-20m" id="price15M20M">
          <label class="form-check-label" for="price15M20M">15.000.000₫ - 20.000.000₫</label>
        </div>
      </li>
      <li class="list-group-item border-0 px-0">
        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="tren-20m" id="priceTren20M">
          <label class="form-check-label" for="priceTren20M">Trên 20.000.000₫</label>
        </div>
      </li>
    </ul>
  </div>
<?php endif; ?>

<style>
.list-group-item {
  transition: all 0.3s ease;
}
.list-group-item:hover {
  background-color: #F3F5FC;
  padding-left: 10px !important;
}
.list-group-item a {
  transition: color 0.3s ease;
}
.list-group-item:hover a {
  color: #0d6efd !important;
}
</style>
