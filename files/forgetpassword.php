<?php
session_start();
include ('conn.php');
include "topbar.php";
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
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
            $email = $_POST['email'];

            $query_email = mysqli_query($conn, "SELECT * FROM customer WHERE customer_email = '$email'");

            if(mysqli_num_rows($query_email) < 1){
                ?>
                <script>
                    document.getElementById('error_message').textContent = "This email does not exist in our database"
                </script>
                <?php
            } else {
                $token = rand(1000, 9999);
                $insert_token = mysqli_query($conn, "UPDATE customer SET token = '$token' WHERE customer_email = '$email'");

                $mail = new PHPMailer(true);

                $mail->isSMTP();
					$mail->Host = 'smtp.gmail.com';
					$mail->SMTPAuth = true;
					$mail->Username = '//your email';
					$mail->Password = '//your passowrd';
					$mail->SMTPSecure = 'ssl';
					$mail->Port = 465;

					$mail->setFrom('//email');

					$mail->addAddress($email);

					$mail->isHTML(true);

					$mail->Subject = 'SheOut Password Reset Code';
					$mail->Body = 'We have received a request to reset your SheOut password \n 
									Enter the following password reset code: \n 
									'.$token.' ';

					$mail->send();

					$_SESSION['email'] = $email;
						
					header("Location: forgetpassword_inputcode.php?id=$email");
            }
        }
    ?>
    <div style=" background-color: #FBF7D3;"><br><br><br>
    <div class="container d-absolute justify-content-left align-items-center min-vh-100 " >
        <div class="col-md-6 right-box">
                <div class="row align-items-center">
                        <div class="header-text mb-4">
                            <h2>Forget pass</h2>
                </div>
            <form method="post">
            <div class="input-group mb-3 ">
                <input type="email" name="email" class="form-control form-control-lg bg-light fs-6 border rounded-5"  placeholder="Email">
            </div>
            <div class="input-group mb-1 ">
                <input type="submit" value="Send Code" name="submit" class="button">
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
