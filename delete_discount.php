<?php
$conn = new mysqli('localhost', 'root', '', 'addproducts');
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}



// Kiểm tra nếu có ID được truyền vào từ URL
if (isset($_GET['id'])) {
    $discount_id = $_GET['id'];

    // Xóa mã giảm giá dựa trên discount_id
    $sql = "DELETE FROM discounts WHERE discount_id = $discount_id";

    if ($conn->query($sql) === TRUE) {
        if(confirm('Thật sự xóa mã giảm giá này?')) {
            alert('Xóa mã giảm giá thành công!');
        } 
        // Điều hướng về trang quản lý mã giảm giá
        header("Location: manage_discounts.php");
    } else {
        echo "Lỗi: " . $conn->error;
    }
}

$conn->close();
?>

