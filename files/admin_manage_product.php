<?php
ob_start();
session_start();
$page = 'products';
include("conn.php");
include "admin_topbar.php";
$admin_id = $_SESSION['admin_id'];

if ($admin_id == "") { 
    header('Location: adminlogin.php');
    exit;
}

$query_categories = mysqli_query($conn, "SELECT * FROM categories WHERE visibility = 'Visible'");


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_product'])) {
    $product_name = htmlspecialchars($_POST['product_name'], ENT_QUOTES);
    $product_description = nl2br(htmlspecialchars($_POST['product_description'], ENT_QUOTES));
    $product_price = $_POST['product_price'];
    $product_stock = $_POST['product_stock'];
    $product_visibility = 'Visible';
    $date_added = date('Y-m-d H:i:s');
    $categories = isset($_POST['categories']) ? $_POST['categories'] : [];

    
    $target_dir = "uploads/products/";
    $main_image = '';

    if (!empty($_FILES['main_image']['name'])) {
        $target_file = $target_dir . basename($_FILES["main_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["main_image"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["main_image"]["tmp_name"], $target_file)) {
                $main_image = basename($_FILES["main_image"]["name"]);
            } else {
                echo "<script>alert('Sorry, there was an error uploading your main image.');</script>";
            }
        } else {
            echo "<script>alert('File is not an image.');</script>";
        }
    }

    
    $uploaded_images = [];
    if (!empty($_FILES['product_images']['name'][0])) {
        $total = count($_FILES['product_images']['name']);
        if ($total > 5) {
            die("Error: You can only upload a maximum of 5 images.");
        }

        for ($i = 0; $i < $total; $i++) {
            $target_dirs = "uploads/products/";
            $target_file = $target_dirs . basename($_FILES["product_images"]["name"][$i]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $check = getimagesize($_FILES["product_images"]["tmp_name"][$i]);
            if ($check !== false) {
                if (move_uploaded_file($_FILES["product_images"]["tmp_name"][$i], $target_file)) {
                    $uploaded_images[] = basename($_FILES["product_images"]["name"][$i]);
                } else {
                    echo "<script>alert('Sorry, there was an error uploading one of your product images.');</script>";
                }
            } else {
                echo "<script>alert('File is not an image.');</script>";
            }
        }
    }

    
    $query_insert = mysqli_query($conn, "INSERT INTO product (product_name, product_description, product_image, product_price, product_stock, date_added, product_visibility) VALUES ('$product_name', '$product_description', '$main_image', '$product_price', '$product_stock', '$date_added', '$product_visibility')");
    
    if ($query_insert) {
        $product_id = mysqli_insert_id($conn);

        
        foreach ($categories as $category_id) {
            mysqli_query($conn, "INSERT INTO product_categories (product_id, category_id) VALUES ('$product_id', '$category_id')");
        }

        
        foreach ($uploaded_images as $image_name) {
            mysqli_query($conn, "INSERT INTO product_image (product_id, image_name) VALUES ('$product_id', '$image_name')");
        }

        echo "<script>alert('Product created successfully');</script>";
        header('Location: admin_manage_product.php');
        exit;
    } else {
        echo "<script>alert('Error creating product');</script>";
    }
}



$view = isset($_GET['view']) ? $_GET['view'] : 'visible';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="admin_manage_product.css">
    <style>
        .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .image-preview img {
            max-width: 100px;
            margin-bottom: 10px;
        }
        .image-container {
            position: relative;
            display: inline-block;
        }
        .remove-button {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 50%;
            cursor: pointer;
        }
    </style>
</head>

<body>
<div class="container">
    <div class="right">
        <form method="get" action="">
            <div class="high-container">
            <button type="submit" class="high <?php echo $view == 'visible' ? 'active' : ''; ?>"  name="view" value="visible">Products</button>
            <button type="submit" class="high <?php echo $view == 'archived' ? 'active' : ''; ?>"  name="view" value="archived">Archived</button>
            </div>
        </form>
<br>
        <button id="myBtn" class="highs" >Create New Product</button><br>
<div class="example">
        <table id="example" class="table table-striped" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Image</th>
                    <th>Price</th>
                    <th>Rating</th>
                    <th>Sold</th>
                    <th>Stock</th>
                    <th>Edit</th>
                    
                    <th><?php echo $view == 'archived' ? 'Unarchive' : 'Delete'; ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query_products = mysqli_query($conn, "SELECT * FROM product WHERE product_visibility = '" . ($view == 'archived' ? 'Archived' : 'Visible') . "'");
                while ($product_row = mysqli_fetch_assoc($query_products)) {
                    $productid = $product_row['product_id'];
                    $query_rating = mysqli_query($conn, "SELECT AVG(rating) as rating from ratings where product_id = '$productid'");
                    $rating_row = mysqli_fetch_assoc($query_rating);

                    $query_sold = mysqli_query($conn, "SELECT SUM(product_quantity) as sold from customer_order_product cop left join customer_order co on cop.order_id = co.order_id where product_id = '$productid' AND co.order_status = 'Completed'");
                    $sold_row = mysqli_fetch_assoc($query_sold);

                    $query_review = mysqli_query($conn, "SELECT * FROM ratings WHERE product_id = '$productid'");
                    
                    ?>
                    <tr>
                        <td><?php echo $product_row['product_id']; ?></td>
                        <td><?php echo $product_row['product_name']; ?></td>
                        <td>
                            <?php 
                                $description = $product_row['product_description'];
                                if (strlen($description) > 50) {
                                    $description = substr($description, 0, 50);
                                    $description = substr($description, 0, strrpos($description, ' ')) . '...';
                                }
                                echo htmlspecialchars($description); 
                            ?>
                           
                        </td>
                        <td>
                            <?php
                            if ($product_row['product_image'] == '') {
                                ?>
                                <img src="uploads/no_img.png" style="max-width: 150px; max-height: 150px;">
                                <?php
                            } else {
                                ?>
                                <img src="<?php echo "uploads/products/" . $product_row['product_image']; ?>" style="max-width: 150px; max-height: 150px;">
                                <?php
                            }
                            ?>
                        </td>
                        
                        
                        <td><?php echo $product_row['product_price']; ?></td>
                        <td><?php echo $rating_row['rating']; ?></td>
                        <td><?php echo $sold_row['sold']; ?></td>
                        <td><?php echo $product_row['product_stock']; ?></td>
                        <td><a href="admin_manage_product_edit.php?id=<?php echo $product_row['product_id']; ?>"><i class="fa fa-pencil"></i></a></td>
                        <td>
                            <form method="post">
                                <input type="submit" value="<?php echo $view == 'archived' ? 'Unarchive' : 'Delete'; ?> " name="<?php echo $view == 'archived' ? 'unarchive_product' : 'delete_product'; echo $product_row['product_id']; ?>">
                            </form>
                        </td>
                    </tr>
                    <?php
                    if (isset($_POST['delete_product' . $product_row['product_id']])) {
                        $query_archive = mysqli_query($conn, "UPDATE product SET product_visibility = 'Archived' WHERE product_id = '" . $product_row['product_id'] . "'");
                        echo "<script>alert('Product archived successfully');</script>";
                        ?>
                        <meta http-equiv="refresh" content="0; url='admin_manage_product.php?view=visible'" />
                        <?php
                    }

                    if (isset($_POST['unarchive_product' . $product_row['product_id']])) {
                        $query_unarchive = mysqli_query($conn, "UPDATE product SET product_visibility = 'Visible' WHERE product_id = '" . $product_row['product_id'] . "'");
                        echo "<script>alert('Product unarchived successfully');</script>";
                        ?>
                        <meta http-equiv="refresh" content="0; url='admin_manage_product.php?view=archived'" />
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
 </div>
    <!-- Modal -->
    <div id="myModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div class="modal-header">
            <h2>Create New Product</h2>
        </div>
        <div class="modal-body">
            <form method="post" name="create_product" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="product_name" class="form-label">Product Name:</label>
                    <input type="text" id="product_name" name="product_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="product_description" class="form-label">Product Description:</label>
                    <textarea id="product_description" name="product_description" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="main_image" class="form-label">Main Image:</label>
                    <input type="file" id="main_image" name="main_image" class="form-control" required>
                    <img id="main-image-preview" style="max-width: 200px; margin-top: 10px;">
                </div>
                <div class="mb-3">
                    <label for="product_images" class="form-label">Product Images:</label>
                    <input type="file" id="product_images" name="product_images[]" class="form-control" multiple>
                    <div id="image-preview" class="image-preview"></div>
                </div>
                <div class="mb-3">
                    <label for="product_price" class="form-label">Product Price:</label>
                    <input type="number" id="product_price" name="product_price" class="form-control" min="0.01" step="0.01" max="100000" required>
                </div>
                <div class="mb-3">
                    <label for="product_stock" class="form-label">Product Stock:</label>
                    <input type="number" id="product_stock" name="product_stock" class="form-control" min="0" step="1" max="1000" required>
                </div>
                <div class="mb-3">
                    <label for="categories" class="form-label">Categories:</label><br>
                    <?php while ($category_row = mysqli_fetch_assoc($query_categories)): ?>
                        <input type="checkbox" id="category_<?php echo $category_row['category_id']; ?>" name="categories[]" value="<?php echo $category_row['category_id']; ?>" class="form-control">
                        <label for="category_<?php echo $category_row['category_id']; ?>"><?php echo $category_row['category_name']; ?></label><br>
                    <?php endwhile; ?>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" name="create_product">Create Product</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function(){
        $('#example').DataTable();
    });

    var modal = document.getElementById("myModal");

    var btn = document.getElementById("myBtn");

    var span = document.getElementsByClassName("close")[0];

    btn.onclick = function() {
        modal.style.display = "block";
    }
    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
<script>
        document.addEventListener('DOMContentLoaded', function () {
            const imageInput = document.getElementById('product_images');
            const imagePreview = document.getElementById('image-preview');
            const mainImageInput = document.getElementById('main_image');
            const mainImagePreview = document.getElementById('main-image-preview');
            let imageCount = 0;

            imageInput.addEventListener('change', function () {
                const files = imageInput.files;
                if (files.length > 5) {
                    alert('You can only upload a maximum of 5 images.');
                    imageInput.value = '';
                    return;
                }

                imagePreview.innerHTML = ''; 
                imageCount = 0;

                for (const file of files) {
                    if (imageCount >= 5) break;

                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const imgContainer = document.createElement('div');
                        imgContainer.classList.add('image-container');

                        const img = document.createElement('img');
                        img.src = e.target.result;

                        const removeBtn = document.createElement('button');
                        removeBtn.classList.add('remove-button');
                        removeBtn.innerHTML = 'X';
                        removeBtn.addEventListener('click', function () {
                            imgContainer.remove();
                            imageInput.value = '';
                            imageCount--;
                        });

                        imgContainer.appendChild(img);
                        imgContainer.appendChild(removeBtn);
                        imagePreview.appendChild(imgContainer);
                        imageCount++;
                    }
                    reader.readAsDataURL(file);
                }
            });

            mainImageInput.addEventListener('change', function () {
                const file = mainImageInput.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        mainImagePreview.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
        });
    </script>
</body>
</html>
