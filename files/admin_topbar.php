<?php
    include("conn.php");
    $query_info = mysqli_query($conn, "SELECT * FROM footer WHERE ID='1'");
    $row = mysqli_fetch_assoc($query_info);
    $Logo = $row['Logo'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin_topbar.css">
    <link rel="stylesheet" href="admin_sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="topbar">
        <div class="logo"><img src="uploads/<?php echo $Logo; ?>" alt="Logo"></div>
        <div class="user-dropdown">
            <i class="fa fa-user" style="color: black; cursor: pointer;" onclick="toggleDropdown()"></i>
            <div class="dropdown-content" id="userDropdown">
                <a href="admin_profile.php">Admin Profile</a>
                <a href="admin_logout.php">Logout</a>
            </div>
        </div>
    </header>
<div class="left">
    <div class="sidebar">
        <nav>
            <ul>
                <li><a href="admin_dashboard.php" onclick="loadContent('dashboard')" class="<?php if($page == 'Dashboard'){echo 'active';} ?>"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="admin_manage_orders.php" onclick="loadContent('orders')" class="<?php if($page == 'orders'){echo 'active';} ?>"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li><a href="admin_manage_product.php" onclick="loadContent('products')" class="<?php if($page == 'products'){echo 'active';} ?>"><i class="fas fa-box"></i> Products</a></li>
                <li><a href="admin_manage_payment.php" onclick="loadContent('payment')" class="<?php if($page == 'payment'){echo 'active';} ?>"><i class="fas fa-credit-card"></i> Payments</a></li>
                <li><a href="admin_manage_categories.php" onclick="loadContent('filter')" class="<?php if($page == 'filter'){echo 'active';} ?>"><i class="fas fa-filter"></i> Filters</a></li>
                <li><a href="admin_sales.php" onclick="loadContent('sales')" class="<?php if($page == 'sales'){echo 'active';} ?>"><i class="fas fa-credit-card-alt"></i> Sales</a></li>
                <li><a href="admin_website_edit.php" onclick="loadContent('website')"  class="<?php if($page == 'website'){echo 'active';} ?>"><i class="fas fa-globe"></i> Website</a></li>
                <li><a href="admin_manage_user.php" onclick="loadContent('user')"  class="<?php if($page == 'user'){echo 'active';} ?>"><i class="fas fa-user"></i> User Info</a></li>
            </ul>
        </nav>
    </div>
</div>
   

    <script>
        function toggleDropdown() {
            document.getElementById("userDropdown").classList.toggle("show");
        }

        function loadContent(page) {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("mainContent").innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", page + ".html", true);
            xhttp.send();
        }
    </script>
</body>
</html>
