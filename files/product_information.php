<?php
ob_start();
session_start();
include("conn.php");
$page = 'products';


function displayStars($rating) {
    $fullStars = floor($rating); 
    $halfStar = $rating - $fullStars >= 0.5 ? 1 : 0; // 
    $emptyStars = 5 - $fullStars - $halfStar; // 

    $starsHtml = '';

    
    for ($i = 0; $i < $fullStars; $i++) {
        $starsHtml .= '<span class="fa fa-star checked"></span>';
    }

    
    if ($halfStar) {
        $starsHtml .= '<span class="fa fa-star-half-o checked"></span>';
    }

    
    for ($i = 0; $i < $emptyStars; $i++) {
        $starsHtml .= '<span class="fa fa-star-o"></span>';
    }

    return $starsHtml;
}

if (!isset($_SESSION['customer_id'])) {
    include "topbar.php";
    $customer_id = "";
} else {
    $customer_id = $_SESSION['customer_id'];
    include "usertopbar.php";
}

$product_id = $_REQUEST['id'];

$query_rating = mysqli_query($conn, "SELECT AVG(rating) as rating, COUNT(rating_id) as ratingcount from ratings where product_id = '$product_id';");
$rating_row = mysqli_fetch_assoc($query_rating);
$rating = $rating_row['rating'];
$rating_count = $rating_row['ratingcount'];

$query_product = mysqli_query($conn, "SELECT * FROM product WHERE product_id = '$product_id'");

$product_images = []; 

foreach($query_product as $row){
    $product_image = $row['product_image'];
    $product_name = $row['product_name'];
    $product_description = $row['product_description'];
    $product_price = $row['product_price'];
    $product_stock = $row['product_stock'];
    if (!empty($product_image)) {
        $product_images[] = $product_image; 
    }
}


$query_additional_images = mysqli_query($conn, "SELECT image_name FROM product_image WHERE product_id = '$product_id'");
while ($image_row = mysqli_fetch_assoc($query_additional_images)) {
    if (!empty($image_row['image_name'])) {
        $product_images[] = $image_row['image_name'];
    }
}


$product_images = array_slice($product_images, 0, 5);

$query_sold = mysqli_query($conn, "SELECT SUM(product_quantity) as total_sold FROM `customer_order_product` LEFT JOIN `customer_order` ON customer_order_product.order_id = customer_order.order_id WHERE product_id = $product_id AND order_status = 'Completed';");
foreach($query_sold as $sold_row){
    $total_sold = $sold_row['total_sold'];
}

$query_category = mysqli_query($conn, "SELECT category_name FROM `product_categories` LEFT JOIN `categories` ON product_categories.category_id = categories.category_id WHERE product_id = '$product_id' AND Visibility = 'Visible';");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link rel="stylesheet" href="product_information.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
      
    </style>
</head>
<body>
    
<nav class="breadcrumb">
    <a href="item_listing.php" class="breadcrumb-item">Product Listing</a>
    <span class="breadcrumb-separator">></span>
    <a href="#" class="breadcrumb-item">Product</a>
</nav>

<div class="whole">
    <div class="carousel">
        <?php if (empty($product_images)) { ?>
            <img src="uploads/no_img.png" id="mainImage" class="main-image">
        <?php } else { ?>
            <img src="<?php echo "uploads/products/" . $product_images[0]; ?>" id="mainImage" class="main-image">
            <div class="carousel-thumbnails">
                <?php foreach ($product_images as $index => $image) { ?>
                    <img src="<?php echo "uploads/products/" . $image; ?>" onclick="changeImage('<?php echo "uploads/products/" . $image; ?>')" class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>">
                <?php } ?>
            </div>
        <?php } ?>
    </div>

<!-- Modal Structure -->
<div id="myModal" class="modal">
  <span class="close">&times;</span>
  <img class="modal-content" id="img01">
  <div id="caption"></div>
</div>


    <div class="product-details">
        <h1><?php echo $product_name; ?></h1> 
        
        <p><?php
        if(mysqli_num_rows($query_category)>1){
            echo '∙ ';
            while($row_category = mysqli_fetch_assoc($query_category)){
                $category = $row_category['category_name'];
                echo '<a href=item_listing.php?search=&categoryitem%5B%5D='.$category.'>'.$category.'</a> ∙ ';
            }
            
        }
        
        ?></p>

        
        <h1><p> ₱<?php echo number_format($product_price, 2); ?></p></h1>
        
        <p><?php echo displayStars($rating); ?> (<?php echo $rating_count; ?>)</p>
        <hr>
        
        <?php if ($product_stock > 0) { ?>
            <form method="post" onsubmit="return checkCustomerID()">
                <div><strong>Quantity:</strong> <input type="number" name="quantity" max="<?php echo $product_stock; ?>" min="1" value="1"></div>
                <p><?php echo $product_stock; ?> left in stock</p>
                <p><?php echo $total_sold; ?> units sold</p><br>
                <div><input type="submit" value="Add to Cart" name="addtocart<?php echo $product_id; ?>"></div><br><hr>

                <?php
                if (isset($_POST['addtocart' . $product_id])) {
                    if (trim($customer_id) == '') {
                        echo "<script>alert('Please log in first.');</script>";
                    } else {
                        $quantity = $_POST['quantity'];
                        $check_cart_query = "SELECT * FROM cart WHERE customer_id = $customer_id AND product_id = $product_id";
                        $check_cart_result = mysqli_query($conn, $check_cart_query);

                        if (mysqli_num_rows($check_cart_result) > 0) {
                            
                            $update_cart_query = "UPDATE cart SET amount = amount + $quantity WHERE customer_id = $customer_id AND product_id = $product_id";
                            mysqli_query($conn, $update_cart_query);
                            echo "<script>alert('Added to cart successfully');</script>";
                        } else {
                            
                            $add_to_cart_query = "INSERT INTO cart (product_id, customer_id, amount, date_added) VALUES($product_id, $customer_id, $quantity, current_timestamp())";
                            mysqli_query($conn, $add_to_cart_query);
                            echo "<script>alert('Added to cart successfully');</script>";
                        }

                    }
                }
                ?>
            </form>
        <?php } else { ?>
            <p>Out of stock</p>
        <?php } ?>
        
    </div>
</div>

<div class="tabs">
    <button class="tablinks active" onclick="openTab(event, 'Description')">Description</button>
    <button class="tablinks" onclick="openTab(event, 'Reviews')">Reviews</button>
</div>

<div id="Description" class="tabcontent active">
    <h3>Description</h3>
    <p style="margin-left:30px"><?php echo nl2br(htmlspecialchars_decode(strip_tags($product_description))); ?></p>
</div>

<div id="Reviews" class="tabcontent">
    <h3>Reviews</h3>
        <div class="rating-tabs">
        <?php
        
        for ($rating = 1; $rating <= 5; $rating++) {
            $query_rating_count = mysqli_query($conn, "SELECT COUNT(*) as count FROM ratings WHERE product_id = '$product_id' AND rating = '$rating'");
            $rating_count_row = mysqli_fetch_assoc($query_rating_count);
            $review_count = $rating_count_row['count'];
            ?>
            <button class="tablinks" onclick="openRatingTab(event, 'Rating<?php echo $rating; ?>')"><?php echo $rating; ?> Star (<?php echo $review_count; ?>)</button>
            <?php
        }
        ?>

        <?php
        
        for ($rating = 1; $rating <= 5; $rating++) {
            $query_rating = mysqli_query($conn, "SELECT * FROM ratings WHERE product_id = '$product_id' AND rating = '$rating'");
            ?>
            <div id="Rating<?php echo $rating; ?>" class="tabcontent">
                <h3><?php echo $rating; ?> Star Ratings</h3>
                
                <?php
                foreach ($query_rating as $review_row) {
                    $user_name = $review_row['user_name'];
                    $individual_rating = $review_row['rating'];
                    $review = $review_row['review'];
                    echo '<div class="revcontentss">';
                    echo '<h3>' . $user_name . '</h3>';
                    echo displayStars($individual_rating) . '<br>';
                    echo '<p style="margin-left: 80px;">' . $review . '</p><br><hr>';
                    echo '</div>';
                }
                ?>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
    
    $reviews_per_page = 5;
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = ($page - 1) * $reviews_per_page;

    $query_review = mysqli_query($conn, "SELECT * FROM ratings WHERE product_id = '$product_id' LIMIT $offset, $reviews_per_page");

    foreach ($query_review as $review_row) {
        $user_name = $review_row['user_name'];
        $rating = $review_row['rating'];
        $review = $review_row['review'];
        ?>
        <div class="revcontents">
            <h3><?php echo $user_name; ?></h3>
            <?php echo displayStars($rating); ?><br>
            <p style="margin-left: 80px;"><?php echo $review; ?></p><br><hr>
        </div>
        <?php
    }

    
    $total_reviews = mysqli_num_rows(mysqli_query($conn, "SELECT * FROM ratings WHERE product_id = '$product_id'"));
    $total_pages = ceil($total_reviews / $reviews_per_page);

    echo '<div class="pagination">';
    for ($i = 1; $i <= $total_pages; $i++) {
        echo '<a href="?id=' . $product_id . '&page=' . $i . '" class="' . ($page == $i ? 'active' : '') . '">' . $i . '</a>';
    }
    echo '</div>';
    ?>


</div>

<script>
    function changeImage(imageSrc) {
        var mainImage = document.getElementById('mainImage');
        mainImage.src = imageSrc;
        var thumbnails = document.querySelectorAll('.carousel-thumbnails img');
        thumbnails.forEach(function(thumbnail) {
            thumbnail.classList.remove('active');
            if (thumbnail.src.includes(imageSrc)) {
                thumbnail.classList.add('active');
            }
        });
    }

    function checkCustomerID() {
        var customerID = "<?php echo isset($customer_id) ? $customer_id : ''; ?>";
        if (customerID === '') {
            alert('Please log in first.');
            return false;
        }
        return true;
    }

    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].classList.remove("active");
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].classList.remove("active");
        }
        document.getElementById(tabName).classList.add("active");
        evt.currentTarget.classList.add("active");
    }

    function openRatingTab(evt, tabName) {
    var i, tabcontent, tablinks;
    
    
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        if (tabcontent[i].id.startsWith("Rating")) {
            tabcontent[i].classList.remove("active");
        }
    }
    
    
    tablinks = document.querySelectorAll(".rating-tabs .tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].classList.remove("active");
    }
    
    
    document.getElementById(tabName).classList.add("active");
    evt.currentTarget.classList.add("active");
    
    
    var reviews = document.querySelectorAll("#Reviews .revcontents");
    reviews.forEach(function(review) {
        review.style.display = "none";
    });
}

</script>
<script>

var modal = document.getElementById("myModal");


var mainImage = document.getElementById("mainImage");
var modalImg = document.getElementById("img01");
var captionText = document.getElementById("caption");

mainImage.onclick = function() {
    modal.style.display = "block";
    modalImg.src = this.src;
    captionText.innerHTML = this.alt;
}

var span = document.getElementsByClassName("close")[0];

span.onclick = function() {
  modal.style.display = "none";
}


</script>
</body>
<?php
include 'footer.php';
?>
</html>
