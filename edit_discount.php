<?php
// Kết nối cơ sở dữ liệu MySQL
$conn = new mysqli('localhost', 'root', '', 'addproducts');
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy mã giảm giá từ cơ sở dữ liệu
if (isset($_GET['id'])) {
    $discount_id = $_GET['id'];
    $sql = "SELECT * FROM discounts WHERE discount_id = $discount_id";
    $result = $conn->query($sql);
    $discount = $result->fetch_assoc();
}

// Xử lý cập nhật thông tin mã giảm giá
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = $_POST['code'];
    $description = $_POST['description'];
    $discount_type = $_POST['discount_type'];
    $discount_value = $_POST['discount_value'];
    $min_order_value = $_POST['min_order_value'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $active = isset($_POST['active']) ? 1 : 0;

    // Cập nhật dữ liệu vào cơ sở dữ liệu
    $update_sql = "UPDATE discounts SET 
        code = '$code', 
        description = '$description', 
        discount_type = '$discount_type', 
        discount_value = $discount_value, 
        min_order_value = $min_order_value, 
        start_date = '$start_date', 
        end_date = '$end_date', 
        active = $active 
        WHERE discount_id = $discount_id";

    if ($conn->query($update_sql) === TRUE) {
        echo "Mã giảm giá đã được cập nhật thành công.";
        // Điều hướng về trang quản lý mã giảm giá
        header("Location: manage_discounts.php");
    } else {
        echo "Lỗi: " . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Mã Giảm Giá</title>
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

    <h2>Sửa Mã Giảm Giá</h2>

    <form action="edit_discount.php?id=<?php echo $discount['discount_id']; ?>" method="POST">
        <label for="code">Mã Giảm Giá:</label>
        <input type="text" id="code" name="code" value="<?php echo $discount['code']; ?>" required>

        <label for="description">Mô Tả:</label>
        <textarea id="description" name="description"><?php echo $discount['description']; ?></textarea>

        <label for="discount_type">Loại Giảm Giá:</label>
        <select id="discount_type" name="discount_type" required>
            <option value="percent" <?php echo $discount['discount_type'] == 'percent' ? 'selected' : ''; ?>>Phần Trăm</option>
            <option value="fixed" <?php echo $discount['discount_type'] == 'fixed' ? 'selected' : ''; ?>>Số Tiền Cố Định</option>
        </select>

        <label for="discount_value">Giá Trị Giảm Giá:</label>
        <input type="number" id="discount_value" name="discount_value" step="0.01" value="<?php echo $discount['discount_value']; ?>" required>

        <label for="min_order_value">Giá Trị Đơn Hàng Tối Thiểu:</label>
        <input type="number" id="min_order_value" name="min_order_value" value="<?php echo $discount['min_order_value']; ?>" step="0.01">

        <label for="start_date">Ngày Bắt Đầu:</label>
        <input type="date" id="start_date" name="start_date" value="<?php echo $discount['start_date']; ?>" required>

        <label for="end_date">Ngày Hết Hạn:</label>
        <input type="date" id="end_date" name="end_date" value="<?php echo $discount['end_date']; ?>">

        <label for="active">Kích Hoạt:</label>
        <input type="checkbox" id="active" name="active" <?php echo $discount['active'] ? 'checked' : ''; ?>> Có

        <button type="submit">Cập Nhật Mã Giảm Giá</button>
    </form>

</body>
</html>

<?php
$conn->close();
?>
