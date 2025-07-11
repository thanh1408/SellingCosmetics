<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chi tiết sản phẩm - Mỹ phẩm</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="./assets/fonts/fontawesome-free-6.4.0-web/fontawesome-free-6.4.0-web/css/all.min.css">
</head>

<body>
    <!-- Header -->
    <header>
        <!-- Top info bar -->
        <div class="top-info">
            <div class="left"></div>
            <div class="right">
                <a href="#"><i class="fas fa-bell"></i> Thông Báo</a>
                <a href="login.php"><i class="fas fa-user"></i> Đăng nhập</a>
            </div>
        </div>

        <!-- Logo + search bar + cart -->
        <div class="topbar">
        <a href="home.php" class="logo">🌸 MYPHAM 563</a>
            <form class="search-box" method="GET" action="search.php">
                <input type="text" name="keyword" placeholder="Tìm kiếm sản phẩm...">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>

            <a href="cart.php" class="cart-icon">
                <i class="fas fa-shopping-cart"></i>
                <span class="cart-count">0</span>
            </a>
        </div>

        <!-- Navbar -->
        <nav class="navbar">
            <a href="home.php"><i class="fa-solid fa-house"></i></a>
            <a href="skincare.php">Skincare</a>
            <a href="#">Makeup</a>
            <a href="#">Haircare</a>
            <a href="#">Bodycare</a>
            <a href="#">Perfume</a>
        </nav>
    </header>

    <!-- Product Card -->
    <!-- Product List -->
    <div class="product-list">
    <div class="product-card">
            <div class="product-img">
                <img src="https://down-vn.img.susercontent.com/file/a740cc999ebc78acde421864a7258777.webp" alt="Son MAC chính hãng">

                <span class="badge discount">-24%</span>
            </div>
            <div class="product-info">
                <h3 class="product-title">Sữa Rửa Mặt Làm Sáng Da Reihaku Hatomugi Cleansing Foam 130g</h3>
                <div class="price">
                    <span class="old-price">59.000đ</span>
                    <span class="new-price">78.000đ</span>
                </div>
                <div class="extra-info">
                    <span class="rating">⭐ 4.9 | Đã bán 4.2k</span>
                    <span class="location">Bắc Giang 1</span>
                </div>
                <div class="product-actions">
                    <button class="add-to-cart"><i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng</button>
                    <form method="POST" action="checkout.php">
                        <input type="hidden" name="product_name" value="Son Tint lì cho môi căng mọc Hàn Quốc Romand">
                        <input type="hidden" name="product_price" value="185000">
                        <input type="hidden" name="product_option" value="130 trắng">
                        <input type="hidden" name="product_qty" value="1">
                        <input type="hidden" name="product_img" value="https://down-vn.img.susercontent.com/file/vn-11134201-7ras8-m1hzy5ps272r21">
                        <button class="buy-now" type="submit"><i class="fas fa-credit-card"></i> Mua ngay</button>
                    </form>
                </div>

            </div>
        </div>

        <div class="product-card">
            <div class="product-img">
                <img src="https://down-vn.img.susercontent.com/file/vn-11134207-7r98o-lmeejqn0w5wf97.webp" alt="Sản phẩm dưỡng da">
                <span class="badge discount">-20%</span>
            </div>
            <div class="product-info">
                <h3 class="product-title">Sữa rửa mặt Cerave Foaming, Hydrating, SA Smoothing Cleanser cho da dầu mụn, khô, nhạy cảm 88ML - 236ML - 473ML</h3>
                <div class="price">
                    <span class="old-price">363.000đ</span>
                    <span class="new-price">290.400đ</span>
                </div>
                <div class="extra-info">
                    <span class="rating">⭐ 4.8 | Đã bán 2.3k</span>
                    <span class="location">Bắc Giang 1</span>
                </div>
                <div class="product-actions">
                    <button class="add-to-cart"><i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng</button>
                    <button class="buy-now"><i class="fas fa-credit-card"></i> Mua ngay</button>
                </div>
            </div>
        </div>
        <div class="product-card">
            <div class="product-img">
                <img src="https://down-vn.img.susercontent.com/file/sg-11134201-7rcd4-lsuzq1yuccnge3" alt="Sản phẩm dưỡng da">
                <span class="badge discount">-37%</span>
            </div>
            <div class="product-info">
                <h3 class="product-title">Sữa Rửa Mặt Simple Refreshing Facial Wash 150ml - Dịu Nhẹ Cho Da Dầu Mụn, Nhạy Cảm- Dr Thêm</h3>
                <div class="price">
                    <span class="old-price">150.000đ</span>
                    <span class="new-price">94.000đ</span>
                </div>
                <div class="extra-info">
                    <span class="rating">⭐ 4.8 | Đã bán 2.3k</span>
                    <span class="location">Bắc Giang 1</span>
                </div>
                <div class="product-actions">
                    <button class="add-to-cart"><i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng</button>
                    <button class="buy-now"><i class="fas fa-credit-card"></i> Mua ngay</button>
                </div>
            </div>
        </div>

        <div class="product-card">
            <div class="product-img">
                <img src=https://down-vn.img.susercontent.com/file/vn-11134207-7ras8-m103a4gdf7yz88 alt="Sản phẩm dưỡng da">
                <span class="badge discount">-27%</span>
            </div>
            <div class="product-info">
                <h3 class="product-title">Sữa rửa mặt cho nam sạch dầu nhờn & ngừa mụn Men Stay Simplicity Facial Cleanser 100g với tinh dầu tràm trà organic</h3>
                <div class="price">
                    <span class="old-price">185.000đ</span>
                    <span class="new-price">135.000đ</span>
                </div>
                <div class="extra-info">
                    <span class="rating">⭐ 4.8 | Đã bán 2.3k</span>
                    <span class="location">Bắc Giang 1</span>
                </div>
                <div class="product-actions">
                    <button class="add-to-cart"><i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng</button>
                    <button class="buy-now"><i class="fas fa-credit-card"></i> Mua ngay</button>
                </div>
            </div>
        </div>

        <div class="product-card">
            <div class="product-img">
                <img src=https://down-vn.img.susercontent.com/file/vn-11134207-7r98o-lrzfnjv4kmv8c8  alt="Sản phẩm dưỡng da">
                <span class="badge discount">-50%</span>
            </div>
            <div class="product-info">
                <h3 class="product-title">Serum Tiam Vàng Niacinamide 10% + Arbutin 2% Phục Hồi Da Sáng Khỏe Sau Mụn Giảm Thâm Nám 40ML</h3>
                <div class="price">
                    <span class="old-price">650.000đ</span>
                    <span class="new-price">339.000đ</span>
                </div>
                <div class="extra-info">
                    <span class="rating">⭐ 4.8 | Đã bán 2.3k</span>
                    <span class="location">Bắc Giang 1</span>
                </div>
                <div class="product-actions">
                    <button class="add-to-cart"><i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng</button>
                    <button class="buy-now"><i class="fas fa-credit-card"></i> Mua ngay</button>
                </div>
            </div>
        </div>
    </div>
    <div class="cart-modal" id="cartModal">
            <div class="cart-modal-content">
                <span class="close-btn" onclick="closeCartModal()">&times;</span>
                <img src="assets/images/product1.jpg" alt="Sản phẩm" class="modal-img">
                <h3 id="modalTitle">Tên sản phẩm</h3>
                <p class="modal-price" id="modalPrice">Giá</p>
                <p class="modal-stock">Kho: 4558</p>

                <form method="POST" action="add_to_cart.php" id="addToCartForm">
                    <input type="hidden" name="product_name" id="modalProductName">
                    <input type="hidden" name="product_price" id="modalProductPrice">
                    <input type="hidden" name="product_quantity" id="modalProductQty" value="1">
                    <input type="hidden" name="product_option" id="modalProductOption">

                    <div class="modal-options">
                        <h4>Phân Loại</h4>
                        <div class="option-btn-group">
                            <button type="button" class="option-btn active" onclick="selectOption(this)">130 trắng</button>
                            <button type="button" class="option-btn" onclick="selectOption(this)">170 trắng</button>
                            <button type="button" class="option-btn" onclick="selectOption(this)">130 xanh da mụn</button>
                        </div>
                    </div>

                    <div class="modal-quantity">
                        <h4>Số lượng</h4>
                        <button type="button" onclick="decreaseQty()">-</button>
                        <input type="text" id="qtyInput" value="1">
                        <button type="button" onclick="increaseQty()">+</button>
                    </div>

                    <button type="submit" class="add-to-cart-btn">Thêm vào Giỏ hàng</button>
                </form>
            </div>
        </div>
        <script>
            // Mở modal và truyền dữ liệu vào
            function openCartModal(name, price, imgSrc) {
                document.getElementById('modalTitle').innerText = name;
                document.getElementById('modalProductName').value = name;
                document.getElementById('modalProductPrice').value = price;
                document.getElementById('modalPrice').innerText = price + 'đ';
                document.querySelector('.modal-img').src = imgSrc;
                document.getElementById('cartModal').style.display = 'block';
            }

            // Đóng modal
            function closeCartModal() {
                document.getElementById('cartModal').style.display = 'none';
            }

            // Chọn phân loại
            function selectOption(btn) {
                document.querySelectorAll('.option-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById('modalProductOption').value = btn.innerText;
            }

            // Tăng số lượng
            function increaseQty() {
                let qty = parseInt(document.getElementById('qtyInput').value);
                qty++;
                document.getElementById('qtyInput').value = qty;
                document.getElementById('modalProductQty').value = qty;
            }

            // Giảm số lượng
            function decreaseQty() {
                let qty = parseInt(document.getElementById('qtyInput').value);
                if (qty > 1) {
                    qty--;
                    document.getElementById('qtyInput').value = qty;
                    document.getElementById('modalProductQty').value = qty;
                }
            }

            // Đóng modal khi click ra ngoài
            window.onclick = function(event) {
                const modal = document.getElementById('cartModal');
                if (event.target === modal) modal.style.display = "none";
            };

            // Gán sự kiện cho tất cả nút "Thêm vào giỏ hàng"
            document.querySelectorAll('.add-to-cart').forEach(btn => {
                btn.addEventListener('click', function() {
                    const productCard = btn.closest('.product-card');
                    const name = productCard.querySelector('.product-title').innerText;
                    const price = productCard.querySelector('.new-price').innerText.replace('đ', '').trim();
                    const img = productCard.querySelector('img').src;

                    openCartModal(name, price, img);
                });
            });
        </script>
</body>

</html>