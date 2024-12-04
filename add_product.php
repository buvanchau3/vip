<?php
// Kết nối cơ sở dữ liệu
$conn = new mysqli('localhost', 'root', '', 'addproducts');
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy danh sách danh mục
$categories = $conn->query("SELECT id, name FROM categories");

// Xử lý dữ liệu từ form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];
    $quantity = $_POST['quantity'];
    $price = $_POST['price'];
    $image = '';
    $secondary_images = [];

    if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $image_name = basename($_FILES['main_image']['name']);
        $image = $upload_dir . $image_name;
    
        // Di chuyển file ảnh đến thư mục uploads
        if (!move_uploaded_file($_FILES['main_image']['tmp_name'], $image)) {
            die("Không thể upload ảnh.");
        }
    } else {
        die("Chưa chọn ảnh hoặc có lỗi xảy ra.");
    }
    
    
    // Xử lý ảnh phụ
    if (isset($_FILES['secondary_images']) && $_FILES['secondary_images']['error'][0] === UPLOAD_ERR_OK) {
        foreach ($_FILES['secondary_images']['name'] as $key => $image_name) {
            $target_file = $target_dir . basename($image_name);
            if (move_uploaded_file($_FILES['secondary_images']['tmp_name'][$key], $target_file)) {
                $secondary_images[] = $target_file; // Thêm ảnh phụ vào mảng
            }
        }
    }

    // Thêm sản phẩm vào bảng `products`
   // Thêm sản phẩm vào bảng `products`
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, quantity, main_image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdis", $name, $description, $price, $quantity, $image);
        
        $stmt->execute();

        // Lấy ID của sản phẩm vừa thêm
        $product_id = $stmt->insert_id;

        // Đóng câu lệnh chuẩn bị
        $stmt->close();


    // Thêm ảnh phụ vào bảng `product_images`
    foreach ($secondary_images as $secondary_image) {
        $stmt = $conn->prepare("INSERT INTO product_images (product_id, image_path) VALUES (?, ?)");
        $stmt->bind_param("is", $product_id, $secondary_image);
        $stmt->execute();
        $stmt->close();
    }

    // Thêm biến thể sản phẩm (nếu có)
    // Thêm các biến thể vào database
if (!empty($_POST['variant_color']) && !empty($_POST['variant_size']) && !empty($_POST['variant_price']) && !empty($_POST['variant_stock'])) {
    $variant_colors = $_POST['variant_color'];
    $variant_sizes = $_POST['variant_size'];
    $variant_prices = $_POST['variant_price'];
    $variant_stocks = $_POST['variant_stock'];

    // Thêm các biến thể vào database
    $stmt = $conn->prepare("INSERT INTO product_variants (product_id, color, size, price, stock) VALUES (?, ?, ?, ?, ?)");
    foreach ($variant_colors as $index => $variant_color) {
        $variant_size = $variant_sizes[$index];
        $variant_price = $variant_prices[$index];
        $variant_stock = $variant_stocks[$index];
        $stmt->bind_param("issdi", $product_id, $variant_color, $variant_size, $variant_price, $variant_stock);
        $stmt->execute();
    }
    $stmt->close();
}


    echo "<script>
    alert('Thêm sản phẩm thành công!');
    </script>";
}

?>


<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="add.css">
    <title>Thêm Sản Phẩm</title>
</head>

<body>
    <h1>Thêm Sản Phẩm</h1>
    <form action="add_product.php" method="POST" enctype="multipart/form-data">
        <!-- Tên sản phẩm -->
        <div>
            <label for="name">Tên sản phẩm:</label>
            <input type="text" id="name" name="name" required>
        </div>

        <!-- Mô tả -->
        <div>
            <label for="description">Mô tả:</label>
            <textarea id="description" name="description"></textarea>
        </div>

        <!-- Danh mục -->
        <div>
            <label for="category_id">Danh mục:</label>
            <select id="category_id" name="category_id" required>
                <option value="">Chọn danh mục</option>
                <?php while ($row = $categories->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <!-- Giá -->
        <div>
            <label for="price">Giá cơ bản:</label>
            <input type="number" id="price" name="price" step="0.01" required>
        </div>


        <div>
            <label for="quantity">Số lượng:</label>
            <input type="number" id="quantity" name="quantity" required><br>
        </div>

        <!-- Ảnh sản phẩm -->
        <div>
            <label for="main_image">Ảnh Chính:</label>
            <input type="file" id="main_image" name="main_image">
        </div>

        <!-- Ảnh sản phẩm phụ -->
        <div>
            <label for="secondary_images">Ảnh Phụ:</label>
            <input type="file" id="secondary_images" name="secondary_images[]" multiple>
        </div>

        <!-- Biến thể sản phẩm -->
        <div id="variants">
            <h3>Biến thể</h3>

            <!-- Một ví dụ cho biến thể (Màu sắc và Kích thước) -->
            <div class="variant">
                <label for="variant_color">Màu sắc:</label>
                <input type="text" name="variant_color[]" placeholder="Màu sắc" required>

                <label for="variant_size">Kích thước:</label>
                <input type="text" name="variant_size[]" placeholder="Kích thước" required>

                <label for="variant_price">Giá:</label>
                <input type="number" name="variant_price[]" step="0.01" required>

                <label for="variant_stock">Tồn kho:</label>
                <input type="number" name="variant_stock[]" required>

                <button type="button" onclick="removeVariant(this)">Xóa</button>
            </div>
        </div>

        <button type="button" onclick="addVariant()">Thêm Biến Thể</button>

        <!-- Submit -->
        <div>
            <button type="submit">Thêm Sản Phẩm</button>
        </div>

    </form>

    <script>
    function addVariant() {
        const variantsDiv = document.getElementById('variants');
        const newVariant = document.createElement('div');
        newVariant.className = 'variant';
        newVariant.innerHTML = `
        <label>Màu sắc:</label>
        <input type="text" name="variant_color[]" placeholder="Ví dụ: Màu Đen" required>
        
        <label>Kích thước:</label>
        <input type="text" name="variant_size[]" placeholder="Ví dụ: Kích thước S" required>
        
        <label>Giá:</label>
        <input type="number" name="variant_price[]" step="0.01" required>
        
        <label>Tồn kho:</label>
        <input type="number" name="variant_stock[]" required>
        
        <button type="button" onclick="removeVariant(this)">Xóa</button>
    `;
        variantsDiv.appendChild(newVariant);
    }

    function removeVariant(button) {
        button.parentElement.remove();
    }
    </script>
</body>

</html>