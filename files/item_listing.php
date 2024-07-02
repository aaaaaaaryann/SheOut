<?php
ob_start();
session_start();
include("conn.php");
$page = 'products';

$limit = 10; // Number of entries to show in a page.
if (isset($_GET["page"])) {
    $page_number = $_GET["page"];
} else {
    $page_number = 1;
}

$initial_page = ($page_number - 1) * $limit;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="item_listing.css">
    <title>Document</title>
</head>
<body>
<?php
if (!isset($_SESSION['customer_id'])) {
    include "topbar.php";
    $customer_id = "";
} else {
    $customer_id = $_SESSION['customer_id'];
    include "usertopbar.php";
}
?>

<div class="filters">
    <div class="filter_inside">
        <button class="filterings">Filter</button>
        <div class="filter_content">
            <hr>
            
            <form method="get">
            <div class="search">
                  <input type="text" name="search" placeholder="Search..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
             </div>
                <?php
                // Fetch filters from the database
                $query_categories = mysqli_query($conn, "SELECT * FROM categories WHERE Visibility = 'Visible'");
                $checked_categories = [];
                if (isset($_GET['categoryitem'])) {
                    $checked_categories = $_GET['categoryitem'];
                }
                while ($query_categories_row = mysqli_fetch_assoc($query_categories)) {
                ?>
                <div class="filter_category">
                    <input type="checkbox" name="categoryitem[]" id="<?php echo $query_categories_row['category_id']; ?>" value="<?php echo $query_categories_row['category_name']; ?>"
                    <?php if (in_array($query_categories_row['category_name'], $checked_categories)) { echo "checked"; } ?> />
                    <label for="<?php echo $query_categories_row['category_id']; ?>"><?php echo $query_categories_row['category_name']; ?></label>
                </div>
                <?php } ?>
                <button type="submit" class="card-button">Search</button>
            </form>
        </div>
    </div>
</div>

<?php
function truncateTitle($title, $maxLength = 20) {
    if (strlen($title) > $maxLength) {
        return substr($title, 0, $maxLength) . '...';
    }
    return $title;
}
?>
<div class="card-container">
    <div class="sort-options">
        <form method="get" class="sort-form">
            <label for="sort">Sort by: </label>
            <select name="sort" id="sort" onchange="this.form.submit()">
                <option value="">Select</option>
                <option value="alphabetical" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'alphabetical') echo 'selected'; ?>>Alphabetical</option>
                <option value="latest" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'latest') echo 'selected'; ?>>Latest</option>
                <option value="price_asc" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') echo 'selected'; ?>>Price Ascending</option>
                <option value="price_desc" <?php if (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') echo 'selected'; ?>>Price Descending</option>
            </select>
            <input type="hidden" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <?php
            if (!empty($checked_categories)) {
                foreach ($checked_categories as $category) {
                    echo '<input type="hidden" name="categoryitem[]" value="' . htmlspecialchars($category) . '">';
                }
            }
            ?>
        </form>
    </div>

    <div class="card">
      
        <div class="candlerows">
            
            <?php
            $search_query = "";
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search_query = "AND product.product_name LIKE '%" . mysqli_real_escape_string($conn, $_GET['search']) . "%'";
            }

            $category_query = "";
            $checked_categories_toquery = [];
            if (isset($_GET['categoryitem']) && !empty($_GET['categoryitem'])) {
                $checked_categories_toquery = $_GET['categoryitem'];
                $string_checked_categories_toquery = "'" . implode("', '", $checked_categories_toquery) . "'";
                $category_query = "AND categories.category_name IN ($string_checked_categories_toquery)";
            }

            $sort_query = "";
            if (isset($_GET['sort'])) {
                switch ($_GET['sort']) {
                    case 'alphabetical':
                        $sort_query = "ORDER BY product.product_name ASC";
                        break;
                    case 'latest':
                        $sort_query = "ORDER BY product.date_added DESC";
                        break;
                    case 'price_asc':
                        $sort_query = "ORDER BY product.product_price ASC";
                        break;
                    case 'price_desc':
                        $sort_query = "ORDER BY product.product_price DESC";
                        break;
                }
            }

            $query_products = "
                SELECT product.*, GROUP_CONCAT(categories.category_name) AS categories
                FROM product_categories
                LEFT JOIN categories ON product_categories.category_id = categories.category_id
                LEFT JOIN product ON product.product_id = product_categories.product_id
                WHERE product.product_visibility = 'Visible' $category_query $search_query
                GROUP BY product.product_id
                HAVING COUNT(DISTINCT categories.category_name) = " . count($checked_categories_toquery) . "
                $sort_query
                LIMIT $initial_page, $limit";

            
            if (empty($checked_categories_toquery)) {
                $query_products = "
                    SELECT *
                    FROM product 
                    WHERE product_visibility = 'Visible' $search_query
                    $sort_query
                    LIMIT $initial_page, $limit";
            }

            $result_products = mysqli_query($conn, $query_products);

            if (mysqli_num_rows($result_products) > 0) {
                while ($query_products_row = mysqli_fetch_assoc($result_products)) {
                    $product_name = $query_products_row['product_name'];
                    $product_img = $query_products_row['product_image'];
                    $product_id = $query_products_row['product_id'];
                    $product_price = $query_products_row['product_price'];
                    $product_stock = $query_products_row['product_stock']; // Fetch product stock
            ?>

            <div class="card-row image-row">
            <a href="product_information.php?id=<?php echo htmlspecialchars($product_id); ?>">
                <?php
                if ($product_img == '') {
                ?>
                    <img src="uploads/no_img.png" style="max-width: 200px; max-height: 200px;">
                <?php
                } else {
                ?>
                    <img src="<?php echo "uploads/products/" . $product_img; ?>" style="max-width: 200px; max-height: 200px;">
                <?php
                }
                ?>
                <div class="price_title">
                    <div class="card-row title-row">
                        <a href="product_information.php?id=<?php echo htmlspecialchars($product_id); ?>" style="text-decoration: none;">
                            <h3><?php echo htmlspecialchars(truncateTitle($product_name)); ?></h3>
                        </a>
                    </div>
                    <div class="card-row description-row">
                        <p>₱<?php echo $product_price; ?></p>
                    </div>
                </div>



                
                <br><br>
                <div class="card-row button-row">
                    <?php if ($product_stock > 0) { ?>
                    <form method="post" onsubmit="return checkCustomerID()">
                        <input type="submit" value="ADD TO CART" name="addtocart<?php echo $product_id; ?>" class="card-button">
                    </form>
                    <?php } else { ?>
                    <p>Out of Stock</p>
                    <?php } ?>
                </div>
                <?php
                if (isset($_POST['addtocart' . $product_id])) {
                    if (trim($customer_id) == '') {
                        echo "<script>alert('Please log in first.');</script>";
                    } else {
                        $check_cart_query = "SELECT * FROM cart WHERE customer_id = $customer_id AND product_id = $product_id";
                        $check_cart_result = mysqli_query($conn, $check_cart_query);

                        if (mysqli_num_rows($check_cart_result) > 0) {
                            
                            $update_cart_query = "UPDATE cart SET amount = amount + 1 WHERE customer_id = $customer_id AND product_id = $product_id";
                            mysqli_query($conn, $update_cart_query);
                            echo "<script>alert('Added to cart successfully');</script>";
                        } else {
                            
                            $add_to_cart_query = "INSERT INTO cart (product_id, customer_id, amount, date_added) VALUES($product_id, $customer_id, 1, current_timestamp())";
                            mysqli_query($conn, $add_to_cart_query);
                            echo "<script>alert('Added to cart successfully');</script>";
                        }
                    }
                }
                ?>
          </a>  </div>
            <?php
                }
            } else {
                echo "No products found.";
            }

            
            if (isset($_GET['categoryitem'])) {
                $count_query = "
                    SELECT COUNT(DISTINCT product.product_id) as total
                    FROM product_categories
                    LEFT JOIN categories ON product_categories.category_id = categories.category_id
                    LEFT JOIN product ON product.product_id = product_categories.product_id
                    WHERE product.product_visibility = 'Visible' $category_query $search_query";
            } else {
                $count_query = "
                    SELECT COUNT(*) as total
                    FROM product 
                    WHERE product_visibility = 'Visible' $search_query";
            }

            $count_result = mysqli_query($conn, $count_query);
            $total_products = mysqli_fetch_assoc($count_result)['total'];
            $total_pages = ceil($total_products / $limit);
            ?>
        </div> 
    </div>
</div>
       
<div class="pagination">
    <?php
    
    $baseQueryParams = $_GET;
    
    
    if ($page_number > 1) {
        $baseQueryParams['page'] = $page_number - 1;
        $previousPageUrl = '?' . http_build_query($baseQueryParams);
        echo "<a href='$previousPageUrl'>&laquo; Previous</a>";
    }

    
    for ($i = 1; $i <= $total_pages; $i++) {
        $baseQueryParams['page'] = $i;
        $pageUrl = '?' . http_build_query($baseQueryParams);
        if ($i == $page_number) {
            echo "<a class='active'>$i</a>";
        } else {
            echo "<a href='$pageUrl'>$i</a>";
        }
    }

    
    if ($page_number < $total_pages) {
        $baseQueryParams['page'] = $page_number + 1;
        $nextPageUrl = '?' . http_build_query($baseQueryParams);
        echo "<a href='$nextPageUrl'>Next &raquo;</a>";
    }
    ?>
</div>

<script>
    function checkCustomerID() {
        var customerID = "<?php echo isset($customer_id) ? $customer_id : ''; ?>";
        if (customerID === '') {
            alert('Please log in first.');
            return false;
        }
        return true;
    }
</script>
<script>
    var acc = document.getElementsByClassName("filterings");
    var i;

    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var filter_content = this.nextElementSibling;
            if (filter_content.style.maxHeight) {
                filter_content.style.maxHeight = null;
            } else {
                filter_content.style.maxHeight = filter_content.scrollHeight + "px";
            }
        });
    }
</script>
</body>
<?php
include 'footer.php';
?>

</html>
