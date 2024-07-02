<?php
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
    <title>Admin Profile</title>
    <link rel="stylesheet" href="admin_profile.css">
</head>
<body>
<div class="right">
    <?php
        $query_profile = mysqli_query($conn, "SELECT * FROM admininfo WHERE adminID = $admin_id");
        $row = mysqli_fetch_assoc($query_profile);
        $admin_name = htmlspecialchars($row['admin_name']);
        $admin_email = htmlspecialchars($row['admin_email']);
        $admin_password = $row['admin_password'];
    ?>
    <div class="card">
        <div class="card-header">
            <h3>Admin Profile</h3>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label for="admin_Name">Name</label>
                    <input type="text" name="adminName" id="admin_Name" value="<?php echo $admin_name; ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="admin_Email">Email</label>
                    <input type="text" name="adminEmail" id="admin_Email" value="<?php echo $admin_email; ?>" readonly>
                </div>
                <button type="button" class="btn btn-primary" id="edit_profile_id" onclick="edit_profile();">Edit</button>
                <button type="submit" class="btn btn-success" id="update_profile_id" style="display:none;" name="update_profile_name">Update</button>
            </form>
        </div>
    </div>
    
    <div class="card mt-4">
        <div class="card-header">
            <h3>Change Password</h3>
        </div>
        <div class="card-body">
            <form method="post">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" name="current_password" id="current_password" placeholder="Current Password" required>
                </div>
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" name="new_password" id="new_password" placeholder="New Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm New Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
                </div>
                <button type="submit" class="btn btn-success" name="change_password">Update</button>
                <small class="form-text">Password should be at least 8 characters, and contain an uppercase & lowercase letter, and a digit.</small>
                <p id="error_message" class="error-message"></p>
                <p id="success_message" class="success-message"></p>
            </form>
        </div>
    </div>
</div>
    <?php
        if(isset($_POST['update_profile_name'])){
            $new_name = $_POST['adminName'];
            $new_email = $_POST['adminEmail'];

            $update_profile = mysqli_query($conn, "UPDATE admininfo SET admin_name = '$new_name', admin_email = '$new_email' WHERE adminID = '$admin_id'");
            header("Location: admin_profile.php");
        }

        if(isset($_POST['change_password'])){
            $current_pass = $_POST['current_password'];
            $confirm_pass = $_POST['confirm_password'];
            $new_password = $_POST['new_password'];

            if(password_verify($current_pass, $admin_password)){
                if($new_password == $confirm_pass){
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $changepassword = mysqli_query($conn, "UPDATE admininfo SET admin_password = '$hashed_password' WHERE adminID = '$admin_id'");
                } else {
                    ?>
                    <script>
                        document.getElementById('error_message').innerHTML = 'Passwords do not match';
                    </script>
                    <?php
                }

                if ($changepassword) {
                    ?>
                    <script>
                        document.getElementById('success_message').innerHTML = 'Password changed successfully';
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

<script>
    function edit_profile() {
        document.getElementById('edit_profile_id').style.display = "none";
        document.getElementById('update_profile_id').style.display = "block";
        document.getElementById('admin_Name').removeAttribute('readonly');
        document.getElementById('admin_Email').removeAttribute('readonly');
    }
</script>
</body>
</html>
