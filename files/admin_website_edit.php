<?php
ob_start();
session_start();
$page = 'website';
include("conn.php");
include "admin_topbar.php";
$admin_id = $_SESSION['admin_id'];

if ($admin_id == "") { 
    header('Location: adminlogin.php');
    exit;
}

$query_info = mysqli_query($conn, "SELECT * FROM footer WHERE ID='1'");
$row = mysqli_fetch_assoc($query_info);
$Logo = $row['Logo'];
$Email = $row['Email'];
$Phone = $row['Phone'];
$Address = $row['Address'];
$Facebook = $row['Facebook'];
$Twitter = $row['Twitter'];
$Instagram = $row['Instagram'];

if(isset($_POST['submit'])){
    $new_email = $_POST['email'];
    $new_phone = $_POST['phone'];
    $new_address = $_POST['address'];
    $new_facebook = $_POST['facebook'];
    $new_twitter = $_POST['twitter'];
    $new_instagram = $_POST['instagram'];

    if ($_FILES['logo']['name'] != '') {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["logo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["logo"]["tmp_name"]);
        if ($check !== false) {
            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                exit;
            }
            if (move_uploaded_file($_FILES["logo"]["tmp_name"], $target_file)) {
                $logo_image = basename($_FILES["logo"]["name"]);
                // Update main image in the database
                mysqli_query($conn, "UPDATE footer SET Logo = '$logo_image'");
            } else {
                echo "Sorry, there was an error uploading your file.";
                exit;
            }
        } else {
            echo "File is not an image.";
            exit;
        }
    }

    $update_website = mysqli_query($conn, "UPDATE footer SET Email = '$new_email', Phone = '$new_phone', Address = '$new_address', Facebook = '$new_facebook', Twitter = '$new_twitter', Instagram = '$new_instagram'");

    header("Location: admin_website_edit.php");


    
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="admin_website_edit.css">
    <title>Edit Form</title>
    <style>
        .read-only {
            pointer-events: none;
            background-color: #f0f0f0;
        }
        #update-button {
            display: none;
        }
    </style>
</head>
<body>
<div class="right">
        <h1>Edit Website Information</h1>
        <form method="post" enctype="multipart/form-data">
            <label for="logo">Logo:</label>
            <div class="image-preview">
                <div>
                    <input type="file" name="logo" id="logo" class="read-only" onchange="previewImage(event)">
                </div>
                <div>
                <?php if (!empty($Logo)): ?>
                    <img src="uploads/<?= $Logo ?>" alt="Current Logo" style="max-width: 150px; max-height: 150px;">
                <?php endif; ?>
                <img id="logo-preview" src="#" alt="Logo Preview">
                </div>
            </div>

            <label for="facebook">Facebook:</label>
            <input type="text" name="facebook" id="facebook" class="read-only" value="<?= $Facebook ?>">

            <label for="instagram">Instagram:</label>
            <input type="text" name="instagram" id="instagram" class="read-only" value="<?= $Instagram ?>">

            <label for="twitter">Twitter:</label>
            <input type="text" name="twitter" id="twitter" class="read-only" value="<?= $Twitter ?>">

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" class="read-only" value="<?= $Email ?>">

            <label for="address">Address:</label>
            <input type="text" name="address" id="address" class="read-only" value="<?= $Address ?>">

            <label for="phone">Contact Number:</label>
            
             <input type="text" name="phone" id="contact" class="read-only" value="<?php echo $Phone; ?>"><br>


            <button type="button" id="edit-button" onclick="editForm()">Edit</button>
            <input type="submit" name="submit" id="update-button" value="Update">
        </form>
    </div>

    <script>
        function editForm() {
            var elements = document.querySelectorAll('#facebook, #instagram, #twitter, #email, #address, #contact, #logo');
            elements.forEach(function(element) {
                element.classList.remove('read-only');
            });
            document.getElementById('update-button').style.display = 'inline';
            document.getElementById('edit-button').style.display = 'none';
        }

        function previewImage(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('logo-preview');
                output.src = reader.result;
                output.style.display = 'block';
            }
            reader.readAsDataURL(event.target.files[0]);
        }
    </script>
</body>
</html>

