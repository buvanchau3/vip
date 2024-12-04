<?php
// Kết nối đến cơ sở dữ liệu (cập nhật thông tin kết nối của bạn)
$conn = new mysqli('localhost', 'root', '', 'addproducts');
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $code = $_POST['code'];
    $description = $_POST['description'];
    $discount_type = $_POST['discount_type'];
    $discount_value = $_POST['discount_value'];
    $min_order_value = $_POST['min_order_value'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $active = isset($_POST['active']) ? 1 : 0;

    // Chuẩn bị và thực thi câu lệnh SQL để chèn dữ liệu
    $sql = "INSERT INTO discounts (code, description, discount_type, discount_value, min_order_value, start_date, end_date, active)
            VALUES ('$code', '$description', '$discount_type', '$discount_value', '$min_order_value', '$start_date', '$end_date', '$active')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
            alert('Mã giảm giá đã được tạo thành công!')
        </script>";
    } else {
        echo "Lỗi: " . $sql . "<br>" . $conn->error;
    }

    // Đóng kết nối
    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo Mã Giảm Giá</title>
    <link rel="stylesheet" href="styles.css">
    <style>
                    body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                        padding: 0;
                    }

                    .container {
                        width: 50%;
                        margin: 50px auto;
                        padding: 20px;
                        background-color: #fff;
                        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
                        border-radius: 8px;
                    }

                    h2 {
                        text-align: center;
                        color: #333;
                    }

                    form {
                        display: flex;
                        flex-direction: column;
                    }

                    label {
                        margin: 8px 0 5px;
                        font-weight: bold;
                        color: #333;
                    }

                    input[type="text"], input[type="number"], input[type="date"], textarea, select {
                        padding: 8px;
                        margin-bottom: 15px;
                        border: 1px solid #ddd;
                        border-radius: 4px;
                    }

                    textarea {
                        height: 100px;
                    }

                    button {
                        padding: 10px;
                        background-color: #4CAF50;
                        color: white;
                        border: none;
                        border-radius: 4px;
                        cursor: pointer;
                    }

                    button:hover {
                        background-color: #45a049;
                    }

    </style>
</head>
<body>

    <div class="container">
        <h2>Tạo Mã Giảm Giá Mới</h2>
        <form action="disCount.php" method="POST">
            <label for="code">Mã Giảm Giá:</label>
            <input type="text" id="code" name="code" required>

            <label for="description">Mô Tả:</label>
            <textarea id="description" name="description"></textarea>

            <label for="discount_type">Loại Giảm Giá:</label>
            <select id="discount_type" name="discount_type" required>
                <option value="percent">Phần Trăm</option>
                <option value="fixed">Số Tiền Cố Định</option>
            </select>

            <label for="discount_value">Giá Trị Giảm Giá:</label>
            <input type="number" id="discount_value" name="discount_value" step="0.01" required>

            <label for="min_order_value">Giá Trị Đơn Hàng Tối Thiểu:</label>
            <input type="number" id="min_order_value" name="min_order_value" step="0.01">

            <label for="start_date">Ngày Bắt Đầu:</label>
            <input type="date" id="start_date" name="start_date" value="<?php echo date('Y-m-d'); ?>" required>

            <label for="end_date">Ngày Hết Hạn:</label>
            <input type="date" id="end_date" name="end_date">

            <label for="active">Kích Hoạt:</label>
            <input type="checkbox" id="active" name="active" checked> Có

            <button type="submit">Tạo Mã Giảm Giá</button>
        </form>
    </div>

</body>
</html>
