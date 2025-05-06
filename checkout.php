<?php
session_start();
require_once "connect.php"; // file kết nối database

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$order = $_SESSION['order_temp'] ?? [];

$product_name = $order['name'] ?? '';
$product_price = $order['price'] ?? 0;
$product_option = $order['option'] ?? '';
$product_qty = $order['qty'] ?? 1;
$product_img = $order['img'] ?? '';


// Kiểm tra nếu có sản phẩm trong giỏ hàng
// if (isset($_SESSION['order_temp'])) {
//     $order = $_SESSION['order_temp'];
//     $product_name = $order['name'];
//     $product_price = $order['price'];
//     $product_option = $order['option'];
//     $product_qty = $order['qty'];
//     $product_img = $order['img'];
// } else {
//     echo "Không có sản phẩm nào trong giỏ hàng!";
//     exit();
// }

// Kiểm tra địa chỉ người dùng
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM dia_chi WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$user_id]);
$address = $stmt->fetch();

// Lưu sản phẩm tạm thời vào session để truyền sang trang kế tiếp
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['product_name'])) {
    $name = $_POST['product_name'];
    $price = $_POST['product_price'];
    $option = $_POST['product_option'];
    $qty = $_POST['product_qty'];
    $img = $_POST['product_img'];

    // Lưu sản phẩm vào session
    $_SESSION['order_temp'] = [
        'name' => $name,
        'price' => $price,
        'option' => $option,
        'qty' => $qty,
        'img' => $img
    ];

    if ($address) {
        header("Location: confirm_order.php");
    } else {
        header("Location: add_address.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Thanh Toán</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f7f7f7;
            padding: 30px;
        }

        .checkout-container {
            background: #fff;
            max-width: 700px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 30px;
        }

        .product-header {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .product-header img {
            width: 140px;
            height: 140px;
            object-fit: cover;
            border-radius: 10px;
        }

        .product-details h2 {
            margin: 0 0 10px;
            font-size: 22px;
        }

        .product-details p {
            margin: 5px 0;
            font-size: 16px;
            color: #444;
        }

        .option-group {
            display: flex;
            gap: 10px;
            margin: 15px 0;
        }

        .option-group button {
            padding: 10px 15px;
            border: 1px solid #ccc;
            background: #fff;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }

        .option-group button:hover,
        .option-group button.active{
            background-color:rgb(158, 177, 180);
            color: #fff;
            border-color: #ccc;
        }

        .qty-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 10px;
        }

        .qty-controls button {
            width: 35px;
            height: 35px;
            border: 1px solid #ccc;
            background: #fff;
            font-size: 18px;
            border-radius: 6px;
            cursor: pointer;
        }

        .qty-controls input {
            width: 50px;
            text-align: center;
            font-size: 16px;
            border: 1px solid #ccc;
            height: 35px;
            border-radius: 6px;
        }

        .total {
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
            color: #d0021b;
        }

        .checkout-btn {
            margin-top: 30px;
            width: 100%;
            padding: 15px;
            font-size: 16px;
            background-color: #f53d2d;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: 0.3s;
        }
    </style>
</head>
<body>
    <div class="checkout-container">
        <form method="POST" action="">
            <div class="product-header">
                <img src="<?= $product_img ?>" alt="Sản phẩm">
                <div class="product-details">
                    <h2><?= $product_name ?></h2>
                    <p>Giá: <span id="price"><?= number_format($product_price) ?></span>đ</p>
                    <p><strong>Phân loại:</strong></p>
                    <div class="option-group" id="optionGroup">
                        <button type="button" onclick="selectOption(this)">130 trắng</button>
                        <button type="button" onclick="selectOption(this)">170 trắng</button>
                        <button type="button" onclick="selectOption(this)">130 xanh da mụn</button>
                    </div>

                    <p><strong>Số lượng:</strong></p>
                    <div class="qty-controls">
                        <button type="button" onclick="changeQty(-1)">−</button>
                        <input type="text" id="qty" value="<?= $product_qty ?>" readonly>
                        <button type="button" onclick="changeQty(1)">+</button>
                    </div>

                    <p class="total">Tổng cộng: <span id="total"><?= number_format($product_price * $product_qty) ?></span>đ</p>
                </div>
            </div>

            <!-- Hidden inputs để gửi -->
            <input type="hidden" name="product_name" value="<?= $product_name ?>">
            <input type="hidden" name="product_price" value="<?= $product_price ?>">
            <input type="hidden" name="product_img" value="<?= $product_img ?>">
            <input type="hidden" name="product_option" id="selectedOption" value="<?= $product_option ?>">
            <input type="hidden" name="product_qty" id="selectedQty" value="<?= $product_qty ?>">

            <button type="submit" class="checkout-btn">Xác nhận mua</button>
        </form>
    </div>

    <script>
        let qtyInput = document.getElementById("qty");
        let selectedQty = document.getElementById("selectedQty");
        let selectedOption = document.getElementById("selectedOption");
        let price = <?= $product_price ?>;

        function changeQty(delta) {
            let qty = parseInt(qtyInput.value);
            qty += delta;
            if (qty < 1) qty = 1;
            qtyInput.value = qty;
            selectedQty.value = qty;
            updateTotal();
        }

        function updateTotal() {
            let total = parseInt(qtyInput.value) * price;
            document.getElementById("total").innerText = total.toLocaleString();
        }

        function selectOption(btn) {
            document.querySelectorAll('#optionGroup button').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            selectedOption.value = btn.innerText;
        }

        // Gắn class "active" cho lựa chọn đầu tiên khi load
        window.onload = () => {
            const firstOption = document.querySelector('#optionGroup button');
            if (firstOption) {
                firstOption.classList.add('active');
                selectedOption.value = firstOption.innerText;
            }
        }
    </script>
</body>
</html>
