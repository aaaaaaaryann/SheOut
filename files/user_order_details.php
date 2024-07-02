<?php
ob_start();
session_start();
$page = 'orders';
include("conn.php");
include "usertopbar.php";

$customer_id = $_SESSION['customer_id'];

if (empty($customer_id)) {
    header('Location: login.php');
    exit();
}

$order_id = $_REQUEST['id'];

$query_name = mysqli_query($conn, "SELECT user_name FROM customer WHERE customer_id = $customer_id");
$row = mysqli_fetch_assoc($query_name);
$user_name = $row['user_name'];

 
$query_order = mysqli_query($conn, "SELECT * FROM customer_order LEFT JOIN customer ON customer_order.customer_id = customer.customer_id WHERE customer_order.order_id = '$order_id' AND customer.customer_id = '$customer_id'");
$order = mysqli_fetch_assoc($query_order);

$query_cancel = mysqli_query($conn, "SELECT * from cancel_order where order_id = '$order_id'");
$cancel_row = mysqli_fetch_assoc($query_cancel);


if (!$order) {
    echo "Order not found or you do not have permission to view this order.";
    exit();
}

$order_date = $order['order_date'];
$order_amount = $order['total_amount'];
$order_address = $order['order_address'];
$order_name = $order['order_name'];
$order_contact = $order['order_contact'];
$order_shipping = $order['mode_of_delivery'];
$order_payment = $order['payment_method'];
$order_status = $order['order_status'];
$tracking_id = $order['tracking_id'];
$shipping_fee = $order['shipping_fee'];
$admin_confirm = $order['admin_confirmation'];
$customer_confirm = $order['customer_confirmation'];

function displayStars($rating) {
    $fullStars = floor($rating);  
    $halfStar = $rating - $fullStars >= 0.5 ? 1 : 0;  
    $emptyStars = 5 - $fullStars - $halfStar;  

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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="user_order_details.css">
    <title>Order Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
     
    </style>
    

</head>
<style>
    
</style>
<body>
<nav class="breadcrumb">
    <a href="userprofile.php" class="breadcrumb-item">View Orders</a>
    <span class="breadcrumb-separator">></span>
    <a href="#" class="breadcrumb-item">Order no.<?php echo $order_id; ?> </a>
</nav>
<div class="right">
    <div class="container">

        <h2>Item Details</h2>
        <h4><?php echo $order_date; ?></h4>
        <?php
            if (($order_status == 'Shipped' || $order_status == 'Ready to Pickup') && $customer_confirm == 'Pending') {
                ?>
                <form id="orderReceivedForm" method="post">
                    <input class="button" type="submit" value="Order Received" name="order_received" onclick="return confirm('Are you sure you received the order?');">
                </form>
                <?php
                if (isset($_POST['order_received'])) {
                    $update_confirm = mysqli_query($conn, "UPDATE customer_order SET customer_confirmation = 'Confirmed' WHERE order_id = '$order_id'");
                    header("Location: user_order_details.php?id=" . $order_id);
                }
            }

            if ($customer_confirm == 'Confirmed') {
                echo "<div style='color: green;'>You have already received the order.</div>";
            }
        ?>


        <div class="order-items">
            <?php
            $query_items = mysqli_query($conn, "SELECT * FROM customer_order_product LEFT JOIN product ON customer_order_product.product_id = product.product_id WHERE customer_order_product.order_id = '$order_id'");

            while ($item = mysqli_fetch_assoc($query_items)) {
                $product_id = $item['product_id'];
                $product_name = $item['product_name'];
                $product_image = $item['product_image'];
                $product_price = $item['product_price'];
                $product_quantity = $item['product_quantity'];

                $query_rating = mysqli_query($conn, "SELECT rating_id, user_name, rating, review, ratings.order_id, ratings.product_id, ratings.created_at FROM `ratings` LEFT JOIN customer_order on ratings.order_id = customer_order.order_id LEFT JOIn customer_order_product ON customer_order.order_id = customer_order_product.order_id WHERE ratings.order_id = '$order_id' AND ratings.product_id = '$product_id';");

                ?>
                <div class="item">
                    <?php if (empty($product_image)) { ?>
                        <img src="uploads/no_img.png" alt="No image available">
                    <?php } else { ?>
                        <img src="<?php echo "uploads/products/" . $product_image; ?>" alt="<?php echo $product_name; ?>">
                    <?php } ?>
                    <div class="contentright">
                        <div class="product-info">
                            <div class="field">
                                <span class="label">Product Name:</span>
                                <span class="value"><?php echo $product_name; ?></span>
                            </div>
                            <div class="field">
                                <span class="label">Quantity:</span>
                                <span class="value"><?php echo $product_quantity; ?></span>
                            </div>
                            <div class="field">
                                <span class="label">Amount:</span>
                                <span class="value">₱ <?php echo $product_quantity * $product_price; ?></span>
                            </div>

                            <?php
                                if(mysqli_num_rows($query_rating) < 1 && $order_status == 'Completed'){
                                    ?>
                                    <button class="button" onclick="document.getElementById('rate<?php echo $product_id; ?>').style.display = 'block'">Rate</button><br>

                                    <div class="rates" style="display:none" id="rate<?php echo $product_id; ?>">
                                        <h3>Rate</h3>
                                        <form method="post">
                                            <p><?php echo $product_name; ?></p>
                                            <div class="rating">
                                                <input type="radio" name="rating" id="star5<?php echo $product_id; ?>" value="5"><label for="star5<?php echo $product_id; ?>">&#9733;</label>
                                                <input type="radio" name="rating" id="star4<?php echo $product_id; ?>" value="4"><label for="star4<?php echo $product_id; ?>">&#9733;</label>
                                                <input type="radio" name="rating" id="star3<?php echo $product_id; ?>" value="3"><label for="star3<?php echo $product_id; ?>">&#9733;</label>
                                                <input type="radio" name="rating" id="star2<?php echo $product_id; ?>" value="2"><label for="star2<?php echo $product_id; ?>">&#9733;</label>
                                                <input type="radio" name="rating" id="star1<?php echo $product_id; ?>" value="1"><label for="star1<?php echo $product_id; ?>">&#9733;</label>
                                            </div>

                                            <div>
                                                <input type="checkbox" name="anonymous" id="anonymous<?php echo $product_id; ?>" value="1">
                                                <label for="anonymous<?php echo $product_id; ?>">Submit as anonymous</label>
                                            </div>
                                            <textarea name="review" placeholder="Leave a comment" maxlength="250" rows="4" cols="50"></textarea>
                                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                                            <input type="hidden" name="customer_id" value="<?php echo $customer_id; ?>"><br>
                                            <input type="submit" class="subrating" value="Submit Rating" name="rate<?php echo $product_id; ?>">
                                        </form>
                                    </div>
                                <?php
                                } else if(mysqli_num_rows($query_rating) > 0 && $order_status == 'Completed'){
                                    $rating_row = mysqli_fetch_assoc($query_rating);?><div class="revs"><?php
                                 echo "<div style='color:green'>You have rated this product already</div>";
                                    
                                 $donerate = $rating_row['rating'];
                                 ?><div ><?php
                                    echo displayStars($donerate);?></div> <?php
                                    echo $rating_row['review'];?></div> <?php
                                }
                                
                                if (isset($_POST['rate' . $product_id])) {
                                    $rating = $_POST['rating'];
                                    $review = $_POST['review'];
                                    $anonymous = isset($_POST['anonymous']) ? 1 : 0;
                                    $product_id = $_POST['product_id'];

                                    if($anonymous == 1){
                                        $insert_review = mysqli_query($conn, "INSERT INTO ratings (customer_id, product_id, order_id, rating, review, user_name) VALUES('$customer_id', '$product_id', '$order_id', '$rating', '$review', 'Anonymous')");
                                    }else{
                                        $insert_review = mysqli_query($conn, "INSERT INTO ratings (customer_id, product_id, order_id, rating, review, user_name) VALUES('$customer_id', '$product_id', '$order_id', '$rating', '$review', '$user_name')");
                                    }

                                    header("Location: user_order_details.php?id=" . $order_id);
                                    exit();
                                }
                            ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="field">
                <span class="label">Shipping Fee: </span>
                <span class="value">₱ <?php echo $shipping_fee; ?> </span>
            </div>

            <div class="field">
                <span class="label">Total: </span>
                <span class="value">₱ <?php echo $order_amount; ?> </span>
            </div>
        </div>

        <div class="order-info">
            <div class="usercontent">
                <h4>Customer</h4><p><?php echo $order_name; ?></p>
                <br>
                <h4>Contact Information</h4><p><?php echo $order_contact; ?></p><br>
                <h4>Shipping Address</h4><p><?php echo $order_address; ?></p><br>
                <h4>Shipping Option</h4><p><?php echo $order_shipping; ?></p><hr><br>
                <h4>Payment Method</h4><p><?php echo $order_payment; ?></p><br>
                <h4>Status</h4><p><?php echo $order_status; ?></p><br><hr>
                <?php
                if (!empty($tracking_id)) {
                    ?>
                    <h4>Tracking ID: </h4><p><?php echo $tracking_id; ?></p>
                    <?php
                }
                if($order_status == 'Cancelled'){

                    $cancelled_by = $cancel_row['cancelled_by'];
$cancellation_reason = $cancel_row['cancellation_reason'];
                    ?>
                <h4>Cancelled by</h4><p><?php echo $cancelled_by; ?></p><br>
                <h4>Reason</h4><p><?php echo $cancellation_reason; ?></p><br>
                    <?php
                }
                ?>

            </div>

            <?php
            if ($order_status == 'Pending') {
                ?>
                <button class="button" onclick="document.getElementById('cancelPopup').style.display = 'flex'">Cancel Order</button>
                <?php
            }
            ?>

            <div id="cancelPopup" class="popup-overlay">
                <div class="popup-content">
                    <button class="close-btn" onclick="document.getElementById('cancelPopup').style.display='none'">&times;</button>
                    <h2>Cancellation Reason</h2>
                    <form method="post">
                        <label>
                            <input type="radio" name="option" value="Order Mistake" onclick="toggleTextbox()"> Order Mistake
                        </label><br>
                        <label>
                            <input type="radio" name="option" value="Change of Mind" onclick="toggleTextbox()"> Change of Mind
                        </label><br>
                        <label>
                            <input type="radio" name="option" value="option3" id="radioWithTextbox" onclick="toggleTextbox()"> Others
                        </label><br>
                        <input type="text" id="textbox" name="other_reason" placeholder="Enter additional information">

                        <input type="submit" value="Cancel" name="cancel_order">
                    </form>

                    <?php
                    if (isset($_POST['cancel_order'])) {
                        $reason = $_POST['option'];
                        $other_reason = $_POST['other_reason'] ?? null;

                        if (!empty($other_reason)) {
                            $cancel_reason = $other_reason;
                        } else {
                            $cancel_reason = $reason;
                        }

                        $cancel_order = mysqli_query($conn, "INSERT INTO cancel_order (order_id, cancellation_reason, cancelled_by) VALUES('$order_id', '$cancel_reason', 'User')");
                        $update_status = mysqli_query($conn, "UPDATE customer_order SET order_status = 'Cancelled' WHERE order_id = '$order_id'");

                        if ($order_payment == 'Online') {
                            $update_payment = mysqli_query($conn, "UPDATE payments SET status = 'To refund' WHERE order_id = '$order_id'");
                        } else {
                            $update_payment = mysqli_query($conn, "UPDATE payments SET status = 'Cancelled' WHERE order_id = '$order_id'");
                        }

                        header("Location: user_order_details.php?id=" . $order_id);
                        exit();
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function toggleTextbox() {
        const textbox = document.getElementById('textbox');
        const radioWithTextbox = document.getElementById('radioWithTextbox');

        if (radioWithTextbox.checked) {
            textbox.style.display = 'block';
        } else {
            textbox.style.display = 'none';
        }
    }
</script>
<?php
include("footer.php");
?>

</body>
</html>
