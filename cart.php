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
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="styles.css"> <!-- Style cho trang giỏ hàng -->
</head>
<style>

    .h2 {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .cart-container {
        width: 80%;
        margin: 0 auto;
        background: #fff;
        padding: 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .cart-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .cart-table th,
    .cart-table td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
        text-align: center;
    }

    .cart-footer {
        margin-top: 20px;
        text-align: right;
    }

    .checkout-btn {
        background-color: #ee4d2d;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        text-decoration: none;
    }

    .checkout-btn:hover {
        background-color: #d84324;
    }

    .toast-message {
        background-color: #4CAF50;
        color: white;
        padding: 15px;
        margin-bottom: 20px;
        text-align: center;
        border-radius: 5px;
    }

    .cart-table img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 5px;
    }

    .remove-btn {
    color: red;
    text-decoration: none;
    font-weight: bold;
    background-color: #ffcccc;
    padding: 10px;
    border-radius: 5px;
    cursor: pointer;
}

.remove-btn:hover {
    background-color: #ff6666;
}

</style>

<body>
<form method="POST" action="remove_from_cart.php">
    <div class="cart-container">
    <h2><i class="fas fa-shopping-cart"></i> Giỏ hàng của bạn</h2>

        <?php if ($toast_message): ?>
            <div class="toast-message"><?= $toast_message; ?></div>
        <?php endif; ?>

        <table class="cart-table">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all" onclick="toggleSelectAll()"> Chọn tất cả</th> <!-- Checkbox Select All -->
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
                            <td><input type="checkbox" name="selected_items[]" value="<?= $item['id']; ?>"></td> <!-- Checkbox cho mỗi sản phẩm -->
                            <td><img src="<?= htmlspecialchars($item['product_image']); ?>" alt="<?= htmlspecialchars($item['product_name']); ?>" width="50" height="50"></td>
                            <td><?= htmlspecialchars($item['product_name']); ?></td>
                            <td><?= htmlspecialchars($item['product_option']); ?></td>
                            <td><?= number_format($item['price'], 3, '.', '.'); ?></td>
                            <td><?= $item['quantity']; ?></td>
                            <td><?= number_format($subtotal, 3, '.', '.'); ?></td>
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
                <p><strong>Tổng cộng: <?= number_format($total_price, 3, '.', '.') ; ?></strong></p>
                <button type="submit" class="checkout-btn">Xóa đã chọn</button>
                <a href="checkout.php" class="checkout-btn">Thanh toán</a>
            </div>
        <?php endif; ?>
    </div>
</form>

<script>
    // Toggle select/unselect all checkboxes
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
