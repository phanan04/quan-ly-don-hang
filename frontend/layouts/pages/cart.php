<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/quan-ly-don-hang/frontend/assets/css/style.css">
    <link rel="stylesheet" href="/quan-ly-don-hang/frontend/assets/css/base.css">
    <style>
        .cart-item {
            border: 1px solid #dee2e6;
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 5px;
        }
        .cart-summary {
            border: 1px solid #dee2e6;
            padding: 15px;
            border-radius: 5px;
            background-color: #f8f9fa;
        }
        .order-notes textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <?php include '../../layouts/partials/header.php'; ?>

    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/quan-ly-don-hang/frontend/index.php">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
            </ol>
        </nav>

        <div class="row">
            <!-- Cart Items Section -->
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>Giỏ hàng của bạn</h5>
                    </div>
                    <div class="card-body">
                        <div id="empty-cart-message" class="text-center py-4 d-none">
                            <i class="bi bi-cart-x" style="font-size: 3rem;"></i>
                            <p class="mt-3">Giỏ hàng của bạn đang trống</p>
                            <a href="/quan-ly-don-hang/frontend/index.php" class="btn btn-primary">Tiếp tục mua sắm</a>
                        </div>
                        
                        <div id="cart-items">
                            <!-- Cart items will be loaded here -->
                        </div>

                        <div id="cart-actions" class="mt-3 d-none">
                            <button class="btn btn-danger" onclick="clearCart()">Xóa hết giỏ hàng</button>
                        </div>
                    </div>
                </div>

                <!-- Order Notes Section -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5>Ghi chú đơn hàng</h5>
                    </div>
                    <div class="card-body order-notes">
                        <textarea id="order-notes" placeholder="Thêm ghi chú cho đơn hàng..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Order Summary Section -->
            <div class="col-md-4">
                <div class="cart-summary">
                    <h5>Thông tin đơn hàng</h5>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tổng tiền hàng:</span>
                        <span class="fw-bold" id="subtotal">0₫</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tổng cộng:</span>
                        <span class="text-danger fw-bold display-6" id="total">0₫</span>
                    </div>

                    <button class="btn btn-danger btn-lg w-100 mt-3" onclick="checkout()">THANH TOÁN</button>
                    
                    <div class="mt-3 p-3 bg-light rounded">
                        <h6>Chính sách mua hàng:</h6>
                        <p class="text-muted small mb-0">Hiện chúng tôi chỉ áp dụng thanh toán với đơn hàng có giá trị tối thiểu 40.000₫ trở lên.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../layouts/partials/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.min.js"></script>
    <script>
        // Format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(amount);
        }

        // Load cart items
        function loadCart() {
            fetch('/quan-ly-don-hang/backend/api/get_cart.php')
                .then(response => response.json())
                .then(data => {
                    const cartItems = document.getElementById('cart-items');
                    const emptyCartMessage = document.getElementById('empty-cart-message');
                    const cartActions = document.getElementById('cart-actions');
                    const cartItemCount = document.getElementById('cart-item-count');
                    const subtotal = document.getElementById('subtotal');
                    const total = document.getElementById('total');

                    if (data.items && data.items.length > 0) {
                        // Show cart items
                        cartItems.innerHTML = data.items.map(item => `
                            <div class="cart-item d-flex align-items-center">
                                <div class="flex-shrink-0 me-3">
                                    <img src="${item.hinh_anh ? '/quan-ly-don-hang/backend/' + item.hinh_anh : '/quan-ly-don-hang/frontend/assets/images/no-image.png'}" 
                                         alt="${item.ten_san_pham}" 
                                         style="width: 80px; height: 80px; object-fit: cover;" 
                                         class="rounded">
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">${item.ten_san_pham}</h6>
                                    <p class="text-danger fw-bold mb-1">${formatCurrency(item.gia_tien)}</p>
                                    <div class="input-group input-group-sm" style="width: 120px;">
                                        <button class="btn btn-outline-secondary" onclick="updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                                        <input type="text" class="form-control text-center" value="${item.quantity}" readonly>
                                        <button class="btn btn-outline-secondary" onclick="updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <button class="btn btn-danger btn-sm" onclick="removeFromCart(${item.id})">Xóa</button>
                                </div>
                            </div>
                        `).join('');

                        // Calculate totals
                        const subtotalAmount = data.subtotal;
                        const totalAmount = subtotalAmount; // Tổng cộng bằng tổng tiền hàng

                        // Update counts and totals
                        cartItemCount.textContent = data.items.length;
                        subtotal.textContent = formatCurrency(subtotalAmount);
                        total.textContent = formatCurrency(totalAmount);

                        // Show cart actions
                        cartActions.classList.remove('d-none');
                        emptyCartMessage.classList.add('d-none');
                    } else {
                        // Show empty cart message
                        cartItems.innerHTML = '';
                        cartActions.classList.add('d-none');
                        emptyCartMessage.classList.remove('d-none');
                        cartItemCount.textContent = '0';
                        subtotal.textContent = formatCurrency(0);
                        total.textContent = formatCurrency(0);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Update item quantity
        function updateQuantity(productId, newQuantity) {
            if (newQuantity < 1) return;
            
            fetch('/quan-ly-don-hang/backend/api/update_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: newQuantity
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    loadCart();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Remove item from cart
        function removeFromCart(productId) {
            if (confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                fetch('/quan-ly-don-hang/backend/api/remove_from_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: productId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        loadCart();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        // Clear cart
        function clearCart() {
            if (confirm('Bạn có chắc muốn xóa tất cả sản phẩm khỏi giỏ hàng?')) {
                fetch('/quan-ly-don-hang/backend/api/clear_cart.php', {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        loadCart();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        // Checkout
        function checkout() {
            // Get latest cart data before checking out
            fetch('/quan-ly-don-hang/backend/api/get_cart.php')
                .then(response => response.json())
                .then(cartData => {
                    if (cartData.status === 'success' && cartData.items.length > 0) {
                        const notes = document.getElementById('order-notes').value;
                        
                        // Safely get export invoice checkbox state, default to false if element not found
                        const exportInvoiceElement = document.getElementById('exportInvoice');
                        const exportInvoice = exportInvoiceElement ? exportInvoiceElement.checked : false;

                        // Safely get selected delivery time, default to 'Giao ngay' if none checked
                        const selectedDeliveryOption = document.querySelector('input[name="deliveryTime"]:checked');
                        const deliveryTime = selectedDeliveryOption ? selectedDeliveryOption.id : 'Giao ngay';

                        const orderPayload = {
                            items: cartData.items,
                            notes: notes,
                            invoice: exportInvoice,
                            delivery: deliveryTime
                        };

                        // Send order data to backend API
                        fetch('/quan-ly-don-hang/backend/api/create_order.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify(orderPayload)
                        })
                        .then(response => response.json())
                        .then(orderResponse => {
                            if (orderResponse.status === 'success') {
                                alert(orderResponse.message);
                                // Clear the cart after successful order
                                loadCart();
                            } else {
                                alert(orderResponse.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error creating order:', error);
                            alert('Đã xảy ra lỗi khi đặt hàng. Vui lòng thử lại.');
                        });

                    } else if (cartData.items.length === 0) {
                        alert('Giỏ hàng của bạn đang trống. Không thể đặt hàng.');
                    } else {
                         alert('Không thể lấy thông tin giỏ hàng. Vui lòng thử lại.');
                    }
                })
                .catch(error => {
                    console.error('Error fetching cart for checkout:', error);
                    alert('Đã xảy ra lỗi khi lấy thông tin giỏ hàng. Vui lòng thử lại.');
                });
        }

        // Load cart when page loads
        document.addEventListener('DOMContentLoaded', loadCart);
    </script>
</body>
</html>
