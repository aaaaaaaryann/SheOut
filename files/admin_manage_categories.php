<?php
ob_start();
session_start();
$page = 'filter';
include("conn.php");
include "admin_topbar.php";
$admin_id = $_SESSION['admin_id'];

if ($admin_id == "") {
    header('Location: adminlogin.php');
    exit;
}

// Handle new category submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_category'])) {
    $category_name = htmlspecialchars($_POST['category_name'], ENT_QUOTES);

    $query_insert = mysqli_query($conn, "INSERT INTO categories (category_name, visibility) VALUES ('$category_name', 'Visible')");

    if ($query_insert) {
        echo "<script>alert('Category created successfully');</script>";
    } else {
        echo "<script>alert('Error creating category');</script>";
    }
}

// Handle category update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_category'])) {
    $category_id = $_POST['category_id'];
    $category_name = htmlspecialchars($_POST['category_name'], ENT_QUOTES);

    $query_update = mysqli_query($conn, "UPDATE categories SET category_name = '$category_name' WHERE category_id = '$category_id'");

    if ($query_update) {
        echo "<script>alert('Category updated successfully');</script>";
    } else {
        echo "<script>alert('Error updating category');</script>";
    }
}

// Handle category visibility change (Archive/Unarchive)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_visibility'])) {
    $category_id = $_POST['category_id'];
    $new_visibility = $_POST['new_visibility'];

    $query_change_visibility = mysqli_query($conn, "UPDATE categories SET visibility = '$new_visibility' WHERE category_id = '$category_id'");

    if ($query_change_visibility) {
        if ($new_visibility == 'Archived') {
            echo "<script>alert('Category archived successfully');</script>";
        } elseif ($new_visibility == 'Visible') {
            echo "<script>alert('Category restored successfully');</script>";
        }
    } else {
        echo "<script>alert('Error updating category visibility');</script>";
    }
}

// Fetch visible categories
$query_visible_categories = mysqli_query($conn, "SELECT * FROM categories WHERE visibility = 'Visible'");

// Fetch archived categories
$query_archived_categories = mysqli_query($conn, "SELECT * FROM categories WHERE visibility = 'Archived'");
$view = isset($_GET['view']) ? $_GET['view'] : 'visible';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="admin_manage_categories.css">
</head>
<body>
<div class="right">
    <!-- Buttons to switch between visible and archived categories -->
    <form method="get" action="">
        <div class="high-container">
            <button type="submit" class="high <?php echo $view == 'visible' ? 'active' : ''; ?>" name="view" value="visible">Visible Categories</button>
            <button type="submit" class="high <?php echo $view == 'archived' ? 'active' : ''; ?>" name="view" value="archived">Archived Categories</button>
        </div>
    </form><br>
    <button id="myBtn" class="highs">Create New Category</button>
    <table id="category" class="display">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Edit</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if (isset($_GET['view']) && $_GET['view'] == 'archived') {
                // Display archived categories
                while ($category_row = mysqli_fetch_assoc($query_archived_categories)) {
                    ?>
                    <tr>
                        <td><a href="#" class="category-link" data-id="<?php echo $category_row['category_id']; ?>"><?php echo $category_row['category_id']; ?></a></td>
                        <td><?php echo $category_row['category_name']; ?></td>
                        <td><button class="edit-btn" data-id="<?php echo $category_row['category_id']; ?>" data-name="<?php echo $category_row['category_name']; ?>"><i class="fa fa-pencil"></i></button></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="category_id" value="<?php echo $category_row['category_id']; ?>">
                                <input type="hidden" name="new_visibility" value="Visible">
                                <button type="submit" name="change_visibility" class="restore-btn"><i class="fa fa-undo"></i> Restore</button>
                            </form>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                // Display visible categories by default
                while ($category_row = mysqli_fetch_assoc($query_visible_categories)) {
                    $categoryid = $category_row['category_id'];
                    ?>
                    <tr>
                        <td><a href="#" class="category-link" data-id="<?php echo $category_row['category_id']; ?>"><?php echo $category_row['category_id']; ?></a></td>
                        <td><?php echo $category_row['category_name']; ?></td>
                        <td><button class="edit-btn" data-id="<?php echo $category_row['category_id']; ?>" data-name="<?php echo $category_row['category_name']; ?>"><i class="fa fa-pencil"></i></button></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="category_id" value="<?php echo $category_row['category_id']; ?>">
                                <input type="hidden" name="new_visibility" value="Archived">
                                <button type="submit" name="change_visibility" class="archive-btn"><i class="fa fa-archive"></i> Archive</button>
                            </form>
                        </td>
                    </tr>
                    <div id="div<?php echo $categoryid; ?>" class="product-details" style="display: none;">
                    <div class="area">
                    <h3><?php echo $category_row['category_name']; ?></h3>
                        <?php
                        $query_product = mysqli_query($conn, "SELECT product.product_id, category_id, product_name, product_image FROM `product_categories` LEFT JOIN product ON product_categories.product_id = product.product_id where product_categories.category_id = $categoryid;");
                        foreach($query_product as $row){
                            ?>
                            <div class="contents" style="display: flex; align-items: center; padding: 10px; border-bottom: 1px solid #ccc;">
                                <div style="flex: 1;">
                                    <a href="admin_manage_product_edit.php?id=<?php echo $row['product_id']; ?>"><?php echo $row['product_id']; ?></a>
                                </div>
                                <div style="flex: 1;">
                                    <?php if($row['product_image'] == '') { ?>
                                        <img src="uploads/no_img.png" style="height: 100px;">
                                    <?php } else { ?>
                                        <img src="uploads/products/<?php echo $row['product_image']; ?>" style="height: 100px;">
                                    <?php } ?>
                                </div>
                                <div style="flex: 2;">
                                    <p><?php echo $row['product_name']; ?></p>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    </div>
                    <?php
                }
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Modal for displaying products -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <span id="closeProductModal" class="close">&times;</span>
        <div id="productDetails"></div>
    </div>
</div>

<!-- Modal for creating and editing categories -->
<div id="myModal" class="modal">
    <div class="modal-content">
        <span id="closeCategoryModal" class="close">&times;</span>
        <div class="modal-header">
            <h2 id="modal-title">Create New Category</h2>
        </div>
        <div class="modal-body">
            <form method="post" name="manage_category" id="manage_category">
                <input type="hidden" name="category_id" id="category_id">
                <label>Category Name</label>
                <input type="text" name="category_name" class="form-control" id="category_name" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" name="create_category" id="create_category_btn">Create Category</button>
                    <button type="submit" class="btn btn-primary" name="edit_category" id="edit_category_btn" style="display:none;">Edit Category</button>
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
        $('#category').DataTable();
    });

    var modal = document.getElementById("myModal");
    var btn = document.getElementById("myBtn");
    var span = document.getElementById("closeCategoryModal");

    btn.onclick = function() {
        document.getElementById('modal-title').textContent = 'Create New Category';
        document.getElementById('create_category_btn').style.display = 'inline-block';
        document.getElementById('edit_category_btn').style.display = 'none';
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

    $(document).on("click", ".edit-btn", function(){
        var category_id = $(this).data('id');
        var category_name = $(this).data('name');
        $('#category_id').val(category_id);
        $('#category_name').val(category_name);
        document.getElementById('modal-title').textContent = 'Edit Category';
        document.getElementById('create_category_btn').style.display = 'none';
        document.getElementById('edit_category_btn').style.display = 'inline-block';
        modal.style.display = "block";
    });

    var productModal = document.getElementById("productModal");
    var spanProduct = document.getElementById("closeProductModal");

    spanProduct.onclick = function() {
        productModal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == productModal) {
            productModal.style.display = "none";
        }
    }

    $(document).on("click", ".category-link", function(){
        var categoryId = $(this).data('id');
        var productDetails = $('#div' + categoryId).html();
        $('#productDetails').html(productDetails);
        productModal.style.display = "block";
    });

    $(document).ready(function() {
        $('#category').DataTable();
    });
</script>
</body>
</html>
