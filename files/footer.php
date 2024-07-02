<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
    <link rel="stylesheet" href="footer.css">
</head>
<body>

<?php
$query_info = mysqli_query($conn, "SELECT * FROM footer WHERE ID='1'");
$row = mysqli_fetch_assoc($query_info);
$Email = $row['Email'];
$Phone = $row['Phone'];
$Address = $row['Address'];
$Facebook = $row['Facebook'];
$Twitter = $row['Twitter'];
$Instagram = $row['Instagram'];
?>

    <footer class="footer">
          <div class="footer-column">
              <h3>About Us</h3>
              <p>Company Information</p>
              <p>Careers</p>
              <p>Privacy Policy</p>
          </div>
          <div class="footer-column">
              <h3>Contact</h3>
              <?php
              if($Email != ''){
                ?>
                    <p>Email: <?php echo $Email; ?></p>
                <?php
              }if ($Phone != '' ){
                ?>
                    <p>Phone: <?php echo $Phone; ?></p>
                <?php
              }if ($Address != '' ){
                ?>
                    <p>Address: <?php echo $Address; ?></p>
                <?php
              }
              ?>
              
              
          </div>
          <div class="footer-column">
              <h3>Follow Us</h3>
              <?php
              if ($Facebook != '' ){
                ?>
                    <p><a href="<?php echo $Facebook; ?>">Facebook</a></p>
                <?php
              }if ($Twitter != '' ){
                ?>
                    <p><a href="<?php echo $Twitter; ?>">Twitter</a></p>
                <?php
              }if ($Instagram != '' ){
                ?>
                    <p><a href="<?php echo $Instagram; ?>">Instagram</a></p>
                <?php
              }
              ?>
             
          </div>
      </footer>
 </div>
</body>
</html>
