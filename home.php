<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chi tiết sản phẩm - Mỹ phẩm</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="./assets/fonts/fontawesome-free-6.4.0-web/fontawesome-free-6.4.0-web/css/all.min.css">
</head>

<body>

    <?php
    session_start();
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    ?>
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
<div class="main-content">
    <nav class="category">
                             <h3 class="category__heading">
                                 <i class="category__heading_icon fa-solid fa-list"></i>
                                 DANH MỤC
                             </h3>
                             <ul class="category-list">
                                 <li class="category-item ">
                                     <a href="skincare.php" class="category-item__link">Skincare</a>
                                 </li>
             
                                 <li class="category-item">
                                     <a href="makeup.php" class="category-item__link">Makeup</a>
                                 </li>
             
                                 <li class="category-item">
                                     <a href="haircare.php" class="category-item__link">Haircare</a>
                                 </li>
                                 <li class="category-item">
                                     <a href="bodycare.php" class="category-item__link">Bodycare</a>
                                 </li>
                                 <li class="category-item">
                                     <a href="perfume.php" class="category-item__link">Perfume</a>
                                 </li>
                             </ul>
                         </nav>
    <!-- Product Card -->
    <!-- Product List -->
    <div class="product-list">
        <div class="product-card" data-id = "p1" >
            <div class="product-img">
                <img src="https://down-vn.img.susercontent.com/file/a740cc999ebc78acde421864a7258777.webp" alt="Son MAC chính hãng">

                <span class="badge discount">-33%</span>
            </div>
            <div class="product-info">
                <h3 class="product-title">Sữa Rửa Mặt Ý Dĩ Hatomugi nội địa Nhật Bản 130g giúp da trắng sáng</h3>
                <div class="price">
                    <span class="old-price">60.000đ</span>
                    <span class="new-price">40.000đ</span>
                </div>
                <div class="extra-info">
                    <span class="rating">⭐ 4.9 | Đã bán 55.2k</span>
                    <span class="location">Bắc Giang</span>
                </div>
                <div class="product-actions">
                <button class="add-to-cart" onclick="openCartModal('Sữa Rửa Mặt Ý Dĩ Hatomugi nội địa Nhật Bản 130g 170g giúp da trắng sáng', '185000', 'https://down-vn.img.susercontent.com/file/a740cc999ebc78acde421864a7258777.webp')">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                    </button>
                    <form method="POST" action="checkout.php">
                        <input type="hidden" name="product_name" value="Sữa Rửa Mặt Ý Dĩ Hatomugi nội địa Nhật Bản 130g 170g giúp da trắng sáng">
                        <input type="hidden" name="product_price" value="185000">
                        <input type="hidden" name="product_option" value="130 trắng">
                        <input type="hidden" name="product_qty" value="1" min = "1">
                        <input type="hidden" name="product_img" value="https://down-vn.img.susercontent.com/file/a740cc999ebc78acde421864a7258777.webp">
                        <button class="buy-now" type="submit"><i class="fas fa-credit-card"></i> Mua ngay</button>
                    </form>
                </div>
            </div>     
        </div>

      
      
        <div class="product-card" data-id = "p2">
            <div class="product-img">
                <img src="https://down-vn.img.susercontent.com/file/vn-11134207-7qukw-lfxjx5kitxx37b" alt="Sản phẩm dưỡng da">
                <span class="badge discount">-20%</span>
            </div>
            <div class="product-info">
                <h3 class="product-title">Sữa rửa mặt Cerave 236ml làm sạch sâu dưỡng ẩm cho da dầu mụn, da thường, da khô</h3>
                <div class="price">
                    <span class="old-price">110.000đ</span>
                    <span class="new-price">88.000đ</span>
                </div>
                <div class="extra-info">
                    <span class="rating">⭐ 4.9 | Đã bán 10.3k</span>
                    <span class="location">Hà Nội</span>
                </div>
                <div class="product-actions">
                <button class="add-to-cart" onclick="openCartModal('Sữa rửa mặt Cerave 236ml làm sạch sâu dưỡng ẩm cho da dầu mụn, da thường, da khô', '185000', 'https://down-vn.img.susercontent.com/file/vn-11134207-7qukw-lfxjx5kitxx37b')">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                    </button>
                    <form method="POST" action="checkout.php">
                        <input type="hidden" name="product_name" value="Sữa rửa mặt Cerave 236ml làm sạch sâu dưỡng ẩm cho da dầu mụn, da thường, da khô">
                        <input type="hidden" name="product_price" value="185000">
                        <input type="hidden" name="product_option" value="130 trắng">
                        <input type="hidden" name="product_qty" value="1" min = "1">
                        <input type="hidden" name="product_img" value="https://down-vn.img.susercontent.com/file/vn-11134207-7qukw-lfxjx5kitxx37b">
                        <button class="buy-now" type="submit"><i class="fas fa-credit-card"></i> Mua ngay</button>
                    </form>
                </div>
            </div>
        </div>



        <div class="product-card" data-id = "p3">
            <div class="product-img">
                <img src="https://down-vn.img.susercontent.com/file/vn-11134207-7ra0g-m8iqk98jisg725" alt="Sản phẩm dưỡng da">
                <span class="badge discount">-47%</span>
            </div>
            <div class="product-info">
                <h3 class="product-title">Serum Phục Hồi Da Sáng Khỏe Sau Mụn Tia'M Vita B3 Source 40Ml </h3>
                <div class="price">
                    <span class="old-price">559.000đ</span>
                    <span class="new-price">295.000đ</span>
                </div>
                <div class="extra-info">
                    <span class="rating">⭐ 4.8 | Đã bán 2.3k</span>
                    <span class="location">Hồ Chí Minh</span>
                </div>
                <div class="product-actions">
                <button class="add-to-cart" onclick="openCartModal('Serum Phục Hồi Da Sáng Khỏe Sau Mụn TiaM Vita B3 Source 40Ml ', '185000', 'https://down-vn.img.susercontent.com/file/vn-11134207-7ra0g-m8iqk98jisg725')">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                    </button>
                    <form method="POST" action="checkout.php">
                        <input type="hidden" name="product_name" value="Serum Phục Hồi Da Sáng Khỏe Sau Mụn Tia'M Vita B3 Source 40Ml ">
                        <input type="hidden" name="product_price" value="185000">
                        <input type="hidden" name="product_option" value="130 trắng">
                        <input type="hidden" name="product_qty" value="1" min = "1">
                        <input type="hidden" name="product_img" value="https://down-vn.img.susercontent.com/file/vn-11134207-7ra0g-m8iqk98jisg725">
                        <button class="buy-now" type="submit"><i class="fas fa-credit-card"></i> Mua ngay</button>
                    </form>
                </div>
            </div>
        </div>



        <div class="product-card" data-id = "p4">
            <div class="product-img">
                <img src=https://down-vn.img.susercontent.com/file/sg-11134201-7rfhg-m3kh553myq7bc8 alt="Sản phẩm dưỡng da">
                <span class="badge discount">-45%</span>
            </div>
            <div class="product-info">
                <h3 class="product-title">Bảng phấn mắt nhũ tương 8 hình trái tim có điểm nổi bật nhũ tương, Bảng phấn mắt 3in1 với má hồng</h3>
                <div class="price">
                    <span class="old-price">66.000</span>
                    <span class="new-price">36.300đ</span>
                </div>
                <div class="extra-info">
                    <span class="rating">⭐ 4.8 | Đã bán 2.3k</span>
                    <span class="location">Hồ Chí Minh</span>
                </div>
                <div class="product-actions">
                <button class="add-to-cart" onclick="openCartModal('Bảng phấn mắt nhũ tương 8 hình trái tim có điểm nổi bật nhũ tương, Bảng phấn mắt 3in1 với má hồng', '36300', 'https://down-vn.img.susercontent.com/file/sg-11134201-7rfhg-m3kh553myq7bc8')">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                    </button>
                    <form method="POST" action="checkout.php">
                        <input type="hidden" name="product_name" value="Bảng phấn mắt nhũ tương 8 hình trái tim có điểm nổi bật nhũ tương, Bảng phấn mắt 3in1 với má hồng">
                        <input type="hidden" name="product_price" value="185000">
                        <input type="hidden" name="product_option" value="130 trắng">
                        <input type="hidden" name="product_qty" value="1" min = "1">
                        <input type="hidden" name="product_img" value="https://down-vn.img.susercontent.com/file/sg-11134201-7rfhg-m3kh553myq7bc8">
                        <button class="buy-now" type="submit"><i class="fas fa-credit-card"></i> Mua ngay</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="product-card" data-id = "p5">
            <div class="product-img">
                <img src=https://down-vn.img.susercontent.com/file/vn-11134207-7ras8-m27i0toqrc8id9 alt="Sản phẩm dưỡng da">
                <span class="badge discount">-4%</span>
            </div>
            <div class="product-info">
                <h3 class="product-title">Tinh Chất oh!oh! Skin Health Serum (with 20% Niacinamide & 2% Acetyl Glucosamine) (10ml - 30ml)</h3>
                <div class="price">
                    <span class="old-price">325.000đ</span>
                    <span class="new-price">311.000đ</span>
                </div>
                <div class="extra-info">
                    <span class="rating">⭐ 4.8 | Đã bán 12.3k</span>
                    <span class="location">Hồ Chí Minh</span>
                </div>
                <div class="product-actions">
                <button class="add-to-cart" onclick="openCartModal('Tinh Chất oh!oh! Skin Health Serum (with 20% Niacinamide & 2% Acetyl Glucosamine) (10ml - 30ml)', '185000', 'https://down-vn.img.susercontent.com/file/vn-11134207-7ras8-m27i0toqrc8id9')">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                    </button>
                    <form method="POST" action="checkout.php">
                        <input type="hidden" name="product_name" value="Tinh Chất oh!oh! Skin Health Serum (with 20% Niacinamide & 2% Acetyl Glucosamine) (10ml - 30ml)">
                        <input type="hidden" name="product_price" value="185000">
                        <input type="hidden" name="product_option" value="130 trắng">
                        <input type="hidden" name="product_qty" value="1" min = "1">
                        <input type="hidden" name="product_img" value="https://down-vn.img.susercontent.com/file/vn-11134207-7ras8-m27i0toqrc8id9">
                        <button class="buy-now" type="submit"><i class="fas fa-credit-card"></i> Mua ngay</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="product-card" data-id = "p6">
            <div class="product-img">
                <img src=https://down-vn.img.susercontent.com/file/vn-11134207-7ra0g-m8lkwxcx3ix3fe alt="Sản phẩm dưỡng da">
                <span class="badge discount">-60%</span>
            </div>
            <div class="product-info">
                <h3 class="product-title">Son Tint lì cho môi căng mọng Hàn Quốc Romand Juicy Lasting Tint 5.5g</h3>
                <div class="price">
                    <span class="old-price">419.000đ</span>
                    <span class="new-price">269.000đ</span>
                </div>
                <div class="extra-info">
                    <span class="rating">⭐ 4.8 | Đã bán 2.3k</span>
                    <span class="location">Hồ Chí Minh</span>
                </div>
                <div class="product-actions">
                <button class="add-to-cart" onclick="openCartModal('Son bóng dưỡng môi bắt sáng 3CE Shine Reflector 1.7g', '185000', 'https://down-vn.img.susercontent.com/file/vn-11134207-7ra0g-m8lkwxcx3ix3fe')">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                    </button>
                    <form method="POST" action="checkout.php">
                        <input type="hidden" name="product_name" value="Son bóng dưỡng môi bắt sáng 3CE Shine Reflector 1.7g">
                        <input type="hidden" name="product_price" value="185000">
                        <input type="hidden" name="product_option" value="130 trắng">
                        <input type="hidden" name="product_qty" value="1" min = "1">
                        <input type="hidden" name="product_img" value="https://down-vn.img.susercontent.com/file/vn-11134207-7ra0g-m8lkwxcx3ix3fe">
                        <button class="buy-now" type="submit"><i class="fas fa-credit-card"></i> Mua ngay</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="product-card" data-id = "p7">
            <div class="product-img">
                <img src=https://down-vn.img.susercontent.com/file/vn-11134201-7ra0g-m8cwtleitrgkb7 alt="Sản phẩm dưỡng da">
                <span class="badge discount">-20%</span>
            </div>
            <div class="product-info">
                <h3 class="product-title">Gel Mờ Sẹo Và Vết Thâm Scar Care Acnes 12Gr</h3>
                <div class="price">
                    <span class="old-price">82.000đ</span>
                    <span class="new-price">65.600đ</span>
                </div>
                <div class="extra-info">
                    <span class="rating">⭐ 4.8 | Đã bán 8.3k</span>
                    <span class="location">Phú Thọ 2</span>
                </div>
                <div class="product-actions">
                <button class="add-to-cart" onclick="openCartModal('Gel Mờ Sẹo Và Vết Thâm Scar Care Acnes 12Gr', '185000', 'https://down-vn.img.susercontent.com/file/vn-11134201-7ra0g-m8cwtleitrgkb7')">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                    </button>
                    <form method="POST" action="checkout.php">
                        <input type="hidden" name="product_name" value="Gel Mờ Sẹo Và Vết Thâm Scar Care Acnes 12Gr">
                        <input type="hidden" name="product_price" value="185000">
                        <input type="hidden" name="product_option" value="130 trắng">
                        <input type="hidden" name="product_qty" value="1" min = "1">
                        <input type="hidden" name="product_img" value="https://down-vn.img.susercontent.com/file/vn-11134201-7ra0g-m8cwtleitrgkb7">
                        <button class="buy-now" type="submit"><i class="fas fa-credit-card"></i> Mua ngay</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="product-card" data-id = "p8">
            <div class="product-img">
                <img src=https://down-vn.img.susercontent.com/file/vn-11134201-7ra0g-m8y6psofy1kyad alt="Sản phẩm dưỡng da">
                <span class="badge discount">-46%</span>
            </div>
            <div class="product-info">
                <h3 class="product-title">Nước Tẩy Trang làm sạch sâu dịu nhẹ cho mọi loại da - Garnier Micellar Cleansing Water 400ml</h3>
                <div class="price">
                    <span class="old-price">398.000</span>
                    <span class="new-price">254.000đ</span>
                </div>
                <div class="extra-info">
                    <span class="rating">⭐ 4.8 | Đã bán 6.3k</span>
                    <span class="location">Hồ Chí Minh</span>
                </div>
                <div class="product-actions">
                    <button class="add-to-cart" onclick="openCartModal('Nước Tẩy Trang làm sạch sâu dịu nhẹ cho mọi loại da - Garnier Micellar Cleansing Water 400ml', '185000', 'https://down-vn.img.susercontent.com/file/vn-11134201-7ra0g-m8y6psofy1kyad')">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                    </button>
                    <form method="POST" action="checkout.php">
                        <input type="hidden" name="product_name" value="Nước Tẩy Trang làm sạch sâu dịu nhẹ cho mọi loại da - Garnier Micellar Cleansing Water 400ml">
                        <input type="hidden" name="product_price" value="185000">
                        <input type="hidden" name="product_option" value="130 trắng">
                        <input type="hidden" name="product_qty" value="1" min = "1">
                        <input type="hidden" name="product_img" value="https://down-vn.img.susercontent.com/file/vn-11134201-7ra0g-m8y6psofy1kyad">
                        <button class="buy-now" type="submit"><i class="fas fa-credit-card"></i> Mua ngay</button>
                    </form>
                </div>
            </div>
        </div>
       
        <div class="product-card" data-id = "p9">
            <div class="product-img">
                <img src=https://down-vn.img.susercontent.com/file/vn-11134207-7ras8-m26fczqyqdgydf alt="Sản phẩm dưỡng da">
                <span class="badge discount">-46%</span>
            </div>
            <div class="product-info">
                <h3 class="product-title">Combo Simple Cho Da Nhạy Cảm - Nước Tẩy Trang (NTT), Sữa Rửa Mặt (SRM), Toner, Kem Dưỡng (KD)</h3>
                <div class="price">
                    <span class="old-price">240.000</span>
                    <span class="new-price">120.000đ</span>
                </div>
                <div class="extra-info">
                    <span class="rating">⭐ 4.8 | Đã bán 2.0k</span>
                    <span class="location">Hồ Chí Minh</span>
                </div>
                <div class="product-actions">
                <button class="add-to-cart" onclick="openCartModal('Combo Simple Cho Da Nhạy Cảm - Nước Tẩy Trang (NTT), Sữa Rửa Mặt (SRM), Toner, Kem Dưỡng (KD)', '185000', 'https://down-vn.img.susercontent.com/file/vn-11134207-7ras8-m26fczqyqdgydf')">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                    </button>
                    <form method="POST" action="checkout.php">
                        <input type="hidden" name="product_name" value="Combo Simple Cho Da Nhạy Cảm - Nước Tẩy Trang (NTT), Sữa Rửa Mặt (SRM), Toner, Kem Dưỡng (KD)">
                        <input type="hidden" name="product_price" value="185000">
                        <input type="hidden" name="product_option" value="130 trắng">
                        <input type="hidden" name="product_qty" value="1" min = "1">
                        <input type="hidden" name="product_img" value="https://down-vn.img.susercontent.com/file/vn-11134207-7ras8-m26fczqyqdgydf">
                        <button class="buy-now" type="submit"><i class="fas fa-credit-card"></i> Mua ngay</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="product-card" data-id = "p10">
            <div class="product-img">
                <img src="https://down-vn.img.susercontent.com/file/vn-11134207-7r98o-lstqguefuimcd2" alt="Sản phẩm dưỡng da">
                <span class="badge discount">-34%</span>
            </div>
            <div class="product-info">
                <h3 class="product-title">Son Kem 3CE Velvet Lip Tint Taupe Speak Up Daffodil Bitter Hour Child Like 4g - Mibebe</h3>
                <div class="price">
                    <span class="old-price">400.000</span>
                    <span class="new-price">264.000đ</span>
                </div>
                <div class="extra-info">
                    <span class="rating">⭐ 5.0 | Đã bán 9.3k</span>
                    <span class="location">Hồ Chí Minh</span>
                </div>
                <div class="product-actions">
                    <button class="add-to-cart" onclick="openCartModal('Son Kem 3CE Velvet Lip Tint Taupe Speak Up Daffodil Bitter Hour Child Like 4g - Mibebe', '185000', 'https://down-vn.img.susercontent.com/file/vn-11134207-7r98o-lstqguefuimcd2')">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                    </button>
                    <form method="POST" action="checkout.php">
                        <input type="hidden" name="product_name" value="Son Kem 3CE Velvet Lip Tint Taupe Speak Up Daffodil Bitter Hour Child Like 4g - Mibebe">
                        <input type="hidden" name="product_price" value="185000">
                        <input type="hidden" name="product_option" value="130 trắng">
                        <input type="hidden" name="product_qty" value="1" min = "1">
                        <input type="hidden" name="product_img" value="https://down-vn.img.susercontent.com/file/vn-11134207-7r98o-lstqguefuimcd2">
                        <button class="buy-now" type="submit"><i class="fas fa-credit-card"></i> Mua ngay</button>
                    </form>
                </div>
            </div>
        </div>
        <!-- Modal Thêm vào giỏ hàng -->
        <div class="cart-modal" id="cartModal">
            <div class="cart-modal-content">
                <span class="close-btn" onclick="closeCartModal()">&times;</span>

                <!-- Gộp ảnh và thông tin sản phẩm vào 1 hàng -->
                <div class="modal-header">
                    <img src="assets/images/product1.jpg" alt="Sản phẩm" class="modal-img">
                    <div class="product-info">
                        <h3 id="modalTitle">Tên sản phẩm</h3>
                        <p class="modal-price" id="modalPrice">Giá</p>
                        <p class="modal-stock">Kho: 10.000</p>
                    </div>
                </div>

                <form method="POST" action="add_to_cart.php" id="addToCartForm">
                    <input type="hidden" name="product_name" id="modalProductName">
                    <input type="hidden" name="product_price" id="modalProductPrice">
                    <input type="hidden" name="product_quantity" id="modalProductQty" value="1">
                    <input type="hidden" name="product_option" id="modalProductOption">

                    <div class="modal-options">
                        <h4>Phân Loại</h4>
                        <div class="option-btn-group">
                            <button type="button" class="option-btn active" onclick="selectOption(this)" data-img="assets/images/product1.jpg" data-price="120000">130 trắng</button>
                            <button type="button" class="option-btn" onclick="selectOption(this)" data-img="assets/images/product2.jpg" data-price="130000">170 trắng</button>
                            <button type="button" class="option-btn" onclick="selectOption(this)" data-img="assets/images/product3.jpg" data-price="125000">130 xanh da mụn</button>
                         </div>   
                    </div>

                    <div class="modal-quantity">
                        <h4>Số lượng</h4>
                        <button type="button" onclick="decreaseQty()">-</button>
                        <input type="text" id="qtyInput" value="1">
                        <button type="button" onclick="increaseQty()">+</button>
                    </div>

                    <button class="add-to-cart-btn">Thêm vào giỏ hàng</button>
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





const productVariants = {
        p1: [
            { name: '130g trắng', img: 'assets/images/hatomogi_trang.jpg', price: 40000 },
            { name: '130g xanh', img: 'assets/images/hatomogi_xanh.jpg', price: 40000 }
        ],
        p2: [
            { name: '88ml', img: 'assets/images/product2.jpg', price: 88000 },
            { name: '236ml', img: 'assets/images/product2.jpg', price: 284000 }
        ],
        p3: [
            { name: 'Vàng', img: 'assets/images/p31.jpg', price: 295000 },
            { name: 'Đen', img: 'assets/images/p32.jpg', price: 295000 },
            { name: 'Trắng', img: 'assets/images/p33.jpg', price: 295000 }
        ],
        p4: [
            { name: 'A01#', img: 'assets/images/p41.jpg', price: 36300 },
            { name: 'A02#', img: 'assets/images/p42.jpg', price: 39600 }
        ],
        p5: [
            { name: '10ml', img: 'assets/images/p5.jpg', price: 311000 },
            { name: '30ml', img: 'assets/images/p5.jpg', price: 651000 }
        ],
        p6: [
            { name: 'JUICY 20 + Glas 16', img: 'assets/images/p61.jpg', price: 269000 },
            { name: 'JUICY 23 + Glas 16', img: 'assets/images/p62.jpg', price: 269000 }
        ],
        p7: [
            { name: '60ml', img: 'assets/images/p7.jpg', price: 65600 }
        ],
        p8: [
            { name: 'Sạch da giảm nhờn', img: 'assets/images/p81.jpg', price: 254000 },
            { name: 'Dịu nhẹ da nhạy', img: 'assets/images/p82.jpg', price: 254000 },
            { name: 'Dành cho da sạm', img: 'assets/images/p83.jpg', price: 254000 }
        ],
        p9: [
            { name: 'NTT + SRM', img: 'assets/images/p9.jpg', price: 165000 },
            { name: 'NTT + KD', img: 'assets/images/p9.jpg', price: 120000 },
            { name: 'SRM + KD', img: 'assets/images/p9.jpg', price: 139000 },
            { name: 'NTT + TONER + SRm + KD', img: 'assets/images/p9.jpg', price: 304000 }
        ],
        p10: [
            { name: 'Taupe', img: 'assets/images/p101.jpg', price: 264000 },
            { name: 'Best ever', img: 'assets/images/p102.jpg', price: 264000 }
        ]
    };

    function openCartModal(productId, name) {
        const variants = productVariants[productId];
        const defaultVariant = variants[0];

        document.getElementById('modalTitle').innerText = name;
        document.getElementById('modalProductName').value = name;
        document.getElementById('modalProductQty').value = 1;
        document.getElementById('qtyInput').value = 1;

        // Render ảnh và giá mặc định
        document.querySelector('.modal-img').src = defaultVariant.img;
        document.getElementById('modalProductPrice').value = defaultVariant.price;
        document.getElementById('modalPrice').innerText = defaultVariant.price + 'đ';
        document.getElementById('modalProductOption').value = defaultVariant.name;

        // Render danh sách phân loại
        const optionGroup = document.querySelector('.option-btn-group');
        optionGroup.innerHTML = ''; // Clear cũ

        variants.forEach((variant, index) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'option-btn' + (index === 0 ? ' active' : '');
            btn.innerText = variant.name;
            btn.setAttribute('data-img', variant.img);
            btn.setAttribute('data-price', variant.price);
            btn.onclick = function () {
                selectOption(this);
            };
            optionGroup.appendChild(btn);
        });

        document.getElementById('cartModal').style.display = 'block';
    }

    // Chọn phân loại
    function selectOption(btn) {
        document.querySelectorAll('.option-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        document.querySelector('.modal-img').src = btn.getAttribute('data-img');
        const price = btn.getAttribute('data-price');
        document.getElementById('modalPrice').innerText = price + 'đ';
        document.getElementById('modalProductPrice').value = price;
        document.getElementById('modalProductOption').value = btn.innerText;
    }

    // Đóng modal
    function closeCartModal() {
        document.getElementById('cartModal').style.display = 'none';
    }

    // Tăng / giảm số lượng
    function increaseQty() {
        let qty = parseInt(document.getElementById('qtyInput').value);
        qty++;
        document.getElementById('qtyInput').value = qty;
        document.getElementById('modalProductQty').value = qty;
    }

    function decreaseQty() {
        let qty = parseInt(document.getElementById('qtyInput').value);
        if (qty > 1) {
            qty--;
            document.getElementById('qtyInput').value = qty;
            document.getElementById('modalProductQty').value = qty;
        }
    }

    // Đóng khi click ngoài
    window.onclick = function (event) {
        const modal = document.getElementById('cartModal');
        if (event.target === modal) modal.style.display = "none";
    };

    // Gán sự kiện nút
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', function () {
            const productCard = btn.closest('.product-card');
            const id = productCard.dataset.id;
            const name = productCard.querySelector('.product-title').innerText;
            openCartModal(id, name);
        });
    });

        </script>
</body>

</html>
