<?php
// Kết nối CSDL
$conn = mysqli_connect("localhost", "root", "", "db_mypham");
mysqli_set_charset($conn, "utf8");

// Lấy ID từ URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sql = "SELECT * FROM product WHERE id = $id";
$result = mysqli_query($conn, $sql);
$product = mysqli_fetch_assoc($result);

if (!$product) {
    echo "Không tìm thấy sản phẩm!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title><?= $product['name'] ?></title>
    <link rel="stylesheet" href="style.css"> <!-- CSS ngoài -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: white;
            color: #333;
        }

        .container {
            max-width: 1100px;
            margin: 30px auto;
            display: flex;
            gap: 40px;
        }

        h1,
        p,
        .price {
            color: rgb(37, 37, 37);
        }

        .image-section {
            flex: 1;
        }

        .main-image img {
            width: 80%;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .thumbnail-list {
            margin-top: 10px;
            display: flex;
            gap: 10px;
        }

        .thumbnail-list img {
            width: 70px;
            height: 70px;
            cursor: pointer;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .info-section {
            flex: 1;
        }

        .info-section h1 {
            font-size: 22px;
            margin-bottom: 10px;
        }

        .price {
            font-size: 24px;
            color: #e74c3c;
            font-weight: bold;
        }

        .stock {
            margin: 10px 0;
        }

        .actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 16px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
            font-weight: bold;
        }

        .btn.cart {
            background-color: rgb(234, 96, 11);
        }

        .btn.buy {
            background-color: rgb(18, 179, 29);
        }

        .quantity-box {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .qty-btn {
            background-color: #cccccc63;
            color: black;
            border: none;
            width: 35px;
            height: 35px;
            font-size: 18px;
            cursor: pointer;
            border-radius: 5px;
        }

        #product_qty {
            width: 50px;
            text-align: center;
            margin: 0 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            height: 35px;
            background: white;
        }

        .payment-methods {
            margin-top: 30px;
        }

        .payment-methods img {
            height: 30px;
            margin-right: 10px;
            background: white;
            border-radius: 6px;
            padding: 4px;
            box-shadow: 0 0 4px rgba(0, 0, 0, 0.1);
        }

        .cart-modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
        }

        .cart-modal-content {
            background: #fff;
            width: 500px;
            /* tăng kích thước phù hợp màn hình */
            max-width: 90%;
            max-height: 90vh;
            padding: 25px;
            border-radius: 10px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation: slideFadeIn 0.4s ease;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            /* thêm bóng đổ nhẹ */
        }


        .modal-body {
            overflow-y: auto;
            flex: 1;
            margin-bottom: 15px;
        }

        .product-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .product-info h3 {
            margin: 0 0 5px 0;
            font-size: 14px;
        }

        .modal-price,
        .modal-stock {
            margin: 2px 0;
        }


        .modal-img {
            width: 100px;
            height: auto;
            flex-shrink: 0;
        }

        .close-btn {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 22px;
            cursor: pointer;
        }

        .option-btn {
            margin: 5px;
            padding: 5px 10px;
            border: 1px solid #ccc;
            cursor: pointer;
            background-color: white;
            border-radius: 4px;
        }

        .option-btn.active {
            border-color: #e91e63;
            background-color: #ffe6ee;
        }

        .modal-quantity {
            display: flex;
            align-items: center;
            gap: 5px;
            margin: 10px 0;
        }

        .modal-quantity input {
            width: 40px;
            text-align: center;
        }

        .add-to-cart-btn {
            width: 100%;
            padding: 10px;
            background-color: #e94c64;
            color: white;
            border: none;
            border-radius: 4px;
        }

        .add-to-cart-btn:hover {
            cursor: pointer;
        }

        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>
    <script>
        function changeImage(src) {
            document.getElementById('mainProductImage').src = src;
        }
    </script>
</head>

<body>

    <div class="container">
        <!-- Hình ảnh -->
        <div class="image-section">
            <div class="main-image">
                <img id="mainProductImage" src="<?= $product['product_image'] ?>" alt="<?= $product['name'] ?>">
            </div>
            <div class="thumbnail-list">
                <!-- Giả sử hiển thị lại 4 ảnh giống nhau để demo -->
                <img src="https://down-vn.img.susercontent.com/file/57afecfdbbf201398b361163497921fb" onclick="changeImage(this)" alt="ảnh 1">
                <img src="https://product.hstatic.net/200000117693/product/atezj6_simg_de2fe0_500x500_maxb_2c796d74539747cd8e2ddd390ecad1c3_master.jpg" onclick="changeImage(this)" alt="ảnh 2">
                <img src="https://example.com/image3.jpg" onclick="changeImage(this)" alt="ảnh 3">
                <img src="https://example.com/image4.jpg" onclick="changeImage(this)" alt="ảnh 4">
            </div>
        </div>

        <!-- Thông tin sản phẩm -->
        <div class="info-section">
            <h1><?= $product['name'] ?></h1>
            <p><strong>Danh mục:</strong> <?= $product['category'] ?></p>
            <p class="price"><?= number_format($product['price'], 0, ',', '.') ?>₫</p>
            <p class="stock">Còn hàng: <?= $product['stock'] ?> sản phẩm</p>

            <form method="POST" action="">
                <input type="hidden" name="product_name" value="<?= $product['name'] ?>">
                <input type="hidden" name="product_price" value="<?= $product['price'] ?>">
                <input type="hidden" name="product_img" value="<?= $product['product_image'] ?>">

                <label for="product_qty">Số lượng:</label>
                <div class="quantity-box">
                    <button type="button" class="qty-btn" onclick="changeQty(-1)">-</button>
                    <input type="text" name="product_qty" id="product_qty" value="1" readonly>
                    <button type="button" class="qty-btn" onclick="changeQty(1)">+</button>
                </div>

                <div class="actions">
                    <button type="button" class="btn cart" onclick="openCartModal('Son Kem 3CE Velvet Lip Tint Taupe Speak Up Daffodil Bitter Hour Child Like 4g - Mibebe', '185000', 'https://down-vn.img.susercontent.com/file/vn-11134207-7r98o-lstqguefuimcd2')">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                    </button>

                </div>
            </form>
            <form method="POST" action="checkout.php">
                <input type="hidden" name="product_name" value="<?= $product['name'] ?>">
                <input type="hidden" name="product_price" value="<?= $product['price'] ?>">
                <input type="hidden" name="product_option" value="130 trắng">
                <input type="hidden" name="product_img" value="<?= $product['product_image'] ?>">
                <!-- Dùng JavaScript để cập nhật số lượng từ input hiện có -->
                <input type="hidden" name="product_qty" id="buy_now_qty" value="1">
                <button type="submit" class="btn buy"><i class="fas fa-credit-card"></i> Mua ngay</button>
            </form>


            <!-- Phương thức thanh toán -->
            <div class="payment-methods">
                <p><strong>Phương thức thanh toán:</strong></p>
                <img src="img/visa.png" alt="Visa">
                <img src="img/momo.png" alt="MoMo">
                <img src="img/vnpay.png" alt="VNPay">
                <img src="img/mastercard.png" alt="Mastercard">
            </div>
        </div>
    </div>

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

</body>
<script>
    function changeQty(delta) {
        let qtyInput = document.getElementById('product_qty');
        let current = parseInt(qtyInput.value);
        if (isNaN(current)) current = 1;
        let newQty = current + delta;
        if (newQty < 1) newQty = 1;
        qtyInput.value = newQty;
    }

    function changeImage(imgElement) {
        document.getElementById('mainProductImage').src = imgElement.src;
    }

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
        p1: [{
                name: '130g trắng',
                img: 'assets/images/hatomogi_trang.jpg',
                price: 40000
            },
            {
                name: '130g xanh',
                img: 'assets/images/hatomogi_xanh.jpg',
                price: 40000
            }
        ],
        p2: [{
                name: '88ml',
                img: 'assets/images/product2.jpg',
                price: 88000
            },
            {
                name: '236ml',
                img: 'assets/images/product2.jpg',
                price: 284000
            }
        ],
        p3: [{
                name: 'Vàng',
                img: 'assets/images/p31.jpg',
                price: 295000
            },
            {
                name: 'Đen',
                img: 'assets/images/p32.jpg',
                price: 295000
            },
            {
                name: 'Trắng',
                img: 'assets/images/p33.jpg',
                price: 295000
            }
        ],
        p4: [{
                name: 'A01#',
                img: 'assets/images/p41.jpg',
                price: 36300
            },
            {
                name: 'A02#',
                img: 'assets/images/p42.jpg',
                price: 39600
            }
        ],
        p5: [{
                name: '10ml',
                img: 'assets/images/p5.jpg',
                price: 311000
            },
            {
                name: '30ml',
                img: 'assets/images/p5.jpg',
                price: 651000
            }
        ],
        p6: [{
                name: 'JUICY 20 + Glas 16',
                img: 'assets/images/p61.jpg',
                price: 269000
            },
            {
                name: 'JUICY 23 + Glas 16',
                img: 'assets/images/p62.jpg',
                price: 269000
            }
        ],
        p7: [{
            name: '60ml',
            img: 'assets/images/p7.jpg',
            price: 65600
        }],
        p8: [{
                name: 'Sạch da giảm nhờn',
                img: 'assets/images/p81.jpg',
                price: 254000
            },
            {
                name: 'Dịu nhẹ da nhạy',
                img: 'assets/images/p82.jpg',
                price: 254000
            },
            {
                name: 'Dành cho da sạm',
                img: 'assets/images/p83.jpg',
                price: 254000
            }
        ],
        p9: [{
                name: 'NTT + SRM',
                img: 'assets/images/p9.jpg',
                price: 165000
            },
            {
                name: 'NTT + KD',
                img: 'assets/images/p9.jpg',
                price: 120000
            },
            {
                name: 'SRM + KD',
                img: 'assets/images/p9.jpg',
                price: 139000
            },
            {
                name: 'NTT + TONER + SRm + KD',
                img: 'assets/images/p9.jpg',
                price: 304000
            }
        ],
        p10: [{
                name: 'Taupe',
                img: 'assets/images/p101.jpg',
                price: 264000
            },
            {
                name: 'Best ever',
                img: 'assets/images/p102.jpg',
                price: 264000
            }
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
            btn.onclick = function() {
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
    window.onclick = function(event) {
        const modal = document.getElementById('cartModal');
        if (event.target === modal) modal.style.display = "none";
    };

    // Gán sự kiện nút
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', function() {
            const productCard = btn.closest('.product-card');
            const id = productCard.dataset.id;
            const name = productCard.querySelector('.product-title').innerText;
            openCartModal(id, name);
        });
    });
</script>


</html>