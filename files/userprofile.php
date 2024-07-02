<?php
ob_start();
session_start();
$page = 'home';
include ("conn.php");
include "usertopbar.php";
$view = isset($_GET['view']) ? $_GET['view'] : 'Orders'; 

$customer_id = $_SESSION['customer_id'];

$query_profile = mysqli_query($conn, "SELECT * FROM customer WHERE customer_id = '$customer_id'");

$rows = mysqli_fetch_assoc($query_profile);

$customer_name = $rows['customer_name'];
$customer_address = $rows['customer_address'];
$customer_contact = $rows['customer_contact'];
$customer_email = $rows['customer_email'];
$customer_password = $rows['customer_password'];



?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link rel="stylesheet" href="userprofile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    .invalid {
        border-color: red;
    }
</style>
</head>
<body>
<script>
    function edit_profile() {
        document.getElementById('edit_profile_id').style.display = "none";
        document.getElementById('update_profile_id').style.display = "";
        document.querySelectorAll('.input').forEach(function(input) {
            input.removeAttribute('readonly');
            input.classList.remove('read-only');
            input.classList.add('editable');
        });
    }

    function validateForm() {
        let isValid = true;
        document.querySelectorAll('.input').forEach(function(input) {
            if (!input.value.trim()) {
                input.classList.add('invalid');
                isValid = false;
            } else {
                input.classList.remove('invalid');
            }
        });
        return isValid;
    }

    document.getElementById('profile_form').onsubmit = function(event) {
        if (!validateForm()) {
            alert('Please fill out all required fields.');
            event.preventDefault();
        }
    };
</script>

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

        document.getElementById("defaultOpen").click();
    </script>
<div class="side">
    <form method="get" action="">
    <nav class="navbar">
        <ul>
            <li><button type="submit" class="butt <?php echo $view == 'Orders' ? 'active' : ''; ?>" name="view" value="Orders"><p>Manage Profile</p></button> </li>
            <li><button type="submit" class="butt <?php echo $view == 'Profile' ? 'active' : ''; ?>" name="view" value="Profile"><p>Profile</p></button></li>
            <li><button type="submit" class="butt <?php echo $view == 'ChangePassword' ? 'active' : ''; ?>" name="view" value="ChangePassword"><o>Change Password</o></button></li>
        </ul>
        </nav>
</form>
</div>
<?php 
if($view == 'Orders'){
            
    ?>

    <div class="profiler" id="manageaccount" style="background-color: #FBF7D3;">
                <h1>Account Management</h1><br>
                <hr><br>
                    <div class="columns">
                        <div class="leftbox">
                            <table>
                                <tr>
                                    <th><h3>Personal Profile</h3></th>
                                    <td> | <a href="userprofile.php?view=Profile" style="text-decoration:none;color:#FA7B44">  Edit</a></td>
                                </tr>
                            </table>
                        
                            <table>
                                <tr>
                                    <th></th>
                                    <td><?php echo $customer_name; ?></td>
                                </tr>
                                
                                <tr>
                                    <th></th>
                                    <td><?php echo $customer_contact; ?></td>
                                </tr>
                                <tr>
                                    <th></th>
                                    <td><?php echo $customer_email; ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="rightbox">
                            <table>
                                <tr>
                                    <th><h3>Address</h3></th>
                                    <td> | <a href="userprofile.php?view=Profile" style="text-decoration:none;color:#FA7B44">  Edit</a></td>
                                </tr>
                            </table>
                                
                                   <p><?php echo $customer_address; ?></p>
                        </div>
                    </div>

                    <br>

                    <?php
    $query_count = mysqli_query($conn, "SELECT 
    SUM(CASE WHEN order_status = 'Pending' THEN 1 ELSE 0 END) AS pending_count, 
    SUM(CASE WHEN order_status = 'Completed' THEN 1 ELSE 0 END) AS completed_count, 
    SUM(CASE WHEN order_status = 'Shipped' THEN 1 ELSE 0 END) AS shipped_count, 
    SUM(CASE WHEN order_status = 'Ready to Pickup' THEN 1 ELSE 0 END) AS ready_to_pickup_count, 
    SUM(CASE WHEN order_status = 'Cancelled' THEN 1 ELSE 0 END) AS cancelled_count, 
    SUM(CASE WHEN order_status = 'Accepted' THEN 1 ELSE 0 END) AS accepted_count 
    FROM customer_order WHERE customer_id = '$customer_id'");
    
    foreach ($query_count as $row) {
        $pendingCount = $row['pending_count'];
        $completedCount = $row['completed_count'];
        $shippedCount = $row['shipped_count'];
        $readyToPickupCount = $row['ready_to_pickup_count'];
        $acceptedCount = $row['accepted_count'];
        $cancelledCount = $row['cancelled_count'];
    }
    ?>

    <div class="tab">
        <button class="tablinks" onclick="openTab(event, 'Pending')" id="defaultOpen"><i class="fas fa-clock"></i> Pending(<?php echo $pendingCount ?>)</button>
        <button class="tablinks" onclick="openTab(event, 'Accepted')"><i class="fas fa-check"></i> Accepted(<?php echo $acceptedCount ?>)</button>
        <button class="tablinks" onclick="openTab(event, 'Shipped')"><i class="fas fa-truck"></i> Shipped(<?php echo $shippedCount ?>)</button>
        <button class="tablinks" onclick="openTab(event, 'ReadyToPickup')"><i class="fas fa-store"></i> Ready To Pickup(<?php echo $readyToPickupCount ?>)</button>
        <button class="tablinks" onclick="openTab(event, 'Completed')"><i class="fas fa-check-circle"></i> Completed(<?php echo $completedCount ?>)</button>
        <button class="tablinks" onclick="openTab(event, 'Cancelled')"><i class="fas fa-times-circle"></i> Cancelled(<?php echo $cancelledCount ?>)</button>
    </div>
                    <div class="midbox">
    

   

    <?php
    function displayOrders($status) {
        global $conn, $customer_id; 
        $query_orderid = mysqli_query($conn, "SELECT DISTINCT order_id, total_amount, order_date FROM customer_order WHERE customer_order.order_status = '$status' AND customer_order.customer_id = '$customer_id' ORDER BY order_date DESC");
        foreach($query_orderid as $order){
            $order_id = $order['order_id'];
            $total_amount = $order['total_amount'];
            $order_date = $order['order_date'];
            echo '<p>'.$order_date.'<br>'.$order_id.'</p>';

            $query_product = mysqli_query($conn, "SELECT customer_order.order_id, customer_order.total_amount, product.product_image, product.product_name, product.product_price, customer_order_product.product_quantity FROM `customer_order` right join customer_order_product on customer_order.order_id = customer_order_product.order_id left join product on customer_order_product.product_id = product.product_id WHERE customer_order.order_status = '$status' AND customer_order.customer_id = '$customer_id' AND customer_order.order_id = '$order_id' ORDER BY order_date ASC");

            foreach ($query_product as $orders) {
                $order_id = $orders['order_id'];
                $product_image = $orders['product_image'];
                $product_name = $orders['product_name'];
                $product_price = $orders['product_price'];
                $product_quantity = $orders['product_quantity'];

                ?>
                
               <div class="order-item">
                <a style='text-decoration:none; color:black;' href='user_order_details.php?id=<?php echo $order_id ?>'>
                    <img src='uploads/products/<?php echo ($product_image == '' ? 'no_img.png' : $product_image); ?>' class='product-image'>
                    
                    <div class="product-details">
                        <table class='tabinfo'>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                
                            </tr>
                            <tr>
                                <td><strong><p class="product-name"><?php echo $product_name?></strong></p></td>
                                <td>₱ <?php echo $product_price?></td>
                                <td><strong>Quantity:</strong> <?php echo $product_quantity?> </td>
                                
                            </tr>
                        </table>

                       
                    </div>
                </a>
            </div>
    
                <?php
            }

            ?>
                <br><p style="text-align:end">Total Amount: ₱<?php echo $total_amount; ?></p><br></a><hr><br>
            <?php

        }
    }
    
    ?>

 
            <div class="items_info">
                <div id="Pending" class="tabcontent">
                    <h3> Pending Orders</h3><br>
                    <?php displayOrders('Pending'); ?>
                </div>

                <div id="Accepted" class="tabcontent">
                    <h3> Accepted</h3>
                    <?php displayOrders('Accepted'); ?>
                </div>

                <div id="Shipped" class="tabcontent">
                    <h3> Shipped</h3>
                    <?php displayOrders('Shipped'); ?>
                </div>

                <div id="ReadyToPickup" class="tabcontent">
                    <h3> PickUp Items</h3>
                    <?php displayOrders('Ready to Pickup'); ?>
                </div>

                <div id="Completed" class="tabcontent">
                    <h3> Complete Orders</h3>
                    <?php displayOrders('Completed'); ?>
                </div>

                <div id="Cancelled" class="tabcontent">
                    <h3> Cancelled Orders</h3>
                    <?php displayOrders('Cancelled'); ?>
                </div>

            </div>
        </div>

            </div>
    </div>
    
    <?php
}else if($view == 'Profile'){
    ?>
    <div class="profiler" id="account">
                <h2>My Profile</h2><br>
                <p>Manage and protect your account</p><br>
                <hr><br>
                
                <?php
                include("conn.php");

                $customer_id = $_SESSION['customer_id'];

                if($customer_id == ""){ 
                    echo '<meta http-equiv="refresh" content="0; url=\'userlogin.php\'"/>';
                }

              
                $query_profile = mysqli_query($conn, "SELECT * FROM customer WHERE customer_id = '$customer_id'");

                $rows = mysqli_fetch_assoc($query_profile);

                $customer_name = $rows['customer_name'];
                $user_name = $rows['user_name'];
                $customer_address = $rows['customer_address'];
                $customer_contact = $rows['customer_contact'];
                $customer_email = $rows['customer_email'];
                $customer_password = $rows['customer_password'];
                ?>
                <!-- User Info -->
                <form method="POST">
    <div class="columns">
        <div class="leftbox">
            <div class="column_profile">
                <div>
                    <h3>User info</h3>
                    <div class="form-container">
                        <h5>Full Name: </h5>
                        <input class="input" type="text" name="customer_name" id="c_name" value="<?php echo $customer_name; ?>" maxlength="35" readonly>
                    </div><br>
                    <div class="form-container">
                        <h5>User Name: </h5>
                        <input class="input" type="text" name="user_name" id="u_name" value="<?php echo $user_name; ?>" maxlength="25" readonly>
                    </div><br>
                    <div class="form-container">
                        <h5>Address </h5>
                        <input class="input" type="text" name="customer_address" id="c_address" value="<?php echo $customer_address; ?>" maxlength="50" readonly>
                    </div><br>
                    <div class="form-container">
                        <h5>Contact Number: </h5>
                        <input class="input" type="text" name="customer_contact" id="c_contact" value="<?php echo $customer_contact; ?>" maxlength="11" pattern="09[0-9]{9}" readonly>
                    </div><br>
                    <div class="form-container">
                        <h5>Email: </h5>
                        <input class="input" type="email" name="customer_email" id="c_email" value="<?php echo $customer_email; ?>" maxlength="150" readonly>
                    </div><br>
                    <input type="button" class="button" value="Edit" id="edit_profile_id" onclick="edit_profile();">
                    <input type="submit" class="button" value="Update" id="update_profile_id" style="display:none;" name="update_profile_name" onclick="update_profile(event);">
                </div>
            </div>
        </div>
    </div>

    <?php
    if(isset($_POST['update_profile_name'])) {
 // Assuming you store customer_id in session
    $new_user = $_POST['user_name'];
    $new_name = $_POST['customer_name'];
    $new_address = $_POST['customer_address'];
    $new_contact = $_POST['customer_contact'];
    $new_email = $_POST['customer_email'];

    // Check if the new email already exists in the database
    $check_email_query = mysqli_query($conn, "SELECT customer_id FROM customer WHERE customer_email = '$new_email' AND customer_id != '$customer_id'");
    if(mysqli_num_rows($check_email_query) > 0) {
        echo '<script>alert("The email address already exists. Please use a different email."); window.location.href="userprofile.php?view=Profile";</script>';
    } else {
        $query_update_profile = mysqli_query($conn, "UPDATE customer SET user_name = '$new_user', customer_name = '$new_name', customer_address = '$new_address', customer_contact = '$new_contact', customer_email = '$new_email' WHERE customer_id = '$customer_id'");
        if($query_update_profile) {
            echo '<script>alert("Profile updated successfully!"); window.location.href="userprofile.php?view=Profile";</script>';
        } else {
            echo '<script>alert("There was an error updating the profile. Please try again."); window.location.href="userprofile.php?view=Profile";</script>';
        }
    }
}
?>
</form>
            </div>
    <?php
}else if($view == 'ChangePassword'){
    ?>
    <div class="profiler" id="changepassword">
                <h2>Change Password</h2><br>
                <hr><br>
                
                <p id="passworderror"></p>  
                <form method="post" action="userprofile.php?view=ChangePassword">

                        <div class="updatepass">
                            <input class="inputs" type="password" name="current_password" placeholder="Current Password"><br>
                        </div><br>
                        <div class="updatepass">
                            <input class="inputs" type="password" name="new_password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" placeholder="New Password" required>
                        </div><br>
                        <div class="updatepass">
                            <input class="inputs" type="password" name="confirm_new_password" placeholder="Confirm Password">
                        </div><br>
                        <div><p id="error_message"></p></div>
                        <div class="updatepass">
                            <input class="button" type="submit" value="Update" name="change_password">
                        </div>
                            <p>Password should be at least 8 characters, contain an uppercase & lowercase letter, and a digit</p>
                       
                    <?php
                    if(isset($_POST['change_password'])){
                        $current_password = $_POST['current_password'];
                        $new_password = $_POST['new_password'];
                        $confirm_new_password = $_POST['confirm_new_password'];

                        if(password_verify($current_password, $customer_password)){
                            if($new_password == $confirm_new_password){
                                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                                $changepassword = mysqli_query($conn, "UPDATE customer SET customer_password = '$hashed_password' WHERE customer_id = '$customer_id'");
                            } else if ($current_password == ''){
                                ?>
                                <script>
                                    document.getElementById('error_message').innerHTML = 'Please enter your current password';
                                </script>
                                <?php
                            }else {
                                ?>
                                <script>
                                    document.getElementById('error_message').innerHTML = 'Passwords do not match';
                                </script>
                                <?php
                            }

                            if ($changepassword) {
                                ?>
                                <script>
                                    document.getElementById('error_message').innerHTML = 'Password changed successfully';
                                </script>
                                <?php
                            } else {
                                ?>
                                <script>
                                    document.getElementById('error_message').innerHTML = 'Password change failed. Please try again';
                                </script>
                                <?php 
                            }

                        }else{
                            ?>
                                <script>
                                    document.getElementById('error_message').innerHTML = 'Current password is incorrect';
                                </script>
                                <?php
                        }

                        
                    }
                    ?>
                </form>
            </div>
    <?php
}

?>

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

        document.getElementById("defaultOpen").click();
    </script>
    <?php
    include 'footer.php';
    ?>
</body>