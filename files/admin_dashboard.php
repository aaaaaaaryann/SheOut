<?php
ob_start();
session_start();
$page = 'Dashboard';
include("conn.php");
include "admin_topbar.php";
$admin_id = $_SESSION['admin_id'];

if ($admin_id == "") { 
    header('Location: adminlogin.php');
    exit;
}

$current_month = date("m");
$current_monthname = date("F");
$current_year = date("Y");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin_dashboard.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <title>Admin Dashboard</title>
</head>
<body>
<div class="right">
    <?php
    $query_income = mysqli_query($conn, "SELECT SUM(total_amount) as income FROM payments LEFT JOIN customer_order ON payments.order_id = customer_order.order_id WHERE payments.status = 'paid' AND YEAR(order_date) = '$current_year' 
    AND MONTH(order_date) = '$current_month'");

    $query_pending = mysqli_query($conn, "SELECT COUNT(order_status) as total_pending FROM customer_order WHERE order_status = 'Pending' ");

    $query_completed = mysqli_query($conn, "SELECT COUNT(order_status) as total_completed FROM customer_order WHERE order_status = 'Completed' AND YEAR(order_date) = '$current_year' 
    AND MONTH(order_date) = '$current_month'");

    $row_income = mysqli_fetch_assoc($query_income);
    $total_income = $row_income['income'];
    
    $row_pending = mysqli_fetch_assoc($query_pending);
    $total_pending = $row_pending['total_pending'];

    $row_completed = mysqli_fetch_assoc($query_completed);
    $total_completed = $row_completed['total_completed'];
    ?>
    <h3>Admin Dashboard</h3>
    <div class="cardarea">
        
            <div class="card text-white bg-primary mb-3" style="max-width: 18rem;">
            
            <div class="card-body">
                <h5 class="card-title"><h4>Income of <?php echo $current_monthname; ?></h4></h5>
                <p class="card-text">₱<?php echo $total_income; ?></p>
            </div>
            <div class="card-footer bg-transparent "><a class="foot" href="admin_sales.php">View more</a></div>
            </div>
            <div class="card text-white bg-secondary mb-3" style="max-width: 18rem;">
            
            <div class="card-body">
                <h5 class="card-title"><h4>Pending Orders</h4></h5>
                <p class="card-text"><?php echo $total_pending; ?></p>
            </div>
            <div class="card-footer bg-transparent  "><a class="foot" href="admin_manage_orders.php">View more</a></div>
            </div>
            <div class="card text-white bg-success mb-3" style="max-width: 18rem;">
            
            <div class="card-body">
                <h5 class="card-title"><h4>Completed orders of <?php echo $current_monthname; ?></h4></h5>
                <p class="card-text"><?php echo $total_completed; ?></p>
            </div>
            <div class="card-footer bg-transparent"><a class="foot" href="admin_manage_orders.php#Completed">View more</a></div>
            </div>
    </div><div class="grids">
    <div class="column">
        <div class="pay">
            <h4>Pending Payments</h4>
            <table id="pendingPaymentsTable" class="display">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $pending_payments = mysqli_query($conn, "SELECT payments.payment_id, payments.order_id, payments.status, customer_order.total_amount, customer_order.payment_method, customer_order.order_date FROM payments LEFT JOIN customer_order ON payments.order_id = customer_order.order_id WHERE payments.status <> 'Paid' AND payments.status <> 'Refunded' AND customer_order.order_status <> 'Pending' AND payments.status <> 'Cancelled'");

                    foreach($pending_payments as $payments){
                        $payment_id = $payments['payment_id'];
                        $order_id = $payments['order_id'];
                        $amount = $payments['total_amount'];
                        $method = $payments['payment_method'];
                        $payment_status = $payments['status'];
                        $payment_date = $payments['order_date'];
                        ?>
                        <tr>
                            <td><a href="admin_order_details.php?id=<?php echo $order_id; ?>"><?php echo $order_id; ?></a></td>
                            <td><?php echo $payment_date; ?></td>
                            <td><?php echo $amount; ?></td>
                            <td><?php echo $method; ?></td>
                            <td>
                                <form method="POST" class="status-form" id="<?php echo $payment_id; ?>">
                                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                                    <select name="status" onchange="this.form.submit()">
                                        <option value="Pending" <?php echo $payment_status == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="Paid" <?php echo $payment_status == 'Paid' ? 'selected' : ''; ?>>Paid</option>
                                        <option value="to refund" <?php echo $payment_status == 'To refund' ? 'selected' : ''; ?>>To Refund</option>
                                        <option value="Refunded" <?php echo $payment_status == 'Refunded' ? 'selected' : ''; ?>>Refunded</option>
                                        <option value="Cancelled" <?php echo $payment_status == 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                        <?php
                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            $order_id = $_POST['order_id'];
                            $status = $_POST['status'];

                            $update_status = mysqli_query($conn, "UPDATE payments SET status ='$status' WHERE order_id = '$order_id'");
                            header("Location: admin_dashboard.php");
                        }
                    }
                    ?>
                </tbody>
            </table>
            <a href="admin_manage_payment.php">For more info</a>
        </div>
        <div class="pend">
            <h4>Pending Orders</h4>
            <table id="pendingOrdersTable" class="display">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Total Amount</th>
                        <th>Quantity</th>
                        <th>Delivery</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $pending_orders = mysqli_query($conn, "SELECT customer_order.order_id, customer_order.total_amount, customer_order.order_date, customer_order.mode_of_delivery, SUM(customer_order_product.product_quantity) as total_quantity FROM customer_order JOIN customer_order_product ON customer_order.order_id = customer_order_product.order_id WHERE customer_order.order_status = 'Pending' GROUP BY customer_order.order_id");

                    
                        while($order = mysqli_fetch_assoc($pending_orders)){
                            $order_date = $order['order_date'];
                            $order_id = $order['order_id'];
                            $total_amount = $order['total_amount'];
                            $quantity = $order['total_quantity'];
                            $delivery = $order['mode_of_delivery'];
                            ?>
                            <tr>
                                <td><a href="admin_order_details.php?id=<?php echo $order_id; ?>"><?php echo $order_id; ?></a></td>
                                <td><?php echo $order_date; ?></td>
                                <td><?php echo $total_amount; ?></td>
                                <td><?php echo $quantity; ?></td>
                                <td><?php echo $delivery; ?></td>
                            </tr>
                            <?php
                        }
                    
                    ?>
                </tbody>
            </table>
            <a href="admin_manage_orders.php">For more info</a>
        </div>
    </div>
    <div class="low">
        <h4>Items low on stock</h4>
        <table id="lowStockTable" class="display">
            <thead>
                <tr>
                    <th>Product ID</th>
                    <th>Product Name</th>
                    <th>Product Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $query_stock = mysqli_query($conn, "SELECT * FROM product WHERE product_visibility = 'Visible' AND product_stock < 10 ORDER BY product_stock ASC");
                    foreach($query_stock as $stock){
                        $product_id = $stock['product_id'];
                        $product_name = $stock['product_name'];
                        $product_stock = $stock['product_stock'];
                        ?>
                    <tr>
                        <td><a href="admin_manage_product_edit.php?id=<?php echo $product_id; ?>"><?php echo $product_id; ?></a></td>
                        <td><?php echo $product_name; ?></td>
                        <td><?php echo $product_stock; ?></td>
                    </tr>
                        <?php
                    }
                ?>
            </tbody>
        </table>
        <a href="admin_manage_product.php">For more info</a>
    </div>
</div>
</div>
<script>
$(document).ready(function() {
    $('#pendingPaymentsTable').DataTable();
    $('#pendingOrdersTable').DataTable();
    $('#lowStockTable').DataTable();
});
</script>
</body>
</html>
