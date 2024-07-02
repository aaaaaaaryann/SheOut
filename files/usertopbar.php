<?php
include("conn.php");

// Get the customer ID from the session
$customer_id = $_SESSION['customer_id'] ?? '';

// If the customer ID is not set, redirect to the login page
if ($customer_id == "") {
    echo '<meta http-equiv="refresh" content="0; url=\'userlogin.php\'"/>';
    exit;
}

// Fetch the customer profile from the database
$query_profile = mysqli_query($conn, "SELECT * FROM customer WHERE customer_id = '$customer_id'");
$rows = mysqli_fetch_assoc($query_profile);
$customer_name = $rows['customer_name'];
$account_status = $rows['account_status'];

if($account_status == 'Inactive'){
    session_destroy();
    header("Location: userlogin.php");
    exit();
}

// Determine the current page
$page = basename($_SERVER['PHP_SELF'], ".php");

// Fetch the number of items in the cart
$query_cart_count = mysqli_query($conn, "SELECT COUNT(*) AS cart_count FROM cart WHERE customer_id = '$customer_id'");
$cart_count_row = mysqli_fetch_assoc($query_cart_count);
$cart_count = $cart_count_row['cart_count'];

$query_info = mysqli_query($conn, "SELECT * FROM footer WHERE ID='1'");
$row = mysqli_fetch_assoc($query_info);
$Logo = $row['Logo'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="usertopbar.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
<header>
    <div class="top-bar">
        <div class="logo">
            <a href="#"><img src="uploads/<?php echo $Logo; ?>" alt="Logo"></a>
        </div>
        
        <nav class="nav-items">
            <ul id="nav-list">
                <li class="<?php if($page == 'index'){echo 'active';} ?>"><a href="index.php">Home</a></li>
                <li class="<?php if($page == 'item_listing'){echo 'active';} ?>"><a href="item_listing.php">Products</a></li>
                <li class="<?php if($page == 'about'){echo 'active';} ?>"><a href="index.php#about">About</a></li>
                
            </ul>
        </nav>
        <nav class="nav-right nav-items">
            <ul id="nav-list">
                <li>
                    <form id="search-form" action="item_listing.php" method="get">
                        <input type="text" name="search" id="search-input" placeholder="Search..." style="<?php echo ($page == 'item_listing') ? '' : 'display: none;'; ?>">
                        <a href="#" id="search-icon"><img src="uploads/search.png" style="height: 20px;"></a>
                    </form>
                </li>
                <li class="<?php if($page == 'cart'){echo 'active';} ?>"><a href="cart.php"><img src="uploads/shop.png" style="height: 20px;">   <?php echo '<span style="color: red;">' . $cart_count . '</span>'; ?></a></li>
                <li class="<?php if($page == 'userprofile'){echo 'active';} ?>"><a href="userprofile.php"><?php echo $customer_name; ?></a></li>
                <li><a href="logout.php"><div class="login">Logout</div></a></li>
            </ul>
        </nav>
                
        <div class="nav-toggle" id="nav-toggle">&#9776;</div>
    </div>

    <script>
        document.getElementById('nav-toggle').addEventListener('click', function() {
            document.getElementById('nav-list').classList.toggle('show');
        });

        document.getElementById('search-icon').addEventListener('click', function(event) {
            event.preventDefault();
            var searchInput = document.getElementById('search-input');
            var currentPage = "<?php echo $page; ?>";
            if (currentPage !== 'item_listing') {
                document.getElementById('search-form').action = "item_listing.php";
                document.getElementById('search-form').submit();
            } else {
                if (searchInput.style.display === 'none' || searchInput.style.display === '') {
                    searchInput.style.display = 'block';
                    searchInput.focus();
                } else {
                    document.getElementById('search-form').submit();
                }
            }
            this.style.display = 'none';
        });
    </script>
</header>
</body>
</html>
