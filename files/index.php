<?php
ob_start();
session_start();
$page = 'home';
include "conn.php";

if (!isset($_SESSION['customer_id'])) {
  include "topbar.php";
  $customer_id = "";
} else {
  $customer_id = $_SESSION['customer_id'];
  include "usertopbar.php";
}


$query_products = mysqli_query($conn, "SELECT * FROM product WHERE product_visibility='Visible' ORDER BY RAND() LIMIT 5");


?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SheOut</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="container">
    <div class="left_container">
      <div>
        <p>#CANDLELIGHTS</p>
        <h2 style="font-size: 50px;">Always cool and smooth<br> your feelings available<br> in a variety of candle</h2>
        <a href="item_listing.php"><div class="button">Discover Products</div></a>
      </div>
    </div>
    <div class="image-slider">
      <div class="slider-container">
        <div class="slider">
          <div class="slide"><img src="uploads/1.png" alt="Image 1"></div>
          <div class="slide"><img src="uploads/2.png" alt="Image 2"></div>
          <div class="slide"><img src="uploads/3.png" alt="Image 3"></div>
        </div>
      </div>
      <button class="prev">&#10094;</button>
      <button class="next">&#10095;</button>
    </div>
  </div>
  <script>
    const prevBtn = document.querySelector('.prev');
    const nextBtn = document.querySelector('.next');
    const slider = document.querySelector('.slider');
    let slideIndex = 0;

    function showSlides() {
      const slides = document.querySelectorAll('.slide');
      if (slideIndex >= slides.length) {
        slideIndex = 0;
      } else if (slideIndex < 0) {
        slideIndex = slides.length - 1;
      }
      slider.style.transform = `translateX(-${slideIndex * 100}%)`;
    }

    prevBtn.addEventListener('click', () => {
      slideIndex--;
      showSlides();
    });

    nextBtn.addEventListener('click', () => {
      slideIndex++;
      showSlides();
    });

    showSlides();
  </script>

  <br><br><br><br>
  <div class="text">
    <h1>Our favorite fragrances to celebrate the season</h1>
  </div>

  <div class="card-container">
  
    <?php while ($row = mysqli_fetch_assoc($query_products)) {
      $product_id = $row['product_id'];
      ?>
      <div class="product-card">
      <a href="product_information.php?id=<?php echo htmlspecialchars($product_id); ?>">
        <?php
        if($row['product_image'] == ''){
          ?>
          <img src="uploads/no_img.png" alt="<?php echo $row['product_name']; ?>">
          <?php
        }else{
          ?><img src="uploads/products/<?php echo $row['product_image']; ?>" alt="<?php echo $row['product_name']; ?>"><?php
        }
        ?>

        <div class="product-container">
          <div class="product-name">
              <h3><?php echo strlen($row['product_name']) > 12 ? substr($row['product_name'], 0, 12) . '...' : $row['product_name']; ?></h3>
          </div>
          <div class="product-price">
             <p style="color: #f57224;"> ₱<?php echo $row['product_price']; ?></p>
          </div>
      </div>

        <form method="post" onsubmit="return checkCustomerID()">
                        <input type="submit" value="ADD TO CART" name="addtocart<?php echo $product_id; ?>" class="card-button">
                        <?php
if (isset($_POST['addtocart' . $product_id])) {
  if (trim($customer_id) == '') {
      echo "<script>alert('Please log in first.');</script>";
  } else {
      $check_cart_query = "SELECT * FROM cart WHERE customer_id = $customer_id AND product_id = $product_id";
      $check_cart_result = mysqli_query($conn, $check_cart_query);

      if (mysqli_num_rows($check_cart_result) > 0) {
          
          $update_cart_query = "UPDATE cart SET amount = amount + 1 WHERE customer_id = $customer_id AND product_id = $product_id";
          mysqli_query($conn, $update_cart_query);
          echo "<script>alert('Added to cart successfully');</script>";
      } else {
          
          $add_to_cart_query = "INSERT INTO cart (product_id, customer_id, amount, date_added) VALUES($product_id, $customer_id, 1, current_timestamp())";
          mysqli_query($conn, $add_to_cart_query);
          echo "<script>alert('Added to cart successfully');</script>";
      }
  }
}

?>
                    </form>
                    
      </div>
      
    <?php } ?>
  </div></a>
  <div class="discovery">
  <a href="item_listing.php" class="dis">View More</a>
  </div>

  <div id="about">
    <div class="about_us">
      <div class="text-column">
        <div>
          <h1 style="font-size: 90px">About us</h1>
        </div>
        <p style="font-size: 20px">
          Family Owned | Home Made | Certified

          At SheOUT we sustainably craft candles and home fragrance with people and the planet at our heart.

          Based on our farm in Cavite, Candle HUB our unique nature-inspired scents imbue the beautiful coast and countryside surrounding us.
        </p>
      </div>
      <div class="image-column">
        <img src="uploads/background.jpg">
      </div>
    </div>
  </div>




  <script>
    function checkCustomerID() {
      var customerID = "<?php echo isset($customer_id) ? $customer_id : ''; ?>";
      if (customerID === '') {
        window.location.href = 'userlogin.php';
        return false;
      }
      return true;
    }
  </script>
</body>
<?php
include 'footer.php';
?>
</html>