<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'addproducts');
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra xem mã giảm giá có được gửi hay không
if (isset($_POST['discount_code'])) {
    $discount_code = $_POST['discount_code'];

    // Truy vấn mã giảm giá trong cơ sở dữ liệu
    $sql = "SELECT * FROM discounts WHERE code = ? AND active = 1 AND NOW() BETWEEN start_date AND end_date";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $discount_code);
    $stmt->execute();
    $result = $stmt->get_result();

    // Kiểm tra nếu có mã giảm giá hợp lệ
    if ($result->num_rows > 0) {
        $discount = $result->fetch_assoc();
        
        // Lấy thông tin về loại giảm giá và giá trị giảm giá
        $discount_type = $discount['discount_type'];
        $discount_value = $discount['discount_value'];
        $min_order_value = $discount['min_order_value'];

        // Kiểm tra xem giỏ hàng có tồn tại và không rỗng
        if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
            // Tính tổng giá giỏ hàng
            $total_cart_price = 0;
            foreach ($_SESSION['cart'] as $item) {
                $total_cart_price += $item['total_price'];
            }

            // Kiểm tra giá trị đơn hàng có đủ điều kiện để áp dụng giảm giá không
            if ($total_cart_price >= $min_order_value) {
                // Tính toán giảm giá
                if ($discount_type == 'percent') {
                    $discount_amount = ($total_cart_price * $discount_value) / 100;
                } else {
                    $discount_amount = $discount_value;
                }

                // Lưu vào session
                $_SESSION['discount_amount'] = $discount_amount;
                echo "<script>alert('Mã giảm giá áp dụng thành công!');</script>";
            } else {
                echo "<script>alert('Giá trị đơn hàng không đủ điều kiện để áp dụng mã giảm giá này.');</script>";
            }
        } else {
            echo "<script>alert('Giỏ hàng của bạn đang trống.');</script>";
        }
    } else {
        echo "<script>alert('Mã giảm giá không hợp lệ hoặc đã hết hạn.');</script>";
    }
}

// Sau khi thông báo được hiển thị, chuyển hướng người dùng đến trang giỏ hàng
echo "<script>window.location.href = 'cart.php';</script>";
exit();
?>
