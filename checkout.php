<?php
session_start();
require_once "connect.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Kiểm tra thông tin sản phẩm trong session
$order = $_SESSION['order_temp'] ?? [];
$product_name = $order['name'] ?? '';
$product_price = $order['price'] ?? 0;
$product_option = $order['option'] ?? '';
$product_qty = $order['qty'] ?? 1;
$product_img = $order['img'] ?? '';
$total = $product_price * $product_qty;

// Lấy danh sách voucher đã thu thập
$user_id = $_SESSION['user_id'];
$sql = "SELECT v.id, v.code, v.discount, v.discount_type, v.min_order_value
        FROM user_vouchers uv
        JOIN vouchers v ON uv.voucher_id = v.id
        WHERE uv.user_id = ? AND v.is_active = 1 AND v.expires_at > NOW()";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$vouchers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Kiểm tra địa chỉ người dùng
$sql = "SELECT * FROM dia_chi WHERE user_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$address = $stmt->fetch();

// Xử lý khi gửi form xác nhận
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['product_name'])) {
    $name = $_POST['product_name'];
    $price = $_POST['product_price'];
    $option = trim($_POST['product_option']);
    $qty = $_POST['product_qty'];
    $img = $_POST['product_img'];
    $voucher_id = $_POST['voucher_id'] ?? 0;

    if ($option === '') {
        header("Location: checkout.php?error=missing_option");
        exit();
    }

    $_SESSION['order_temp'] = [
        'name' => $name,
        'price' => $price,
        'option' => $option,
        'qty' => $qty,
        'img' => $img,
        'voucher_id' => $voucher_id
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Thanh Toán - Luna Beauty</title>
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
        min-height: 100vh;
        padding: 40px 20px;
    }
    .checkout-container {
        background: #fff;
        max-width: 800px;
        margin: 0 auto;
        padding: 40px;
        border-radius: 24px;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .checkout-container:hover {
        transform: translateY(-8px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);
    }
    .product-header {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 32px;
        margin-bottom: 32px;
        border-bottom: 2px solid #f0f0f0;
        padding-bottom: 24px;
    }
    .product-header img {
        width: 100%;
        height: 200px;
        object-fit: cover;
        border-radius: 16px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }
    .product-details h2 {
        font-size: 28px;
        color: #333;
        font-weight: 600;
        margin-bottom: 12px;
    }
    .product-details p {
        font-size: 16px;
        color: #555;
        margin: 8px 0;
    }
    .option-group {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin: 16px 0;
    }
    .option-group button {
        padding: 12px 20px;
        border: 2px solid #e0e0e0;
        background: #fff;
        border-radius: 12px;
        font-size: 15px;
        color: #333;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .option-group button:hover,
    .option-group button.active {
        background: #ff6b81;
        color: #fff;
        border-color: #ff6b81;
        box-shadow: 0 4px 12px rgba(255, 107, 129, 0.3);
    }
    .qty-controls {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 16px 0;
    }
    .qty-controls button {
        width: 40px;
        height: 40px;
        border: 2px solid #e0e0e0;
        background: #fff;
        font-size: 20px;
        border-radius: 50%;
        color: #ff6b81;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .qty-controls button:hover {
        background: #ff6b81;
        color: #fff;
        border-color: #ff6b81;
    }
    .qty-controls input {
        width: 60px;
        text-align: center;
        font-size: 16px;
        border: 2px solid #e0e0e0;
        height: 40px;
        border-radius: 12px;
        background: #fff;
        color: #333;
    }
    .voucher-group {
        margin: 16px 0;
    }
    .voucher-group label {
        font-size: 16px;
        color: #333;
        font-weight: 500;
        margin-bottom: 8px;
        display: block;
    }
    .voucher-group select {
        width: 100%;
        padding: 12px;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        font-size: 15px;
        color: #333;
        background: #fff;
        cursor: pointer;
        transition: border-color 0.3s ease;
    }
    .voucher-group select:focus {
        border-color: #ff6b81;
        outline: none;
    }
    .total {
        font-size: 20px;
        font-weight: 700;
        color: #ff0000;
        margin-top: 24px;
    }
    .checkout-btn {
        width: 100%;
        padding: 16px;
        background: linear-gradient(90deg, #ff6b81 0%, #ff8e53 100%);
        color: #fff;
        border: none;
        border-radius: 50px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        margin-top: 32px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .checkout-btn:hover {
        background: linear-gradient(90deg, #ff4d68 0%, #ff7036 100%);
        box-shadow: 0 8px 24px rgba(255, 107, 129, 0.5);
        transform: translateY(-3px);
    }
    @media (max-width: 1024px) {
        .checkout-container {
            max-width: 700px;
            padding: 32px;
        }
        .product-header {
            grid-template-columns: 180px 1fr;
            gap: 24px;
        }
        .product-header img {
            height: 180px;
        }
    }
    @media (max-width: 768px) {
        body {
            padding: 24px 16px;
        }
        .checkout-container {
            padding: 24px;
        }
        .product-header {
            grid-template-columns: 150px 1fr;
            gap: 20px;
        }
        .product-header img {
            height: 150px;
        }
        .product-details h2 {
            font-size: 24px;
        }
        .product-details p {
            font-size: 15px;
        }
        .option-group button {
            padding: 10px 16px;
            font-size: 14px;
        }
        .qty-controls button {
            width: 36px;
            height: 36px;
            font-size: 18px;
        }
        .qty-controls input {
            width: 50px;
            height: 36px;
            font-size: 15px;
        }
        .total {
            font-size: 18px;
        }
        .checkout-btn {
            padding: 14px;
            font-size: 15px;
        }
    }
    @media (max-width: 600px) {
        body {
            padding: 16px;
        }
        .checkout-container {
            padding: 16px;
        }
        .product-header {
            grid-template-columns: 1fr;
            gap: 16px;
            text-align: center;
        }
        .product-header img {
            max-width: 200px;
            height: 140px;
            margin: 0 auto;
        }
        .product-details h2 {
            font-size: 20px;
        }
        .product-details p {
            font-size: 14px;
        }
        .option-group {
            justify-content: center;
        }
        .option-group button {
            padding: 8px 12px;
            font-size: 13px;
        }
        .qty-controls button {
            width: 32px;
            height: 32px;
            font-size: 16px;
        }
        .qty-controls input {
            width: 45px;
            height: 32px;
            font-size: 14px;
        }
        .total {
            font-size: 16px;
        }
        .checkout-btn {
            padding: 12px;
            font-size: 14px;
        }
    }
</style>
<body>
    <div class="checkout-container">
        <form method="POST">
            <div class="product-header">
                <img src="<?= htmlspecialchars($product_img) ?>" alt="Sản phẩm">
                <div class="product-details">
                    <h2><?= htmlspecialchars($product_name) ?></h2>
                    <p>Giá: <span id="price"><?= number_format($product_price) ?></span>đ</p>
                    <p><strong>Phân loại:</strong></p>
                    <div class="option-group" id="optionGroup">
                        <button type="button" onclick="selectOption(this)" <?= $product_option === '130 trắng' ? 'class="active"' : '' ?>>130 trắng</button>
                        <button type="button" onclick="selectOption(this)" <?= $product_option === '170 trắng' ? 'class="active"' : '' ?>>170 trắng</button>
                        <button type="button" onclick="selectOption(this)" <?= $product_option === '130 xanh da mụn' ? 'class="active"' : '' ?>>130 xanh da mụn</button>
                    </div>
                    <p><strong>Số lượng:</strong></p>
                    <div class="qty-controls">
                        <button type="button" onclick="changeQty(-1)">−</button>
                        <input type="text" id="qty" value="<?= $product_qty ?>" readonly>
                        <button type="button" onclick="changeQty(1)">+</button>
                    </div>
                    <p><strong>Chọn Voucher:</strong></p>
                    <div class="voucher-group">
                        <label for="voucher_id">Áp dụng voucher giảm giá</label>
                        <select id="voucher_id" name="voucher_id" onchange="updateTotal()">
                            <option value="0">Không sử dụng voucher</option>
                            <?php foreach ($vouchers as $voucher): ?>
                                <option value="<?= $voucher['id'] ?>" 
                                        data-discount="<?= $voucher['discount'] ?>" 
                                        data-type="<?= $voucher['discount_type'] ?>" 
                                        data-min="<?= $voucher['min_order_value'] ?>">
                                    <?= htmlspecialchars($voucher['code']) ?> - Giảm 
                                    <?= $voucher['discount_type'] === 'percentage' ? ($voucher['discount'] * 100) . '%' : number_format($voucher['discount']) . 'đ' ?>
                                    (Đơn tối thiểu: <?= number_format($voucher['min_order_value']) ?>đ)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p class="total">Tổng cộng: <span id="total"><?= number_format($total) ?></span>đ</p>
                </div>
            </div>
            <input type="hidden" name="product_name" value="<?= htmlspecialchars($product_name) ?>">
            <input type="hidden" name="product_price" value="<?= $product_price ?>">
            <input type="hidden" name="product_img" value="<?= htmlspecialchars($product_img) ?>">
            <input type="hidden" name="product_option" id="selectedOption" value="<?= htmlspecialchars($product_option) ?>">
            <input type="hidden" name="product_qty" id="selectedQty" value="<?= $product_qty ?>">
            <button type="submit" class="checkout-btn"><i class="fas fa-check-circle"></i> Xác nhận mua</button>
        </form>
    </div>
    <script>
        let qtyInput = document.getElementById("qty");
        let selectedQty = document.getElementById("selectedQty");
        let selectedOption = document.getElementById("selectedOption");
        let price = <?= $product_price ?>;
        let voucherSelect = document.getElementById("voucher_id");

        function changeQty(delta) {
            let qty = parseInt(qtyInput.value);
            qty += delta;
            if (qty < 1) qty = 1;
            qtyInput.value = qty;
            selectedQty.value = qty;
            updateTotal();
        }

        function selectOption(btn) {
            document.querySelectorAll('#optionGroup button').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            selectedOption.value = btn.innerText;
        }

        function updateTotal() {
            let qty = parseInt(qtyInput.value);
            let subtotal = qty * price;
            let voucherId = voucherSelect.value;
            let discount = 0;

            if (voucherId !== "0") {
                let selectedOption = voucherSelect.querySelector(`option[value="${voucherId}"]`);
                let discountValue = parseFloat(selectedOption.getAttribute("data-discount"));
                let discountType = selectedOption.getAttribute("data-type");
                let minOrder = parseFloat(selectedOption.getAttribute("data-min"));

                if (subtotal >= minOrder) {
                    if (discountType === "percentage") {
                        discount = subtotal * discountValue;
                    } else {
                        discount = discountValue;
                    }
                }
            }

            let total = subtotal - discount;
            document.getElementById("total").innerText = total.toLocaleString();
        }

        window.onload = () => {
            let currentOption = "<?= htmlspecialchars($product_option) ?>";
            if (currentOption) {
                document.querySelectorAll('#optionGroup button').forEach(btn => {
                    if (btn.innerText === currentOption) {
                        btn.classList.add('active');
                    }
                });
            } else {
                const firstOption = document.querySelector('#optionGroup button');
                if (firstOption) {
                    firstOption.classList.add('active');
                    selectedOption.value = firstOption.innerText;
                }
            }
            updateTotal();
        };
    </script>
</body>
</html>
