<?php   
include "topbar.php";
session_start();
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="register.css">
</head>
<body>
    <?php

        include("conn.php");

    ?>

<div style=" background-color: #FBF7D3;"><br><br><br>
    <div class="container d-absolute justify-content-left align-items-center min-vh-100 " >

        
       <div class="col-md-6 right-box">
          <div class="row align-items-center">
                <div class="header-text mb-4">
                     <h2>Create a Account</h2>
                     
                            </div>
                                <form method="POST">
                                    
                                <div class="input-group mb-3 ">
                                    <input type="text" name="user_name" placeholder="Name" required class="form-control form-control-lg bg-light fs-6 border rounded-5">
                                </div>  
                                <div class="input-group mb-3 ">
                                    <input type="text" name="username" placeholder="Username" required class="form-control form-control-lg bg-light fs-6 border rounded-5">
                                </div>  
                                <div class="input-group mb-3 ">   
                                    <input type="email" name="register_email" placeholder="Email" required class="form-control form-control-lg bg-light fs-6 border rounded-5">
                                </div>
                               
                                <div class="input-group mb-3 ">
                                    <input type="password" name="register_password" placeholder="Password" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required class="form-control form-control-lg bg-light fs-6 border rounded-5">
                                </div>
                                <div class="input-group mb-3 ">
                                    <input type="password" name="register_confirm_password" placeholder="Confirm Password" required class="form-control form-control-lg bg-light fs-6 border rounded-5">
                                </div>
                                <div class="input-group mb-3 " id="question">
                                    <p sytle="font-size:5px">Password should be Atleast 8 characters *Has an uppercase Lowercase letter, and a digit</p>
                                </div>
                                    <p id="registererror"></p>
                                <div class="input-group mb-3">
                                    <input type="submit" name="register" value="Register" class="button">
                                </div><hr>
                                <div class="register" >
                                    Signed up already? Click here to <a href="userlogin.php"  style="color:#933939; text-decoration:none;">login</a>
                                </div>
                                </form>
                                </div>
            </div>
    </div>
</div>
    <?php

if(isset($_POST['register'])){
    if($_POST['register_password']==$_POST['register_confirm_password']){
        $name = $_POST['user_name'];
        $username = $_POST['username'];
        $email = $_POST['register_email'];
        $password = $_POST['register_password'];

    $check_duplicate_email = mysqli_query($conn, "SELECT * FROM customer WHERE customer_email = '$email'");
    if (mysqli_num_rows($check_duplicate_email) >0 ) {
        ?>
       <script>
				document.getElementById('registererror').innerHTML = 'Account with that email already exists. <a href="userlogin.php">Redirect to Login?</a>';
			</script>
        <?php
    } else {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $hashedPassword = mysqli_real_escape_string($conn, $hashedPassword);
        $register ="INSERT INTO customer (customer_name, user_name, customer_email, customer_password) VALUES ('$name', '$username', '$email', '$hashedPassword')";
        mysqli_query($conn, $register);
        ?>
        <script>
                 document.getElementById('registererror').innerHTML = 'Registered succesfully!. <a href="userlogin.php">Login here</a>';
             </script>
         <?php

    }

        

    }else{
        ?>
        <script>
                 document.getElementById('registererror').innerHTML = 'Passwords do not match';
             </script>
         <?php
    }
    

} 

    ?>
</body>
<?php
include 'footer.php';
?>
</html>