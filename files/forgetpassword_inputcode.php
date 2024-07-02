<?php 
session_start();
include("conn.php");
include "topbar.php";
$email = $_SESSION['email'];

if($email == ''){
    header("Location: userlogin.php");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="forgetpassword.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    
    <title>Document</title>
</head>
<body>
    
<?php

if(isset($_POST['submit'])){
    $reset_code = $_POST['code'];
    $query_token = mysqli_query($conn, "SELECT * FROM customer WHERE customer_email = '$email'");
    $row = mysqli_fetch_assoc($query_token);
    $token = $row['token'];

    if($token == $reset_code){
        
        $_SESSION['customer_id'] = $row['customer_id'];
        header("Location: forgetpassword_changepassword.php");
    } else {
        ?>
        <script>
            document.getElementById('code_error_text').textContent = "incorrect password reset code";
        </script>
        <?php
    }
}

?>
<div style=" background-color: #FBF7D3;"><br><br><br>
    <div class="container d-absolute justify-content-left align-items-center min-vh-100 " >
        <div class="col-md-6 right-box">
                <div class="row align-items-center">
                        <div class="header-text mb-4">
                            <h2>Enter your 4 digit password reset code sent in your email</h2>
                </div>
       
    <form method="post">
    <div class="input-group mb-3 ">
        <input type="number" name="code" class="form-control form-control-lg bg-light fs-6 border rounded-5" max="9999" placeholder="Numbers only"> 
        </div>
        <div class="input-group mb-1 ">
        <input type="submit" name="submit" value="submit" class="button">
        </div>
        <p style="color:red"id='error_message'></p>
    </form>
    </div>
    </div>
    <div class="left-box">
        <img src="uploads/pass.png" alt="">
    </div>
 </div>

</body>
<?php
include 'footer.php';
?>
</html>
