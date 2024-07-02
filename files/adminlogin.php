<?php
ob_start();
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
	<link rel="stylesheet" href="adminlogin.css">
</head>
<body>
<form method="POST" class="login">

    <h3>Admin Log In</h3>
    <input type="text" name="login_email" placeholder="Email">
    <br>
    <input type="password" name="login_password" placeholder="Password">
    <br>
    <p id="loginerror"></p>
    <input type="submit" name="login" value="Login">


</form>

<?php

include("conn.php");
session_start();

	if(isset($_POST['login'])){
		$email = $_POST['login_email'];
		$password = $_POST['login_password'];

		$login_sql = mysqli_query($conn, "SELECT * FROM admininfo WHERE admin_email = '$email'");
		$row = mysqli_fetch_assoc($login_sql);
		
		if(mysqli_num_rows($login_sql) < 1){?>
			<script>
				document.getElementById('loginerror').textContent = 'Email does not exist';
			</script>
			<?php
		}else{
			

			if(password_verify($password, $row['admin_password'])){
				
						$_SESSION['admin_name']=$row['admin_name'];
						$_SESSION['admin_id']=$row['adminID'];

						Header('Location: admin_dashboard.php');
			}else {
                ?>
                <script>
                    document.getElementById('loginerror').textContent = 'Invalid credentials';
                </script>
                <?php
            }
		}
	}

?>
</body>
</html>

