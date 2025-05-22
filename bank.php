<?php
session_start();

$order_code = "DH" . rand(10000, 99999);
$_SESSION['order_code'] = $order_code;

$total_amount = isset($_SESSION['order_temp']) ? $_SESSION['order_temp']['price'] * $_SESSION['order_temp']['qty'] : 0;

$user_name = $_SESSION['username'] ?? 'Khách hàng';

$banks = [
    'vcb' => 'Vietcombank',
    'acb' => 'ACB',
    'techcombank' => 'Techcombank',
    'bidv' => 'BIDV',
    'mbbank' => 'MB Bank',
];
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8" />
    <title>Chọn ngân hàng và nhập thông tin chuyển khoản</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fafafa;
            padding: 30px;
        }

        .container {
            max-width: 500px;
            margin: auto;
            background: #fff;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgb(0 0 0 / 0.1);
        }

        h2 {
            color: #e94c64;
            text-align: center;
            margin-bottom: 25px;
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 6px;
            color: #444;
        }

        form {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            /* để xuống dòng khi nhỏ màn hình */
        }

        form>label {
            flex-basis: 100%;
            font-weight: bold;
            margin-bottom: 5px;
        }

        form>select,
        form>input[type="text"] {
            flex: 1;
            /* chiếm cùng tỉ lệ ngang nhau */
            padding: 8px 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
            min-width: 180px;
            /* đảm bảo không quá nhỏ */
        }

        select,
        input[type=text] {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 18px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 15px;
        }

        .fixed-info {
            background: #f0f0f0;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #222;
            font-size: 17px;
            width: 100%;
        }

        .fixed-info span {
            font-weight: bold;
            color: #e94c64;
        }

        button {
            width: 100%;
            padding: 14px 0;
            border: none;
            background: #e94c64;
            color: white;
            font-size: 18px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background: #d54058;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Thông tin chuyển khoản</h2>

        <form action="thankyou.php" method="POST">
            <label for="bank">Chọn ngân hàng</label>
            <select name="bank" id="bank" required>
                <option value="" disabled selected>-- Chọn ngân hàng --</option>
                <?php foreach ($banks as $key => $name): ?>
                    <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($name) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="account_number">Số tài khoản</label>
            <input type="text" id="account_number" name="account_number" placeholder="Nhập số tài khoản" required />

            <label for="account_holder">Chủ tài khoản</label>
            <input type="text" id="account_holder" name="account_holder" placeholder="Nhập tên chủ tài khoản" required />

            <div class="fixed-info">
                Số tiền cần chuyển: <span><?= number_format($total_amount) ?>đ</span><br />
                Nội dung chuyển khoản: <span><?= htmlspecialchars($user_name) ?> chuyển tiền</span>
            </div>

            <input type="hidden" name="order_code" value="<?= $order_code ?>">
            <input type="hidden" name="amount" value="<?= $total_amount ?>">

            <button type="submit">Xác nhận đã chuyển khoản</button>
        </form>
    </div>
</body>

</html>