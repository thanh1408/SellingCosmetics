<?php
// Kết nối CSDL
$conn = new mysqli("localhost", "root", "", "qlsua");
$conn->set_charset("utf8");

// Kiểm tra và lấy từ khóa tìm kiếm
$query = isset($_GET['query']) ? trim($_GET['query']) : '';

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kết quả tìm kiếm</title>
    <style>
        body { font-family: Arial; background: #fff0f5; padding: 20px; }
        .result { background: white; padding: 20px; border-radius: 10px; }
        .product { margin-bottom: 15px; border-bottom: 1px solid #ccc; padding-bottom: 10px; }
        .product img { width: 80px; height: auto; margin-right: 10px; vertical-align: middle; }
        .product-name { color: #e84a70; font-weight: bold; font-size: 18px; }
    </style>
</head>
<body>
    <div class="result">
        <?php if ($query !== ''): ?>
            <h2>Kết quả tìm kiếm cho: <em><?= htmlspecialchars($query) ?></em></h2>
            <?php
            // Tìm trong bảng sua (sản phẩm) theo tên sản phẩm hoặc loại sản phẩm (không phân biệt hoa/thường)
            $sql = "SELECT * FROM tt_sua WHERE LOWER(ten_sua) LIKE LOWER(?) OR LOWER(loai_sua) LIKE LOWER(?)";
            $stmt = $conn->prepare($sql);
            $param = '%' . strtolower($query) . '%';
            $stmt->bind_param("ss", $param, $param);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="product">';
                    echo '<img src="images/' . htmlspecialchars($row['hinh']) . '" alt="' . htmlspecialchars($row['ten_sua']) . '">';
                    echo '<span class="product-name">' . htmlspecialchars($row['ten_sua']) . '</span>';
                    echo ' - ' . htmlspecialchars($row['loai_sua']) . ' - ' . number_format($row['don_gia']) . ' VNĐ';
                    echo '</div>';
                }
            } else {
                echo "<p>Không tìm thấy sản phẩm nào phù hợp.</p>";
            }

            $stmt->close();
            ?>
        <?php else: ?>
            <p>Vui lòng nhập từ khóa tìm kiếm.</p>
        <?php endif; ?>

        <?php
        $conn->close();
        ?>
    </div>
</body>
</html>
