<?php
$page = 'orders';
ob_start();
session_start();

include("conn.php");
include "admin_topbar.php";

$admin_id = $_SESSION['admin_id'];

if ($admin_id == "") { 
    header('Location: adminlogin.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="admin_manage_orders.css">
  
</head>
<body>
<div class="right">
    <h3>MANAGE ORDERS</h3>
    <?php
    $query_count = mysqli_query($conn, "SELECT 
    SUM(CASE WHEN order_status = 'Pending' THEN 1 ELSE 0 END) AS pending_count, 
    SUM(CASE WHEN order_status = 'Completed' THEN 1 ELSE 0 END) AS completed_count, 
    SUM(CASE WHEN order_status = 'Shipped' THEN 1 ELSE 0 END) AS shipped_count, 
    SUM(CASE WHEN order_status = 'Ready to Pickup' THEN 1 ELSE 0 END) AS ready_to_pickup_count, 
    SUM(CASE WHEN order_status = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled_count, 
    SUM(CASE WHEN order_status = 'Accepted' THEN 1 ELSE 0 END) AS accepted_count 
    FROM customer_order");
    
    $row = mysqli_fetch_assoc($query_count);
    $pendingCount = $row['pending_count'];
    $completedCount = $row['completed_count'];
    $shippedCount = $row['shipped_count'];
    $readyToPickupCount = $row['ready_to_pickup_count'];
    $acceptedCount = $row['accepted_count'];
    $cancelledCount = $row['cancelled_count'];
    ?>

    <div class="tab">
        <button class="tablinks" onclick="openTab(event, 'Pending')" id="defaultOpen"><i class="fas fa-clock"></i> Pending(<?php echo $pendingCount ?>)</button>
        <button class="tablinks" onclick="openTab(event, 'Accepted')"><i class="fas fa-check"></i> Accepted(<?php echo $acceptedCount ?>)</button>
        <button class="tablinks" onclick="openTab(event, 'Shipped')"><i class="fas fa-truck"></i> Shipped(<?php echo $shippedCount ?>)</button>
        <button class="tablinks" onclick="openTab(event, 'ReadyToPickup')"><i class="fas fa-store"></i> Ready To Pickup(<?php echo $readyToPickupCount ?>)</button>
        <button class="tablinks" onclick="openTab(event, 'Completed')"><i class="fas fa-check-circle"></i> Completed(<?php echo $completedCount ?>)</button>
        <button class="tablinks" onclick="openTab(event, 'Cancelled')"><i class="fas fa-times-circle"></i> Cancelled(<?php echo $cancelledCount ?>)</button>
    </div>
   
    <?php
    function displayOrders($status) {
        global $conn; 
        $query_orderid = mysqli_query($conn, "SELECT DISTINCT customer_order.order_date, customer_order.order_id, customer_order.customer_id, customer_order.total_amount, customer_order.order_status, customer_order.mode_of_delivery, customer_order.payment_method, payments.payment_id 
                                              FROM customer_order 
                                              LEFT JOIN payments ON customer_order.order_id = payments.order_id 
                                              WHERE customer_order.order_status = '$status' 
                                              ORDER BY customer_order.order_date ASC");

        echo "<table id='$status-table' class='table table-striped table-bordered' style='width:100%'>
                <thead>
                    <tr>
                        <th>Order ID</th>
                       
                        <th>Products</th>
                        <th>Payment Method</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>";

        foreach($query_orderid as $order){
            $order_id = $order['order_id'];
            $total_amount = $order['total_amount'];
            $payment_id = $order['payment_id'];
            $payment_method = $order['payment_method'];
            $order_date = $order['order_date'];

            
            $query_product = mysqli_query($conn, "SELECT product.product_image, product.product_name, product.product_price, customer_order_product.product_quantity 
                                                  FROM customer_order_product 
                                                  LEFT JOIN product ON customer_order_product.product_id = product.product_id 
                                                  WHERE customer_order_product.order_id = '$order_id' 
                                                  ORDER BY customer_order_product.order_id ASC");

            echo "<tr onclick=\"window.location='admin_order_details.php?id=$order_id';\" style='cursor:pointer;'>
                    <td><a style='text-decoration:none; color:black;' href='admin_order_details.php?id=$order_id'>$order_id<br>$order_date</a></td>
                    
                    <td>";

            
            foreach ($query_product as $product) {
                $product_image = $product['product_image'];
                $product_name = $product['product_name'];
                $product_price = $product['product_price'];
                $product_quantity = $product['product_quantity'];

                
                echo "<div class='order-item'>
                        <a href='admin_order_details.php?id=$order_id'>
                            <img src='uploads/products/" . ($product_image == '' ? 'no_img.png' : $product_image) . "' class='product-image'>
                        </a>
                        <span>$product_name - ₱$product_price x $product_quantity</span>
                      </div>";
            }

            echo "</td>
                    <td>$payment_method</td>
                    <td>₱$total_amount</td>
                  </tr>";
        }

        echo "</tbody>
              </table>";
    }
    ?>

    <div class="items_info">
        <div id="Pending" class="tabcontent">
            <h3> Pending Orders</h3><hr>
            <?php displayOrders('Pending'); ?>
        </div>

        <div id="Accepted" class="tabcontent">
            <h3> Accepted Orders</h3><hr>
            <?php displayOrders('Accepted'); ?>
        </div>

        <div id="Shipped" class="tabcontent">
            <h3> Shipped Orders</h3><hr>
            <?php displayOrders('Shipped'); ?>
        </div>

        <div id="ReadyToPickup" class="tabcontent">
            <h3> Ready to Pickup Orders</h3><hr>
            <?php displayOrders('Ready to Pickup'); ?>
        </div>

        <div id="Completed" class="tabcontent">
            <h3> Completed Orders</h3><hr>
            <?php displayOrders('Completed'); ?>
        </div>

        <div id="Cancelled" class="tabcontent">
            <h3> Cancelled Orders</h3><hr>
            <?php displayOrders('Cancelled'); ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="admin_manage_orders.js"></script>

<script>
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";

        
        $('#' + tabName + '-table').DataTable();
    }

    document.getElementById("defaultOpen").click();
</script>
</body>
</html>
