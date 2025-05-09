<?php
// Kết nối CSDL
$conn = new mysqli("localhost", "root", "", "db_mypham");
$conn->set_charset("utf8");

// Kiểm tra lỗi kết nối
if ($conn->connect_error) {
    die("Kết nối CSDL thất bại: " . $conn->connect_error);
}

// Lấy từ khóa tìm kiếm nếu có
$query = isset($_GET['query']) ? trim(urldecode($_GET['query'])) : '';

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Kết quả tìm kiếm</title>
    <style>
        body { font-family: Arial; background: #fff0f5; padding: 20px; }
        .result { background: white; padding: 20px; border-radius: 10px; }
        .product { margin-bottom: 15px; border-bottom: 1px solid #ccc; padding-bottom: 10px; display: flex; align-items: center; }
        .product img { width: 80px; height: auto; margin-right: 10px; border-radius: 5px; }
        .product-name { color: #e84a70; font-weight: bold; font-size: 18px; }
        form { margin-bottom: 20px; }
        input[type="text"] { padding: 8px; width: 250px; border-radius: 5px; border: 1px solid #ccc; }
        button { padding: 8px 12px; background-color: #e84a70; color: white; border: none; border-radius: 5px; cursor: pointer; }
        h2 { text-align: center; color: #e84a70; }
    </style>
</head>
<body>
    <div class="result">
        <?php if (!empty($query)): ?>
            <h2>Kết quả tìm kiếm cho: <em><?= htmlspecialchars($query) ?></em></h2>
            <?php
            // Truy vấn tìm kiếm không phân biệt chữ hoa/thường
            $sql = "SELECT * FROM product WHERE LOWER(name) LIKE ? OR LOWER(category) LIKE ?";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $param = '%' . mb_strtolower($query, 'UTF-8') . '%';
                $stmt->bind_param("ss", $param, $param);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="product">';
                        echo '<img src="images/' . htmlspecialchars($row['product_image']) . '" alt="' . htmlspecialchars($row['name']) . '">';
                        echo '<div>';
                        echo '<div class="product-name">' . htmlspecialchars($row['name']) . '</div>';
                        echo '<div>' . htmlspecialchars($row['category']) . ' - ' . number_format($row['price']) . ' VNĐ</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo "<p>Không tìm thấy sản phẩm nào phù hợp.</p>";
                }

                $stmt->close();
            } else {
                echo "<p>Lỗi khi chuẩn bị truy vấn: " . htmlspecialchars($conn->error) . "</p>";
            }
            ?>
        <?php else: ?>
            <p>Vui lòng nhập từ khóa tìm kiếm.</p>
        <?php endif; ?>

        <?php $conn->close(); ?>
    </div>
</body>
</html>
