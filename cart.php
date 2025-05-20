<?php
session_start();
require_once 'connect.php';

// Kiểm tra xem có thông báo Toast hay không
$toast_message = $_SESSION['toast_message'] ?? '';
unset($_SESSION['toast_message']);  // Xóa thông báo sau khi hiển thị

// Lấy session_id hiện tại
$session_id = session_id();

// Lấy tất cả sản phẩm trong giỏ hàng của người dùng
$sql = "SELECT * FROM cart_items WHERE session_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $session_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = [];

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}

$total_price = 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng - Luna Beauty</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<style>
    /* Global Reset and Base Styles */
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

    /* Cart Container */
    .cart-container {
        background: #fff;
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px;
        border-radius: 24px;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .cart-container:hover {
        transform: translateY(-8px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12);
    }

    h2 {
        text-align: center;
        color: #ff6b81;
        font-size: 32px;
        font-weight: 600;
        margin-bottom: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }

    /* Toast Notification */
    .toast-message {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #2ecc71;
        color: #fff;
        padding: 16px 24px;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 500;
        z-index: 9999;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
        animation: slideFadeIn 0.5s ease;
    }

    /* Cart Table */
    .cart-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 32px;
    }

    .cart-table th,
    .cart-table td {
        padding: 16px;
        text-align: center;
        border-bottom: 2px solid #f0f0f0;
        font-size: 16px;
    }

    .cart-table th {
        background: #fff7f7;
        color: #ff6b81;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 14px;
    }

    .cart-table tr {
        transition: background 0.3s ease;
    }

    .cart-table tr:hover {
        background: #fff5f7;
    }

    .cart-table img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 4px;
    }

    .cart-table input[type="checkbox"] {
        width: 20px;
        height: 20px;
        accent-color: #ff6b81;
        cursor: pointer;
    }

    /* Buttons */
    .home-btn,
    .checkout-btn,
    .remove-btn {
        padding: 12px 24px;
        border: none;
        border-radius: 50px;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .home-btn {
        background: #fff;
        color: #ff6b81;
        border: 2px solid #ff6b81;
    }

    .home-btn:hover {
        background: #ff6b81;
        color: #fff;
        box-shadow: 0 6px 16px rgba(255, 107, 129, 0.4);
    }

    .checkout-btn {
        background: linear-gradient(90deg, #ff6b81 0%, #ff8e53 100%);
        color: #fff;
    }

    .checkout-btn:hover {
        background: linear-gradient(90deg, #ff4d68 0%, #ff7036 100%);
        box-shadow: 0 8px 24px rgba(255, 107, 129, 0.5);
        transform: translateY(-3px);
    }

    .remove-btn {
        background: #fff;
        color: #d32f2f;
        border: 2px solid #d32f2f;
    }

    .remove-btn:hover {
        background: #d32f2f;
        color: #fff;
        box-shadow: 0 6px 16px rgba(211, 47, 47, 0.4);
    }

    /* Cart Footer */
    .cart-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 32px;
        gap: 20px;
    }

    .cart-footer p {
        font-size: 20px;
        font-weight: 700;
        color: #ff0000; /* Consistent with confirm_order.php */
    }

    .cart-footer .buttons {
        display: flex;
        gap: 16px;
    }

    /* Animations */
    @keyframes slideFadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .cart-container {
            max-width: 100%;
            padding: 32px;
        }

        .cart-table th,
        .cart-table td {
            padding: 12px;
            font-size: 15px;
        }

        .cart-table img {
            width: 60px;
            height: 60px;
        }
    }

    @media (max-width: 768px) {
        .cart-container {
            padding: 24px;
        }

        h2 {
            font-size: 28px;
        }

        .cart-table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
        }

        .cart-table th,
        .cart-table td {
            font-size: 14px;
            padding: 10px;
        }

        .cart-footer {
            flex-direction: column;
            align-items: flex-end;
            gap: 16px;
        }

        .cart-footer .buttons {
            width: 100%;
            justify-content: flex-end;
        }
    }

    @media (max-width: 600px) {
        body {
            padding: 16px;
        }

        .cart-container {
            padding: 16px;
        }

        h2 {
            font-size: 24px;
        }

        .cart-table th,
        .cart-table td {
            font-size: 13px;
            padding: 8px;
        }

        .cart-table img {
            width: 50px;
            height: 50px;
        }

        .home-btn,
        .checkout-btn,
        .remove-btn {
            padding: 10px 16px;
            font-size: 14px;
        }

        .cart-footer p {
            font-size: 18px;
        }

        .toast-message {
            top: 16px;
            right: 16px;
            padding: 12px 20px;
            font-size: 14px;
        }
    }
</style>
<body>
    <form method="POST" action="remove_from_cart.php">
        <div class="cart-container">
            <h2><i class="fas fa-shopping-cart"></i> Giỏ hàng của bạn</h2>

            <?php if ($toast_message): ?>
                <div class="toast-message"><?= htmlspecialchars($toast_message); ?></div>
            <?php endif; ?>

            <table class="cart-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="select-all" onclick="toggleSelectAll()"> Chọn tất cả</th>
                        <th>Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Phân loại</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Tổng tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($cart_items) > 0): ?>
                        <?php foreach ($cart_items as $item):
                            $subtotal = $item['price'] * $item['quantity'];
                            $total_price += $subtotal;
                        ?>
                            <tr>
                                <td><input type="checkbox" name="selected_items[]" value="<?= $item['id']; ?>"></td>
                                <td><img src="<?= htmlspecialchars($item['product_image']); ?>" alt="<?= htmlspecialchars($item['product_name']); ?>"></td>
                                <td><?= htmlspecialchars($item['product_name']); ?></td>
                                <td><?= htmlspecialchars($item['product_option']); ?></td>
                                <td><?= number_format($item['price'], 0, '.', '.'); ?></td>
                                <td><?= $item['quantity']; ?></td>
                                <td><?= number_format($subtotal, 0, '.', '.'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">Giỏ hàng của bạn hiện tại trống!</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php if (count($cart_items) > 0): ?>
                <div class="cart-footer">
                    <p>Tổng cộng: <?= number_format($total_price, 0, '.', '.'); ?></p>
                    <div class="buttons">
                        <button type="button" class="home-btn" onclick="window.location.href='home.php';">
                            <i class="fas fa-home"></i> Quay về trang chủ
                        </button>
                        <button type="submit" class="remove-btn">
                            <i class="fas fa-trash"></i> Xóa đã chọn
                        </button>
                        <a href="checkout.php" class="checkout-btn">
                            <i class="fas fa-credit-card"></i> Thanh toán
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <button type="button" class="home-btn" onclick="window.location.href='home.php';">
                    <i class="fas fa-home"></i> Quay về trang chủ
                </button>
            <?php endif; ?>
        </div>
    </form>

    <script>
        function toggleSelectAll() {
            var selectAll = document.getElementById('select-all');
            var checkboxes = document.querySelectorAll('input[name="selected_items[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
        }
    </script>
</body>
</html>
<?php
$conn->close();
?>
