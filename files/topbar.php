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
    <link rel="stylesheet" href="topbar.css">
</head>
<header>
    <div class="top-bar">
        <div class="logo">
            <a href="#"><img src="uploads/<?php echo $Logo; ?>" alt="Logo"></a>
        </div>
        
        <nav class="nav-items">
            <ul id="nav-list">
                <li class="<?php if($page=='home'){echo 'active';} ?>"><a href="index.php">Home</a></li>
                <li class="<?php if($page=='products'){echo 'active';} ?>"><a href="item_listing.php">Products</a></li>
                <li class="<?php if($page=='about'){echo 'active';} ?>"><a href="index.php#about">About</a></li>
                
            </ul>
        </nav>
        <nav class="nav-right nav-items">
            <ul id="nav-list">
                <li>
                    <form id="search-form" action="item_listing.php" method="get">
                        <input type="text" name="search" id="search-input" placeholder="Search..." style="<?php echo ($page == 'products') ? '' : 'display: none;'; ?>">
                        <a href="#" id="search-icon"><img src="uploads/search.png" style="height: 20px;"></a>
                    </form>
                </li>
                <li><a href="userlogin.php"><img src="uploads/user.png" style="height: 20px;"></a></li>
                <li><a href="userlogin.php#"><img src="uploads/shop.png" style="height: 20px;"></a></li>
                <li><a href="userlogin.php"><div class="login">Login</div></a></li>
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
            if (currentPage !== 'products') {
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
<body>
    
</body>
</html>
