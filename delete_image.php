<?php
// Kết nối đến cơ sở dữ liệu
$conn = new mysqli('localhost', 'root', '', 'addproducts');
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Kiểm tra xem ID ảnh có tồn tại trong URL hay không
if (isset($_GET['id'])) {
    $image_id = intval($_GET['id']); // Lấy ID và chuyển sang số nguyên để tránh lỗi

    // Truy vấn lấy đường dẫn ảnh
    $stmt = $conn->prepare("SELECT image_path FROM product_images WHERE id = ?");
    $stmt->bind_param("i", $image_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $image = $result->fetch_assoc();

    if ($image) {
        $file_path = $image['image_path'];

        // Xóa file ảnh khỏi thư mục nếu tồn tại
        if (file_exists($file_path)) {
            unlink($file_path);
        }

        // Xóa ảnh khỏi cơ sở dữ liệu
        $stmt = $conn->prepare("DELETE FROM product_images WHERE id = ?");
        $stmt->bind_param("i", $image_id);
        $stmt->execute();
        echo"<script>
        alert('Xóa ảnh phụ thành công')</script>";
        
        // Chuyển hướng người dùng quay lại trang trước
        header("Location: {$_SERVER['HTTP_REFERER']}");
        exit();
    } else {
        echo "Ảnh không tồn tại.";
    }
} else {
    echo "Không tìm thấy ID ảnh.";
}
?>
