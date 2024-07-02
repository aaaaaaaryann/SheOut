<?php
ob_start();
session_start();
$page = 'user';
include("conn.php");
include "admin_topbar.php";
$admin_id = $_SESSION['admin_id'];
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';

if ($admin_id == "") { 
    header('Location: adminlogin.php');
    exit;
}


$query_active_users = mysqli_query($conn, "SELECT * FROM customer WHERE account_status = 'Active'");
$query_inactive_users = mysqli_query($conn, "SELECT * FROM customer WHERE account_status = 'Inactive'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="admin_manage_user.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
  
  
</head>
<body>
<div class="right">
    <h1>User Accounts</h1><br>
    <div class="tab">
        <button class="tablinks" onclick="openTab(event, 'Active')">Active Accounts</button>
        <button class="tablinks" onclick="openTab(event, 'Inactive')">Inactive Accounts</button>
    </div>

    <div id="Active" class="tabcontent">
        <h3>Active Accounts</h3>
        <table>
            <thead>
                <tr>
                    <th>Customer ID</th>
                    <th>Customer Name</th>
                    <th>Contact Number</th>
                    <th>Email</th>
                    <th>Orders</th>
                    <th>Action</th>
                    
                </tr>
            </thead>
            <?php
                foreach($query_active_users as $row){
                    $customer_id = $row['customer_id'];
                    $customer_name = $row['customer_name'];
                    $email = $row['customer_email'];
                    $contact = $row['customer_contact'];
                    $account_status = $row['account_status'];

                    $query_order = mysqli_query($conn, "SELECT * FROM customer_order WHERE customer_id = '$customer_id' ORDER BY order_date DESC");

                    ?>

                    <tr>
                        <td><?php echo $customer_id; ?></td>
                        <td><?php echo $customer_name; ?></td>
                        <td><?php echo $contact; ?></td>
                        <td><?php echo $email; ?></td>
                        <td> <button class="button"onclick="togglePopup('popup<?php echo $customer_id; ?>')">View Orders</button></td>
                        <td><form id="pops" method="post">
                            <input type="text" name="ban_reason" placeholder="Reason for deactivation" required>
                            <input type="submit" value="Deactivate" name="deactivate<?php echo $customer_id; ?>">

                            <?php
                            if(isset($_POST['deactivate'.$customer_id])){
                                $ban_reason = $_POST['ban_reason'];
                                $deactivate = mysqli_query($conn, "UPDATE customer SET account_status = 'Inactive' WHERE customer_id = $customer_id"); 

                                $mail = new PHPMailer(true);
                                try {
                                    // Server settings
                                    $mail->SMTPDebug = 0;
                                    $mail->isSMTP();
                                    $mail->Host       = 'smtp.gmail.com';
                                    $mail->SMTPAuth   = true;
                                    $mail->Username   = 'ngyawngyawngyawngyaw@gmail.com';
                                    $mail->Password   = 'uthluhccjammaaax';
                                    $mail->SMTPSecure = 'ssl';
                                    $mail->Port       = 465;

                                    // Recipients
                                    $mail->setFrom('ngyawngyawngyawngyaw@gmail.com', 'Sheout');
                                    $mail->addAddress($email);

                                    // Content
                                    $mail->isHTML(true);
                                    $mail->Subject = 'Account Deactivation Notice';
                                    $mail->Body = "Your account has been deactivated.<br>Reason: $ban_reason. You may send an email to sheout.support@gmail.com";
                                    $mail->AltBody = "Your account has been deactivated. Reason: $ban_reason. You may send an email to sheout.support@gmail.com";

                                    $mail->send();

                                    header("Location: admin_manage_user.php");
                                } catch (Exception $e) {
                                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                                }
                            }
                            ?>
                        </form></td>
                    </tr>
                   

                    <tr>
                        <td colspan="5">
                        <!-- E2 IPPOP UP MOOOOO-->
                            <div id="popup<?php echo $customer_id; ?>" class="modal">
                                <div class="popins">
                                    <span class="close" onclick="togglePopup('popup<?php echo $customer_id; ?>')">&times;</span>
                                    <table id="pendingOrdersTable" class="display">
                                        <thead>
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Order Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($query_order as $order): ?>
                                                <tr>
                                                    <td><a href="admin_order_details.php?id=<?php echo $order['order_id']; ?>"><?php echo $order['order_id']; ?></a></td>
                                                    <td><?php echo $order['order_date']; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- gang d222 -->
                        </td>
                    </tr>
                    <?php
                }
            ?>
        </table>
    </div>

    <div id="Inactive" class="tabcontent">
        <h3>Inactive Accounts</h3>
        <table >
            <thead>
                <tr>
                    <th>Customer ID</th>
                    <th>Customer Name</th>
                    <th>Contact Number</th>
                    <th>Email</th>
                    <th>Orders</th>
                    <th>Action</th>
                </tr>
            </thead>
            <?php
                foreach($query_inactive_users as $row){
                    $customer_id = $row['customer_id'];
                    $customer_name = $row['customer_name'];
                    $email = $row['customer_email'];
                    $contact = $row['customer_contact'];
                    $account_status = $row['account_status'];

                    $query_order = mysqli_query($conn, "SELECT * FROM customer_order WHERE customer_id = '$customer_id' ORDER BY order_date DESC");

                    ?>

                    <tr>
                        <td><?php echo $customer_id; ?></td>
                        <td><?php echo $customer_name; ?></td>
                        <td><?php echo $contact; ?></td>
                        <td><?php echo $email; ?></td>
                        <td> <button class="button"onclick="togglePopup('popup<?php echo $customer_id; ?>')">View Orders</button></td>
                        <td><form method="post">
                            <input type="submit" value="Reactivate" name="reactivate<?php echo $customer_id; ?>">
                            <?php
                            if(isset($_POST['reactivate'.$customer_id])){
                                $activate = mysqli_query($conn, "UPDATE customer SET account_status = 'Active' WHERE customer_id = '$customer_id'"); 

                                $mail = new PHPMailer(true);
                                try {
                                    // Server settings
                                    $mail->SMTPDebug = 0;
                                    $mail->isSMTP();
                                    $mail->Host       = 'smtp.gmail.com';
                                    $mail->SMTPAuth   = true;
                                    $mail->Username   = 'ngyawngyawngyawngyaw@gmail.com';
                                    $mail->Password   = 'uthluhccjammaaax';
                                    $mail->SMTPSecure = 'ssl';
                                    $mail->Port       = 465;

                                    // Recipients
                                    $mail->setFrom('ngyawngyawngyawngyaw@gmail.com', 'Sheout');
                                    $mail->addAddress($email);

                                    // Content
                                    $mail->isHTML(true);
                                    $mail->Subject = 'Account Reactivation Notice';
                                    $mail->Body = "Your account has been reactivated.";
                                    $mail->AltBody = "Your account has been reactivated.";

                                    $mail->send();

                                    header("Location: admin_manage_user.php");
                                } catch (Exception $e) {
                                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                                }
                            }
                            ?>
                        </form></td>
                    </tr>
                    
                    <tr>
                        <td colspan="5">
                            <!-- E2 IPPOP UP MOOOOO-->
                            <div id="popup<?php echo $customer_id; ?>" class="modal">
                                <div class="popins">
                                    <span class="close" onclick="togglePopup('popup<?php echo $customer_id; ?>')">&times;</span>
                                    <table id="pendingPaymentsTable" class="display">
                                        <thead>
                                            <tr>
                                                <th>Order ID</th>
                                                <th>Order Date</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($query_order as $order): ?>
                                                <tr>
                                                    <td><a href="admin_order_details.php?id=<?php echo $order['order_id']; ?>"><?php echo $order['order_id']; ?></a></td>
                                                    <td><?php echo $order['order_date']; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- GANG D2-->
                        </td>
                    </tr>
                    <?php
                }
            ?>
        </table>
    </div>
</div>
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
        }
        function togglePopup(popupId) {
    var modal = document.getElementById(popupId);
    if (modal.style.display === "block") {
        modal.style.display = "none";
    } else {
        modal.style.display = "block";
    }
}

        
        document.getElementsByClassName('tablinks')[0].click();
    </script>
    <script>
$(document).ready(function() {
    $('#pendingPaymentsTable').DataTable();
    $('#pendingOrdersTable').DataTable();
    $('#lowStockTable').DataTable();
});
</script>
</body>
</html>
