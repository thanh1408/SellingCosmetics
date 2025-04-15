<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Giỏ hàng Shopee</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .cart-container {
            max-width: 1000px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h2 {
            margin-bottom: 20px;
            color: #ee4d2d;
        }
        .cart-table {
            width: 100%;
            border-collapse: collapse;
        }
        .cart-table th, .cart-table td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }
        .product-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .product-info img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        .qty-controls {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .qty-controls button {
            width: 30px;
            height: 30px;
            border: 1px solid #ccc;
            background: #fff;
            cursor: pointer;
        }
        .qty-controls input {
            width: 40px;
            text-align: center;
            border: 1px solid #ccc;
            height: 30px;
        }
        .cart-footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .checkout-btn {
            background: #ee4d2d;
            color: #fff;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        .checkout-btn:hover {
            background: #d84324;
        }
        button[name="delete"] {
            background: none;
            border: none;
            color: red;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="checkbox"] {
            transform: scale(1.2);
        }
    </style>
</head>
<body>
    <div class="cart-container">
        <h2>🛍️ Giỏ Hàng Của Bạn</h2>
        <form method="post" action="update_cart.php">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>Sản phẩm</th>
                        <th>Phân loại</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Dữ liệu PHP sẽ đổ vào đây -->
                </tbody>
            </table>
            <div class="cart-footer">
                <label><input type="checkbox" id="selectAllBottom"> Chọn tất cả</label>
                <div class="total">Tổng: <strong>0đ</strong></div>
                <button class="checkout-btn">Mua hàng</button>
            </div>
        </form>
    </div>
    <script>
        const selectAll = document.getElementById('selectAll');
        const selectAllBottom = document.getElementById('selectAllBottom');
        function syncCheckAll(source) {
            document.querySelectorAll('.item-check').forEach(cb => cb.checked = source.checked);
            selectAll.checked = source.checked;
            selectAllBottom.checked = source.checked;
        }
        selectAll.addEventListener('change', () => syncCheckAll(selectAll));
        selectAllBottom.addEventListener('change', () => syncCheckAll(selectAllBottom));
    </script>
</body>
</html>
