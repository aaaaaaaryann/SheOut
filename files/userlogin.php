<?php
ob_start();
include "topbar.php";
session_start();
$page = 'home';
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
	<link rel="stylesheet" href="userlogin.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    
</head>

       </div>
<body>
	

<div style=" background-color: #FBF7D3;"><br><br><br>
    <div class="container d-absolute justify-content-left align-items-center min-vh-100 " >

        
       <div class="col-md-6 right-box">
          <div class="row align-items-center">
                <div class="header-text mb-4">
                     <h2>Login</h2>
                     
                </div>
                        
                <form method="POST">
                <div class="input-group mb-3 ">
                <input type="email" name="login_email" placeholder="Email" class="form-control form-control-lg bg-light fs-6 border rounded-5" value="<?php echo isset($_POST['login_email']) ? htmlspecialchars($_POST['login_email']) : ''; ?>">
                </div>
                <div class="input-group mb-1">
                <input type="password" name="login_password" placeholder="Password" class="form-control form-control-lg bg-light fs-6 border rounded-5" value="<?php echo isset($_POST['login_password']) ? htmlspecialchars($_POST['login_password']) : ''; ?>">
                </div><br>
                <p  style="color:red" id="loginerror"></p> 
            </div>
                
                <div class="input-group mb-3">
				<input class="button" type="submit" name="login" value="Login">
                </div><br>
                <div class="register">
			 Did you forget your password? <a href="forgetpassword.php" style="color:#933939; text-decoration:none;">Click here</a>
            </div>
              <hr>
              
			  <div class="register">
			  New customer? Sign up for an <a href="register.php" style="color:#933939; text-decoration:none;">account</a>
            </div>
         </div>
   
           
      
            </form>
        </div>
    </div>
 </div>


<?php

include("conn.php");


if(isset($_POST['login'])){
    $email = $_POST['login_email'];
    $password = $_POST['login_password'];

     
    if (empty($email) && empty($password)){
        ?>
        <script>
            document.getElementById('loginerror').textContent = 'Please enter your email and password';
        </script>
        <?php
    } else if (empty($password)){
        ?>
        <script>
            document.getElementById('loginerror').textContent = 'Please enter your password';
        </script>
        <?php 
    } else if (empty($email)){
        ?>
        <script>
            document.getElementById('loginerror').textContent = 'Please enter your email';
        </script>
        <?php 
    } else {
        

        
        $login_sql = mysqli_query($conn, "SELECT * FROM customer WHERE customer_email = '$email'");
        
        
        if(mysqli_num_rows($login_sql) > 0){
            $row = mysqli_fetch_assoc($login_sql);
            
            
            if(password_verify($password, $row['customer_password'])){
                if($row['account_status'] == 'Inactive'){
                    ?>
                    <script>
                        document.getElementById('loginerror').textContent = 'Your account has been deactivated';
                    </script>
                    <?php  
                } else {
                    
                    $_SESSION['customer_name'] = $row['customer_name'];
                    $_SESSION['customer_id'] = $row['customer_id'];
                    Header('Location: index.php');
                }
            } else {
                
                ?>
                <script>
                    document.getElementById('loginerror').textContent = 'Invalid credentials';
                </script>
                <?php
            }
        } else {
             
            ?>
            <script>
                document.getElementById('loginerror').textContent = 'User does not exist';
            </script>
            <?php
        }
    }
}
?>



</body>
<?php
include 'footer.php';
?>
</html>

