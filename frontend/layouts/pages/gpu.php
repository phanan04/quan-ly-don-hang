<?php include '../../layouts/partials/header.php'; ?>

<div class="container-fluid mt-3">
    <div class="row">
        
        
        <!-- Nội dung chính bên phải -->
        <div class="col-md-10 ps-3">
            

            <div class="row">
                <!-- Bộ lọc sản phẩm -->
                <div class="col-md-3">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Bộ lọc sản phẩm</h5>
                        </div>
                        <div class="card-body">
                            <!-- Lọc theo hãng -->
                            <div class="mb-3">
                                <h6>Hãng sản xuất</h6>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="asus">
                                    <label class="form-check-label" for="asus">ASUS</label>
                                </div>
                                
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="msi">
                                    <label class="form-check-label" for="msi">MSI</label>
                                </div>
                            </div>

                            <!-- Lọc theo giá -->
                            <div class="mb-3">
                                <h6>Khoảng giá</h6>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="price1">
                                    <label class="form-check-label" for="price1">Dưới 100 triệu</label>
                                </div>
                                
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="price4">
                                    <label class="form-check-label" for="price4">Trên 100 triệu</label>
                                </div>
                            </div>

                            <!-- Lọc theo series -->
                            <div class="mb-3">
                                <h6>Series</h6>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="rtx40">
                                    <label class="form-check-label" for="rtx40">RTX 50 Series</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="rtx30">
                                    <label class="form-check-label" for="rtx30">RTX 40 Series</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Danh sách sản phẩm -->
                <div class="col-md-9">
                    <!-- Tiêu đề và bộ lọc -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h3 class="mb-0">Card Đồ Họa </h3>
                            <small class="text-muted" id="product-count">0 sản phẩm</small>
                        </div>
                        <div class="d-flex gap-2">
                            <div class="dropdown">
                                <button class="btn btn-light dropdown-toggle" type="button" id="dropdownSapXep" data-bs-toggle="dropdown" aria-expanded="false">
                                    Sắp xếp
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dropdownSapXep">
                                    <li><a class="dropdown-item" href="#" data-sort="newest">Mới nhất</a></li>
                                    <li><a class="dropdown-item" href="#" data-sort="price-asc">Giá tăng dần</a></li>
                                    <li><a class="dropdown-item" href="#" data-sort="price-desc">Giá giảm dần</a></li>
                                    <li><a class="dropdown-item" href="#" data-sort="popular">Bán chạy</a></li>
                                </ul>
                            </div>
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-light active" data-view="grid"><i class="bi bi-grid"></i></button>
                                <button type="button" class="btn btn-light" data-view="list"><i class="bi bi-list"></i></button>
                            </div>
                        </div>
                    </div>

                    <!-- Grid sản phẩm -->
                    <div class="row row-cols-1 row-cols-md-3 g-4" id="product-grid">
                        <!-- Products will be loaded here -->
                    </div>

                    <!-- Phân trang -->
                    <nav class="mt-4">
                        <ul class="pagination justify-content-center" id="pagination">
                            <!-- Pagination will be loaded here -->
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../layouts/partials/footer.php'; ?>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Function to load products
function loadProducts(page = 1) {
    fetch(`/quan-ly-don-hang/backend/api/get_products.php?category=GPU&page=${page}`)
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                const products = data.data;
                const productGrid = document.getElementById('product-grid');
                const productCount = document.getElementById('product-count');
                
                // Update product count
                productCount.textContent = `${products.length} sản phẩm`;
                
                // Clear existing products
                productGrid.innerHTML = '';
                
                // Add products to grid
                products.forEach(product => {
                    const productCard = `
                        <div class="col">
                            <div class="card h-100">
                                <div class="position-relative">
                                    <img src="${product.hinh_anh ? '/quan-ly-don-hang/backend/' + product.hinh_anh : '/quan-ly-don-hang/frontend/assets/images/no-image.png'}" class="card-img-top" alt="${product.ten_san_pham}">
                                    ${product.trang_thai === 'New' ? '<span class="position-absolute top-0 end-0 badge bg-danger m-2">New</span>' : ''}
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title">${product.ten_san_pham}</h5>
                                    <p class="card-text text-danger fw-bold">${new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(product.gia_tien)}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <button class="btn btn-primary" onclick="addToCart(${product.id})">Thêm vào giỏ</button>
                                        <button class="btn btn-outline-primary" onclick="addToWishlist(${product.id})"><i class="bi bi-heart"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    productGrid.innerHTML += productCard;
                });
            }
        })
        .catch(error => console.error('Error:', error));
}

// Load products when page loads
document.addEventListener('DOMContentLoaded', () => {
    loadProducts();
});

// Function to add to cart
function addToCart(productId) {
    fetch('/quan-ly-don-hang/backend/api/add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // Show success message
            alert(data.message);
            // Update cart count in header if exists
            const cartCountElement = document.getElementById('cart-count');
            if (cartCountElement) {
                cartCountElement.textContent = data.cart_count;
            }
        } else {
            // Show error message
            alert(data.message);
            // If not logged in, redirect to login page
            if (data.message.includes('đăng nhập')) {
                window.location.href = '/quan-ly-don-hang/frontend/layouts/pages/login.php';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng');
    });
}

// Function to add to wishlist
function addToWishlist(productId) {
    // Implement wishlist functionality
    console.log('Adding to wishlist:', productId);
}

// Handle sorting
document.querySelectorAll('[data-sort]').forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault();
        const sortType = e.target.dataset.sort;
        // Implement sorting logic
        console.log('Sorting by:', sortType);
    });
});

// Handle view switching
document.querySelectorAll('[data-view]').forEach(button => {
    button.addEventListener('click', (e) => {
        e.preventDefault();
        const viewType = e.target.dataset.view;
        // Implement view switching logic
        console.log('Switching to view:', viewType);
    });
});
</script>
