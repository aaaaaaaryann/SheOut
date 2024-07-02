<?php
session_start();
include("conn.php");

if (isset($_POST['cancel_order'])) {
    $order_id = $_POST['order_id'];
    $cancellation_reason = mysqli_real_escape_string($conn, $_POST['cancellation_reason']);
    $customer_email = $_POST['customer_email'];

    
    $query_update = mysqli_query($conn, "UPDATE customer_order SET order_status = 'Cancelled' WHERE order_id = '$order_id'");

    if ($query_update) {
        
        $query_insert = mysqli_query($conn, "INSERT INTO cancel_order (order_id, cancellation_reason) VALUES ('$order_id', '$cancellation_reason')");

        if ($query_insert) {
            
            $to = $customer_email;
            $subject = "Order Cancellation Notification";
            $message = "Your order with ID $order_id has been cancelled for the following reason:\n\n$cancellation_reason";
            $headers = "From: no-reply@yourdomain.com";

            if (mail($to, $subject, $message, $headers)) {
                $_SESSION['message'] = "Order $order_id has been cancelled successfully and the customer has been notified.";
            } else {
                $_SESSION['error'] = "Order $order_id has been cancelled, but the email notification failed.";
            }
        } else {
            $_SESSION['error'] = "Failed to log the cancellation reason. Please try again.";
        }
    } else {
        $_SESSION['error'] = "Failed to cancel the order. Please try again.";
    }

    header("Location: admin_manage_orders.php");
    exit();
}
?>
