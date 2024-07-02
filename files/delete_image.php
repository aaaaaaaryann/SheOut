<?php
ob_start();
session_start();


if (!isset($_SESSION['admin_id'])) { 
    header('Location: adminlogin.php');
    exit;
}


include("conn.php");


if (isset($_POST['image_id'])) {
    
    $image_id = mysqli_real_escape_string($conn, $_POST['image_id']);

    
    $delete_query = "DELETE FROM product_image WHERE image_id = '$image_id'";
    if (mysqli_query($conn, $delete_query)) {
        
        echo "Image deleted successfully.";
    } else {
       
        echo "Error deleting image from the database: " . mysqli_error($conn);
    }
} else {
    
    echo "No image_id provided.";
}


mysqli_close($conn);
?>
