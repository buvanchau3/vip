<?php
if (isset($_GET['product_id'])) {
    $product_id = (int)$_GET['product_id'];

    $conn = new mysqli('localhost', 'root', '', 'addproducts');
    if ($conn->connect_error) {
        die('Kết nối thất bại: ' . $conn->connect_error);
    }

    $query = $conn->prepare('SELECT id, color, size, price FROM product_variants WHERE product_id = ?');
    $query->bind_param('i', $product_id);
    $query->execute();
    $result = $query->get_result();

    $variants = [];
    while ($row = $result->fetch_assoc()) {
        $variants[] = $row;
    }

    $query->close();
    $conn->close();

    echo json_encode(['variants' => $variants]);
}
