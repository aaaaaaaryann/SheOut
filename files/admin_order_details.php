<?php
ob_start();
session_start();
$page = 'orders';
include("conn.php");
include "admin_topbar.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$admin_id = $_SESSION['admin_id'];

if (empty($admin_id)) { 
    header('Location: adminlogin.php');
    exit();
}

$order_id = $_REQUEST['id'];

function adjustStocks($conn, $order_id, $adjustmentFactor) {
    $query_items = mysqli_query($conn, "SELECT product_id, product_quantity FROM customer_order_product WHERE order_id = '$order_id'");
    while ($item = mysqli_fetch_assoc($query_items)) {
        $product_id = $item['product_id'];
        $product_quantity = $item['product_quantity'];
        $adjust_stock_query = "UPDATE product SET product_stock = product_stock + ($adjustmentFactor * $product_quantity) WHERE product_id = '$product_id'";
        mysqli_query($conn, $adjust_stock_query);
    }
}


$query_order = mysqli_query($conn, "SELECT * FROM customer_order LEFT JOIN customer ON customer_order.customer_id = customer.customer_id WHERE customer_order.order_id = '$order_id'");
$order = mysqli_fetch_assoc($query_order);

$order_date = $order['order_date'];
$customer_id = $order['order_id'];
$order_amount = $order['total_amount'];
$order_address = $order['order_address'];
$order_name = $order['order_name'];
$order_contact = $order['order_contact'];
$order_shipping = $order['mode_of_delivery'];
$order_payment = $order['payment_method'];
$order_status = $order['order_status'];
$shipping_fee = $order['shipping_fee'];
$track_id = $order['tracking_id'];
$admin_confirm = $order['admin_confirmation'];
$customer_confirm = $order['customer_confirmation'];


$query_payment = mysqli_query($conn, "SELECT * FROM payments WHERE order_id = '$order_id'");
$payment = mysqli_fetch_assoc($query_payment);
$payment_image = $payment['proof_image'];


if (isset($_POST['status'])) {
    $status = $_POST['status'];

    
    $current_status_query = mysqli_query($conn, "SELECT order_status FROM customer_order WHERE order_id = '$order_id'");
    $current_status_result = mysqli_fetch_assoc($current_status_query);
    $current_status = $current_status_result['order_status'];

    $cancellation_reason = isset($_POST['cancellation_reason']) ? $_POST['cancellation_reason'] : '';
    $tracking_id = isset($_POST['tracking_id']) ? $_POST['tracking_id'] : '';

    
    if ($status == 'Accepted' && $current_status == 'Pending') {
        adjustStocks($conn, $order_id, -1);
        if ($order_payment == 'online') {
            $update_payment = mysqli_query($conn, "UPDATE payments SET status = 'Paid' WHERE order_id = '$order_id'");
        }
    } else if($status == 'Completed'){
        $update_payment = mysqli_query($conn, "UPDATE payments SET status = 'Paid' WHERE order_id = '$order_id'");
    }elseif ($status == 'Shipped' && $current_status == 'Accepted' && $order_shipping == 'Delivery') {
        $update_tracking_query = "UPDATE customer_order SET tracking_id = '$tracking_id' WHERE order_id = '$order_id'";
        mysqli_query($conn, $update_tracking_query);
    } elseif ($status == 'Cancelled' && $current_status == 'Accepted') {
        adjustStocks($conn, $order_id, 1);
        if ($order_payment == 'online') {
            $update_payment = mysqli_query($conn, "UPDATE payments SET status = 'To refund' WHERE order_id = '$order_id'");
        } else if ($order_payment == 'cod') {
            $update_payment = mysqli_query($conn, "UPDATE payments SET status = 'Cancelled' WHERE order_id = '$order_id'");
        }
    } elseif ($status == 'Cancelled' && $current_status == 'Pending') {
        adjustStocks($conn, $order_id, -1);
        if ($order_payment == 'online') {
            $update_payment = mysqli_query($conn, "UPDATE payments SET status = 'To refund' WHERE order_id = '$order_id'");
        } else if ($order_payment == 'cod') {
            $update_payment = mysqli_query($conn, "UPDATE payments SET status = 'Cancelled' WHERE order_id = '$order_id'");
        }
    }

    if ($status == 'Cancelled' && !empty($cancellation_reason)) {
        $insert_reason_query = "INSERT INTO cancel_order (order_id, cancellation_reason, cancelled_by) VALUES ('$order_id', '$cancellation_reason', 'Admin')";
        mysqli_query($conn, $insert_reason_query);
    }

    
    $update_status_query = "UPDATE customer_order SET order_status = '$status' WHERE order_id = '$order_id'";
    if (mysqli_query($conn, $update_status_query)) {
        
        $order_query = mysqli_query($conn, "SELECT customer.customer_email FROM customer_order LEFT JOIN customer ON customer_order.customer_id = customer.customer_id WHERE customer_order.order_id = '$order_id'");
        $order = mysqli_fetch_assoc($order_query);
        $customer_email = $order['customer_email'];
        
        
        $mail = new PHPMailer(true);
        try {
            
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = '//your email';
            $mail->Password   = '//your password';
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = 465;

            
            $mail->setFrom('//your email', 'Sheout');
            $mail->addAddress($customer_email);

            
            $mail->isHTML(true);
            $mail->Subject = 'Order Status Update';
            
            if (!empty($cancellation_reason) && $status == 'Cancelled') {
                $mail->Body = "Your order with ID $order_id has been cancelled. Reason: $cancellation_reason";
                $mail->AltBody = "Your order with ID $order_id has been cancelled. Reason: $cancellation_reason";
            } elseif ($status == 'Shipped' && !empty($tracking_id)) {
                $mail->Body = "Your order with ID $order_id has been shipped. Tracking ID: $tracking_id";
                $mail->AltBody = "Your order with ID $order_id has been shipped. Tracking ID: $tracking_id";
            } else {
                $mail->Body = "Your order status has been updated to: $status.";
                $mail->AltBody = "Your order status has been updated to: $status.";
            }

            $mail->send();
            echo 'Email notification sent.';
        } catch (Exception $e) {
            echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

        header("Location: admin_order_details.php?id=$order_id");
        exit();
    } else {
        echo "Failed to update order status.";
    }
}

if(isset($_POST['confirm_admin'])){
    $update_admin_confirm = mysqli_query($conn, "UPDATE customer_order SET admin_confirmation = 'Confirmed' WHERE order_id = '$order_id'");

    $query_confirm = mysqli_query($conn, "SELECT * FROM customer_order where order_id = '$order_id'");
    $confirm_row = mysqli_fetch_assoc($query_confirm);

    $admin_confirmation = $confirm_row['admin_confirmation'];
    $customer_confirmation = $confirm_row['customer_confirmation'];

    
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin_order_details.css">
    <title>Order Details</title>
    <script>
        function confirmAction(message) {
            return confirm(message);
        }

        function showCancellationModal() {
            document.getElementById('cancellationModal').style.display = 'block';
        }

        function hideCancellationModal() {
            document.getElementById('cancellationModal').style.display = 'none';
        }
    </script>
   
</head>
<body>
<nav class="breadcrumb">
    <a href="admin_manage_orders.php" class="breadcrumb-item">Manage Orders</a>
    <span class="breadcrumb-separator">></span>
    <a href="#" class="breadcrumb-item">Order no.<?php echo $order_id; ?></a>
</nav>
<div class="right">
    <div class="container">
        <h4><?php echo $order_date; ?></h4>
        <h4>Order Items</h4>
        <div class="order-items">
            <?php
            $query_items = mysqli_query($conn, "SELECT * FROM customer_order_product LEFT JOIN product ON customer_order_product.product_id = product.product_id WHERE customer_order_product.order_id = '$order_id'");
            while ($item = mysqli_fetch_assoc($query_items)) {
                $product_name = $item['product_name'];
                $product_image = $item['product_image'];
                $product_price = $item['product_price'];
                $product_quantity = $item['product_quantity'];
                $product_stock = $item['product_stock'];
                ?>
                <div class="item">
                    <?php if (empty($product_image)) { ?>
                        <img src="uploads/no_img.png">
                    <?php } else { ?>
                        <img src="<?php echo "uploads/products/" . $product_image; ?>">
                    <?php } ?>
                    <div class="contentright">
                        <table>
                            <tr>
                                <th>Product Name</th>
                                <th>Quantity</th>
                                <th>Stock</th>
                                <th>Sub Total</th>
                            </tr>
                            <tr>
                                <td><?php echo $product_name; ?></td>
                                <td><?php echo $product_quantity; ?></td>
                                <td><?php echo $product_stock; ?></td>
                                <td><?php echo $product_price * $product_quantity; ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <p></p>
            <?php } ?>
            <p>Shipping fee: <?php echo $shipping_fee; ?> </p>
            <p>Total Amount: <?php echo $order_amount; ?> </p>
        </div>
       
        <div class="order-info">
                <div class="mods">
                    <?php if ($order_payment != 'cod') { ?>
                        <h4>Proof of payment </h4>
                        <img id="myImg" src="<?php echo $payment_image; ?>" style="height:200px; cursor: pointer;">
                        
                     
                        <div id="myModal" class="modal">
                            <span class="close">&times;</span>
                            <img class="modal-content" id="img01">
                            <div id="caption"></div>
                        </div>
                    <?php } ?>
                </div>

            <div class="usercontent">
                <h4>Customer</h4><p><?php echo $order_name; ?></p><hr>
                <!--<p><strong>Order ID:</strong> <?php echo $order_id; ?></p>
                <p><strong>Total Amount:</strong> <?php echo $order_amount; ?></p>-->
                <br>
                <h4>Contact Information</h4><p> <?php echo $order_contact; ?></p><hr><br>
                <h4>Shipping Address</h4> <p><?php echo $order_address; ?></p><hr><br>
                <h4>Shipping Option: </h4><p><?php echo $order_shipping; ?></p>
                <h4>Payment Method:</h4> <p><?php echo $order_payment; ?></p>
                <h4>Status:</h4> <p><?php echo $order_status; ?></p>
                <?php
                if(!empty($track_id)){
                    ?>
                <h4>Tracking ID: </h4><p><?php echo $track_id; ?></p>
                    <?php
                }
                ?>
                <?php if($order_status == 'Cancelled'){
                    $query_cancel = mysqli_query($conn, "SELECT * FROM cancel_order where order_id = '$order_id'");
                    $cancel_row = mysqli_fetch_assoc($query_cancel);
                    $cancelled_by = $cancel_row['cancelled_by'];
                    $cancel_reason = $cancel_row['cancellation_reason'];
                    ?>
                    <h4>Cancelled By:</h4><p><?php echo $cancelled_by; ?></p>
                    <h4>Reason: </h4><p><?php echo $cancel_reason; ?></p>
                    <?php
                } ?>
                
            </div>
            <div class="actions">
                <form method="post">
                    <?php if ($order_status == 'Pending') { ?>
                        <button type="submit" name="status" value="Accepted"
                                onclick="return confirmAction('Are you sure you want to accept this order?')">Accept Order</button>
                        <button type="button" onclick="showCancellationModal()" class="cancel">Cancel Order</button>
                    <?php } elseif ($order_status == 'Accepted' && $order_shipping == 'Delivery') { ?>
                        <div><label for="tracking_id"><h4>Tracking ID:</h4></label>
                        <input type="text" id="tracking_id" name="tracking_id" required></div> <br>
                        <button type="submit" name="status" value="Shipped"
                                onclick="return confirmAction('Are you sure you want to mark this order as shipped?')">Mark as Shipped</button>
                        
                        <button type="button" onclick="showCancellationModal()" class="cancel">Cancel Order</button>
                    <?php } elseif ($order_status == 'Accepted' && $order_shipping == 'Pickup') { ?>
                        <button type="submit" name="status" value="Ready to Pickup"
                                onclick="return confirmAction('Are you sure you want to mark this order as ready for pickup?')">Mark as Ready to Pickup</button>
                       
                        <button type="button" onclick="showCancellationModal()" class="cancel">Cancel Order</button>
                    <?php } elseif (($order_status == 'Shipped' || $order_status == 'Ready to Pickup') && $admin_confirm == 'Confirmed' && $customer_confirm == 'Confirmed') { ?>
                        <button type="submit" name="status" value="Completed"
                                onclick="return confirm('Are you sure you want to mark this order as completed?')">Mark as Completed</button>
                    <?php } elseif (($order_status == 'Shipped' || $order_status == 'Ready to Pickup') && $admin_confirm == 'Pending') { ?>
                        <button type="submit" name="confirm_admin" value="Confirm"
                                onclick="return confirm('Are you sure you want to confirm this order?')">Confirm Order</button>
                    <?php } elseif (($order_status == 'Shipped' || $order_status == 'Ready to Pickup') && $admin_confirm == 'Confirmed') { ?>
                        <p>Waiting for customer confirmation</p>
                    <?php } elseif (($order_status == 'Shipped' || $order_status == 'Ready to Pickup') && $customer_confirm == 'Confirmed') { ?>
                        <p>Customer has already confirmed</p>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Cancellation Reason Modal -->
<div id="cancellationModal">
    <div id="cancellationModalContent">
        <span class="closes" onclick="hideCancellationModal()">&times;</span>
        <h2>Cancellation Reason</h2>
        <form method="post">
            <textarea class="texts" name="cancellation_reason" placeholder="Enter cancellation reason" required></textarea>
            <button type="submit" name="status" value="Cancelled" onclick="return confirmAction('Are you sure you want to cancel this order?')">Submit Cancellation</button>
        </form>
    </div>
</div>

<script>
var modal = document.getElementById("myModal");

var img = document.getElementById("myImg");
var modalImg = document.getElementById("img01");
var captionText = document.getElementById("caption");
img.onclick = function(){
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
</html>
