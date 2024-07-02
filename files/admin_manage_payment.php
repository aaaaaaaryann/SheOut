<?php
ob_start();
session_start();
$page = 'payment';
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="admin_manage_payment.css">
    <title>Document</title>
</head>
<body>
<div class="right">
    <div class="tabs">
<h4>Pending Payments</h4>
    <table id="pendingOrdersTable" class="display">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Image</th>
                <th>Status</th>
            </tr>
        </thead>
        <?php
        $pending_payments = mysqli_query($conn, "SELECT customer_order.order_date, payments.proof_image, payments.payment_id, payments.order_id, payments.status, customer_order.total_amount, customer_order.payment_method FROM payments LEFT JOIN customer_order ON payments.order_id = customer_order.order_id ORDER BY customer_order.order_date DESC");

        foreach($pending_payments as $payments){
            $image = $payments['proof_image'];
            $payment_id = $payments['payment_id'];
            $order_id = $payments['order_id'];
            $amount = $payments['total_amount'];
            $method = $payments['payment_method'];
            $payment_status = $payments['status'];
            $order_date = $payments['order_date'];
            ?>
                <tr>
                    <td><a href="admin_order_details.php?id=<?php echo $order_id; ?>"><?php echo $order_id; ?></a></td>
                    <td><?php echo $order_date; ?></td>
                    <td><?php echo $amount; ?></td>
                    <td><?php echo $method; ?></td>
                    <td><?php if($image == 'N/A'){
                        echo "N/A";
                    }else{
                        ?>
                            <img src="<?php echo $image; ?>" style="height: 100px;">
                        <?php
                    }
                    ?></td>
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
                header("Location: admin_manage_payment.php");
            }
        }
        ?>
    </table>
</div>
</div>
</body>
<script>
$(document).ready(function() {
    $('#pendingPaymentsTable').DataTable();
    $('#pendingOrdersTable').DataTable();
    $('#lowStockTable').DataTable();
});
</script>
</html>