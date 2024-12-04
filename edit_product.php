<?php
// Kết nối cơ sở dữ liệu
$conn = new mysqli('localhost', 'root', '', 'addproducts');
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy danh sách danh mục
$categories = $conn->query("SELECT id, name FROM categories");

// Lấy thông tin sản phẩm hiện tại
if (isset($_GET['edit_product_id'])) {
    $product_id = $_GET['edit_product_id'];

    // Lấy dữ liệu sản phẩm từ cơ sở dữ liệu
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    // Lấy biến thể sản phẩm hiện tại từ bảng `product_variants`
    $variants = [];
    $stmt = $conn->prepare("SELECT color, size, price, stock FROM product_variants WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $variants[] = $row;
    }
}

// Xử lý dữ liệu từ form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $category_id = $_POST['category_id'];
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

    // Cập nhật sản phẩm vào bảng `products`
    $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, quantity = ?, main_image = ? WHERE id = ?");
    $stmt->bind_param("ssdisi", $name, $description, $price, $quantity, $image, $product_id);
    $stmt->execute();
    $stmt->close();

    // Xóa các biến thể cũ và thêm lại
    $stmt = $conn->prepare("DELETE FROM product_variants WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->close();

    // Thêm biến thể sản phẩm (nếu có)
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

    // Thêm ảnh phụ vào bảng `product_images`
    foreach ($secondary_images as $secondary_image) {
        $stmt = $conn->prepare("INSERT INTO product_images (product_id, image_path) VALUES (?, ?)");
        $stmt->bind_param("is", $product_id, $secondary_image);
        $stmt->execute();
        $stmt->close();
    }

    echo "<script>
    alert('Cập nhật sản phẩm thành công!');
    window.location.href = 'view_products.php';
    </script>";
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="add.css">
    <title>Chỉnh sửa Sản Phẩm</title>
</head>

<body>
    <h1>Chỉnh sửa Sản Phẩm</h1>
    <form action="edit_product.php?edit_product_id=<?= $product_id ?>" method="POST" enctype="multipart/form-data">
        <!-- Tên sản phẩm -->
        <div>
            <label for="name">Tên sản phẩm:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>" required>
        </div>

        <!-- Mô tả -->
        <div>
            <label for="description">Mô tả:</label>
            <textarea id="description" name="description"><?= htmlspecialchars($product['description']) ?></textarea>
        </div>

        <div>
            <label for="name">Số lượng sản phẩm:</label>
            <input type="text" id="quantity" name="quantity" value="<?= htmlspecialchars($product['quantity']) ?>" required>
        </div>

        <!-- Danh mục -->
        <div>
            <label for="category_id">Danh mục:</label>
            <select id="category_id" name="category_id" required>
                <option value="">Chọn danh mục</option>
                <?php while ($row = $categories->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>" <?= $product['category_id'] == $row['id'] ? 'selected' : '' ?>><?= htmlspecialchars($row['name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <!-- Giá -->
        <div>
            <label for="price">Giá cơ bản:</label>
            <input type="number" id="price" name="price" step="0.01" value="<?= htmlspecialchars($product['price']) ?>" required>
        </div>

        <!-- Ảnh sản phẩm -->
        <div>
            <label for="main_image">Ảnh Chính:</label>
            <input type="file" id="main_image" name="main_image">
            <?php if ($product['main_image']): ?>
                <img src="<?= $product['main_image'] ?>" alt="Ảnh chính sản phẩm" width="100">
            <?php endif; ?>
        </div>

        <!-- Ảnh sản phẩm phụ -->
        <div>
            <label for="secondary_images">Ảnh Phụ:</label>
            <input type="file" id="secondary_images" name="secondary_images[]" multiple>
            <?php
            // Hiển thị ảnh phụ nếu có
            $stmt = $conn->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                echo '<img src="' . $row['image_path'] . '" alt="Ảnh phụ sản phẩm" width="100">';
            }
            ?>
        </div>

        <!-- Biến thể sản phẩm -->
        <div id="variants">
            <h3>Biến thể</h3>
            <?php
            if (!empty($variants)) {
                foreach ($variants as $variant) {
                    echo '<div class="variant">
                            <label>Màu sắc:</label>
                            <input type="text" name="variant_color[]" value="' . htmlspecialchars($variant['color']) . '" placeholder="Màu sắc" required>

                            <label>Kích thước:</label>
                            <input type="text" name="variant_size[]" value="' . htmlspecialchars($variant['size']) . '" placeholder="Kích thước" required>

                            <label>Giá:</label>
                            <input type="number" name="variant_price[]" step="0.01" value="' . htmlspecialchars($variant['price']) . '" required>

                            <label>Tồn kho:</label>
                            <input type="number" name="variant_stock[]" value="' . htmlspecialchars($variant['stock']) . '" required>

                            <button type="button" onclick="removeVariant(this)">Xóa</button>
                        </div>';
                }
            } else {
                echo '<div class="variant">
                        <label>Màu sắc:</label>
                        <input type="text" name="variant_color[]" placeholder="Ví dụ: Màu Đen" required>
                        
                        <label>Kích thước:</label>
                        <input type="text" name="variant_size[]" placeholder="Ví dụ: Kích thước S" required>
                        
                        <label>Giá:</label>
                        <input type="number" name="variant_price[]" step="0.01" required>
                        
                        <label>Tồn kho:</label>
                        <input type="number" name="variant_stock[]" required>

                        <button type="button" onclick="removeVariant(this)">Xóa</button>
                    </div>';
            }
            ?>
        </div>
        
        <button type="button" onclick="addVariant()">Thêm Biến Thể</button>

        <!-- Submit -->
        <div>
            <button type="submit">Cập nhật Sản Phẩm</button>
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
