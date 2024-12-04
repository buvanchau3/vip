<?php
$conn = new mysqli('localhost', 'root', '', 'addproducts');
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}


// Truy vấn lấy tất cả mã giảm giá
$sql = "SELECT * FROM discounts";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Mã Giảm Giá</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* styles.css */
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 20px;
            }

            .container {
                max-width: 1200px;
                margin: auto;
                background: white;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            }

            h2 {
                text-align: center;
                color: #333;
                margin-bottom: 20px;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            th, td {
                padding: 12px;
                text-align: left;
                border: 1px solid #dee2e6;
            }

            th {
                background-color: #007bff;
                color: white;
            }

            tbody tr:hover {
                background-color: #f1f1f1;
            }

            td a {
                text-decoration: none;
                color: #007bff;
                padding: 6px 12px;
                border-radius: 4px;
            }

            td a:hover {
                background-color: #e7f1ff;
                border-radius: 4px;
            }

            td {
                vertical-align: middle;
            }

            /* Thêm kiểu cho trạng thái */
            td:nth-child(7) {
                font-weight: bold;
            }

            td:nth-child(7):contains('Kích Hoạt') {
                color: #28a745; /* Xanh lá cho trạng thái kích hoạt */
            }

            td:nth-child(7):contains('Không Kích Hoạt') {
                color: #dc3545; /* Đỏ cho trạng thái không kích hoạt */
            }
    </style>
</head>
<body>
    <div class="container">
        <h2>Quản Lý Mã Giảm Giá</h2>

        <!-- Hiển thị danh sách mã giảm giá -->
        <table border="1">
            <thead>
                <tr>
                    <th>Mã Giảm Giá</th>
                    <th>Mô Tả</th>
                    <th>Loại</th>
                    <th>Giá Trị</th>
                    <th>Ngày Bắt Đầu</th>
                    <th>Ngày Hết Hạn</th>
                    <th>Trạng Thái</th>
                    <th>Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['code'] . "</td>";
                        echo "<td>" . $row['description'] . "</td>";
                        echo "<td>" . ucfirst($row['discount_type']) . "</td>";
                        echo "<td>" . $row['discount_value'] . "</td>";
                        echo "<td>" . $row['start_date'] . "</td>";
                        echo "<td>" . $row['end_date'] . "</td>";
                        echo "<td>" . ($row['active'] ? 'Kích Hoạt' : 'Không Kích Hoạt') . "</td>";
                        echo "<td>
                            <a href='edit_discount.php?id=" . $row['discount_id'] . "'>Sửa</a> |
                            <a href='delete_discount.php?id=" . $row['discount_id'] . "'>Xóa</a>
                        </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>Chưa có mã giảm giá nào.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>

<?php
$conn->close();
?>
