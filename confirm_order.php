<?php
session_start();
require_once "connect.php"; // file kết nối database

// Nếu không có sản phẩm thì thông báo nhẹ nhàng
if (!isset($_SESSION['order_temp'])) {
    echo "<h3 style='color:red;text-align:center;'>Không có thông tin sản phẩm để đặt hàng. Vui lòng chọn lại!</h3>";
    exit();
}

// Lấy thông tin sản phẩm từ session
$order = $_SESSION['order_temp'];
$product_name = $order['name'];
$product_price = $order['price'];
$product_option = $order['option'];
$product_qty = $order['qty'];
$product_img = $order['img'];
$total = $product_price * $product_qty;

// Lấy thông tin địa chỉ người dùng
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM dia_chi WHERE user_id = ? AND mac_dinh = 1 LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$address = $result->fetch_assoc();

if (!$address) {
    echo "<h3 style='color:red;text-align:center;'>Bạn chưa thêm địa chỉ giao hàng!</h3>";
    exit();
}

$address_value = $address['dia_chi_day_du'] ?? "Không có địa chỉ.";

// Xác nhận đơn hàng
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Gán trạng thái và thời gian
    $status = "Chờ xử lý";
    $created_at = date("Y-m-d H:i:s");
    $updated_at = $created_at;

    $sql = "INSERT INTO orders (user_id, product_name, product_option, quantity, price, total, address, status, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issiidssss", $user_id, $product_name, $product_option, $product_qty, $product_price, $total, $address_value, $status, $created_at, $updated_at);

    if ($stmt->execute()) {
        echo "<script>sessionStorage.setItem('orderSuccess', '1'); window.location = 'confirm_order.php';</script>";
        exit();
    
    } else {
        echo "<h3 style='color:red;text-align:center;'>Đặt hàng thất bại. Vui lòng thử lại!</h3>";
    }
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Xác Nhận Đơn Hàng</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f7f7f7;
            padding: 30px;
        }

        .confirm-container {
            background: #fff;
            max-width: 700px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
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

        .address-details {
            margin: 20px 0;
            background-color: #f1f1f1;
            padding: 15px;
            border-radius: 6px;
        }

        .total {
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
            color: #d0021b;
        }

        .confirm-btn {
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

        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            max-width: 400px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.3);
        }

        .modal-content h2 {
            margin-bottom: 15px;
        }

        .modal-content button {
            padding: 10px 20px;
            background-color: #f53d2d;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="confirm-container">
        <h2>Xác Nhận Đơn Hàng</h2>

        <div class="product-header">
            <img src="<?= $product_img ?>" alt="Sản phẩm">
            <div class="product-details">
                <h2><?= $product_name ?></h2>
                <p>Giá: <span id="price"><?= number_format($product_price) ?></span>đ</p>
                <p><strong>Phân loại:</strong> <?= $product_option ?></p>
                <p><strong>Số lượng:</strong> <?= $product_qty ?></p>
                <p class="total">Tổng cộng: <span id="total"><?= number_format($product_price * $product_qty) ?></span>đ</p>
            </div>
        </div>

        <div class="address-details">
            <h3>Địa chỉ giao hàng:</h3>
            <p><?= $address_value ?></p>
        </div>

        <form method="POST" action="">
            <button type="submit" class="confirm-btn">Xác Nhận Đơn Hàng</button>
        </form>
    </div>
    <div id="successModal" class="modal">
        <div class="modal-content">
            <h2>Đặt hàng thành công!</h2>
            <p>Cảm ơn bạn đã mua hàng tại cửa hàng chúng tôi.</p>
            <button onclick="redirectHome()">Về trang chủ</button>
        </div>
    </div>
    <script>
           if (sessionStorage.getItem('orderSuccess') === '1') {
        showSuccessModal();
        sessionStorage.removeItem('orderSuccess'); // Xoá để không hiển thị lại khi reload
    }

    function showSuccessModal() {
        document.getElementById("successModal").style.display = "flex";
    }

    function redirectHome() {
        window.location.href = "home.php";
    }
    </script>

</body>

</html>