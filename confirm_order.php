<?php
session_start();
require_once "connect.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['address_id'])) {
    $_SESSION['selected_address_id'] = $_POST['address_id'];
    header("Location: confirm_order.php");
    exit();
}

if (!isset($_SESSION['order_temp'])) {
    echo "<h3 style='color:red;text-align:center;'>Không có thông tin sản phẩm để đặt hàng. Vui lòng chọn lại!</h3>";
    exit();
}

// Lấy thông tin sản phẩm và voucher từ session
$order = $_SESSION['order_temp'];
$product_name = $order['name'];
$product_price = $order['price'];
$product_option = $order['option'];
$product_qty = $order['qty'];
$product_img = $order['img'];
$voucher_id = $order['voucher_id'] ?? 0;

// Tính tổng tiền
$subtotal = $product_price * $product_qty;
$discount = 0;
$voucher_code = '';

if ($voucher_id) {
    $sql = "SELECT code, discount, discount_type, min_order_value FROM vouchers WHERE id = ? AND is_active = 1 AND expires_at > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $voucher_id);
    $stmt->execute();
    $voucher = $stmt->get_result()->fetch_assoc();
    
    if ($voucher && $subtotal >= $voucher['min_order_value']) {
        $voucher_code = $voucher['code'];
        if ($voucher['discount_type'] === 'percentage') {
            $discount = $subtotal * $voucher['discount'];
        } else {
            $discount = $voucher['discount'];
        }
    }
}

$total = $subtotal - $discount;

// Lấy thông tin địa chỉ
$user_id = $_SESSION['user_id'];
$selected_address_id = $_SESSION['selected_address_id'] ?? null;

if ($selected_address_id) {
    $sql = "SELECT * FROM dia_chi WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $selected_address_id, $user_id);
} else {
    $sql = "SELECT * FROM dia_chi WHERE user_id = ? AND mac_dinh = 1 LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
}

$stmt->execute();
$result = $stmt->get_result();
$address = $result->fetch_assoc();

if (!$address) {
    echo "<h3 style='color:red;text-align:center;'>Bạn chưa thêm địa chỉ giao hàng!</h3>";
    exit();
}

$address_value = $address['dia_chi_day_du'] ?? "Không có địa chỉ.";
$address_text = strtolower($address_value);
if (strpos($address_text, 'hà nội') !== false || strpos($address_text, 'hồ chí minh') !== false) {
    $shipping_fee = 20000;
} else {
    $shipping_fee = 30000;
}
$grand_total = $total + $shipping_fee;

// Xác nhận đơn hàng
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['confirm_order'])) {
    $status = "Chờ xử lý";
    $created_at = date("Y-m-d H:i:s");
    $updated_at = $created_at;

    $sql = "INSERT INTO orders (user_id, product_name, product_option, quantity, price, total, address, status, created_at, updated_at, voucher_id, discount, shipping_fee) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issidsssssidi", $user_id, $product_name, $product_option, $product_qty, $product_price, $grand_total, $address_value, $status, $created_at, $updated_at, $voucher_id, $discount, $shipping_fee);

    if ($stmt->execute()) {
        unset($_SESSION['order_temp']);
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Nhận Đơn Hàng</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    body {
        font-family: 'Inter', sans-serif;
        background: linear-gradient(135deg, #fff5f7 0%, #f8e9ec 100%);
        color: #333;
        line-height: 1.6;
        min-height: 100vh;
        padding: 40px 20px;
    }
    .confirm-container {
        background: #fff;
        max-width: 900px;
        margin: 0 auto;
        border-radius: 24px;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
        padding: 40px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .confirm-container:hover {
        transform: translateY(-8px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);
    }
    h2 {
        text-align: center;
        color: #ff6b81;
        font-size: 32px;
        font-weight: 600;
        margin-bottom: 32px;
    }
    .product-header {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 32px;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 24px;
        margin-bottom: 32px;
    }
    .product-header img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }
    .product-header img:hover {
        transform: scale(1.05);
    }
    .product-details h1 {
        font-size: 20px;
        color: #333;
        font-weight: 600;
        margin-bottom: 12px;
    }
    .product-details p {
        font-size: 16px;
        color: #555;
        margin: 8px 0;
    }
    .total {
        font-size: 20px;
        font-weight: 700;
        color: #ff0000;
        margin-top: 16px;
    }
    .address-details {
        background: #fff7f7;
        padding: 24px;
        border-radius: 16px;
        margin-bottom: 32px;
        border: 1px solid #ffe4e1;
    }
    .address-details h3 {
        font-size: 20px;
        color: #ff6b81;
        font-weight: 500;
        margin-bottom: 16px;
    }
    .address-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
    }
    .address-info p {
        font-size: 16px;
        color: #444;
        margin: 6px 0;
    }
    .change-address-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 16px;
        background: #fff;
        border: 2px solid #ff6b81;
        color: #ff6b81;
        font-size: 15px;
        font-weight: 500;
        border-radius: 12px;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .change-address-btn:hover {
        background: #ff6b81;
        color: #fff;
        box-shadow: 0 6px 16px rgba(255, 107, 129, 0.4);
    }
    .confirm-btn {
        width: 100%;
        padding: 16px;
        font-size: 16px;
        font-weight: 500;
        background: linear-gradient(90deg, #ff6b81 0%, #ff8e53 100%);
        color: #fff;
        border: none;
        border-radius: 50px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .confirm-btn:hover {
        background: linear-gradient(90deg, #ff4d68 0%, #ff7036 100%);
        box-shadow: 0 8px 24px rgba(255, 107, 129, 0.5);
        transform: translateY(-3px);
    }
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        backdrop-filter: blur(5px);
        justify-content: center;
        align-items: center;
    }
    .modal-content {
        background: #fff;
        max-width: 500px;
        width: 90%;
        padding: 40px;
        border-radius: 24px;
        text-align: center;
        box-shadow: 0 16px 48px rgba(0, 0, 0, 0.2);
        animation: slideFadeIn 0.4s ease;
    }
    .modal-content h2 {
        color: #2ecc71;
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 16px;
    }
    .modal-content p {
        font-size: 16px;
        color: #555;
        margin-bottom: 24px;
    }
    .modal-content button {
        width: 100%;
        padding: 14px;
        background: linear-gradient(90deg, #ff6b81 0%, #ff8e53 100%);
        color: #fff;
        border: none;
        border-radius: 50px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .modal-content button:hover {
        background: linear-gradient(90deg, #ff4d68 0%, #ff7036 100%);
        box-shadow: 0 8px 24px rgba(255, 107, 129, 0.5);
        transform: translateY(-3px);
    }
    @keyframes slideFadeIn {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @media (max-width: 768px) {
        .confirm-container {
            padding: 24px;
        }
        .product-header {
            grid-template-columns: 150px 1fr;
            gap: 24px;
        }
        .product-header img {
            height: 150px;
        }
        .product-details h1 {
            font-size: 24px;
        }
        .total {
            font-size: 18px;
        }
        .address-details {
            padding: 20px;
        }
    }
    @media (max-width: 600px) {
        body {
            padding: 20px 12px;
        }
        .product-header {
            grid-template-columns: 1fr;
            gap: 16px;
            text-align: center;
        }
        .product-header img {
            max-width: 200px;
            margin: 0 auto;
        }
        .product-details h1 {
            font-size: 20px;
        }
        .confirm-btn {
            padding: 14px;
            font-size: 15px;
        }
        .modal-content {
            padding: 24px;
        }
    }
</style>
<body>
    <div class="confirm-container">
        <h2>Xác Nhận Đơn Hàng</h2>
        <div class="product-header">
            <img src="<?= htmlspecialchars($product_img) ?>" alt="Sản phẩm">
            <div class="product-details">
                <h1><?= htmlspecialchars($product_name) ?></h1>
                <p>Giá: <span id="price"><?= number_format($product_price) ?></span>đ</p>
                <p><strong>Phân loại:</strong> <?= htmlspecialchars($product_option) ?></p>
                <p><strong>Số lượng:</strong> <?= $product_qty ?></p>
                <p>Thành tiền: <?= number_format($subtotal) ?>đ</p>
                <?php if ($discount > 0): ?>
                    <p>Voucher (<?= htmlspecialchars($voucher_code) ?>): -<?= number_format($discount) ?>đ</p>
                <?php endif; ?>
                <p>Phí vận chuyển: <?= number_format($shipping_fee) ?>đ</p>
                <p class="total">Tổng cộng: <span id="total"><?= number_format($grand_total) ?></span>đ</p>
            </div>
        </div>
        <div class="address-details">
            <h3>Địa chỉ giao hàng</h3>
            <div class="address-row">
                <div class="address-info">
                    <p><strong><?= htmlspecialchars($address['ho_ten']) ?></strong></p>
                    <p>SĐT: <?= htmlspecialchars($address['so_dien_thoai']) ?></p>
                    <p>Địa chỉ: <?= htmlspecialchars($address['dia_chi_day_du']) ?></p>
                </div>
                <a href="change_address.php" class="change-address-btn" title="Thay đổi địa chỉ">
                    <i class="fas fa-pen"></i> Thay đổi
                </a>
            </div>
        </div>
        <form method="POST" action="">
            <input type="hidden" name="confirm_order" value="1">
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
            sessionStorage.removeItem('orderSuccess');
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
