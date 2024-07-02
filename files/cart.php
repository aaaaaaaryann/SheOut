<?php
session_start();
include("conn.php");
$page = 'cart';

// Ensure user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: userlogin.php");
    exit();
}

$customer_id = $_SESSION['customer_id'];

// Handle deletion of cart items
if (isset($_POST['delete'])) {
    if (!empty($_POST['selected_items'])) {
        foreach ($_POST['selected_items'] as $cart_id) {
            $cart_id = intval($cart_id);
            mysqli_query($conn, "DELETE FROM cart WHERE cart_id = $cart_id AND customer_id = $customer_id");
        }
    }
}

// Handle AJAX request for quantity update
if (isset($_POST['action']) && $_POST['action'] == 'update_quantity') {
    $cart_id = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);
    mysqli_query($conn, "UPDATE cart SET amount = $quantity WHERE cart_id = $cart_id AND customer_id = $customer_id");
    echo json_encode(['success' => true]);
    exit();
}

// Handle checkout form submission
// Handle checkout form submission
if (isset($_POST['checkout'])) {
    if (empty($_POST['selected_items'])) {
        $_SESSION['checkout_error'] = "No items selected";
        header("Location: cart.php"); // Redirect back to cart
        exit();
    }

    $_SESSION['checkout_items'] = array();
    foreach ($_POST['selected_items'] as $cart_id) {
        $cart_id = intval($cart_id);
        $query_item = mysqli_query($conn, "
            SELECT cart.cart_id, cart.customer_id, cart.product_id, product.product_name, 
            product.product_image, product.product_price, product.product_stock, 
            cart.amount AS total_quantity 
            FROM 
                cart 
            JOIN 
                product ON cart.product_id = product.product_id 
            WHERE 
                cart.cart_id = $cart_id AND cart.customer_id = $customer_id
        ");
        $item = mysqli_fetch_assoc($query_item);
        $_SESSION['checkout_items'][] = $item;
    }
    header("Location: checkout.php");
    exit();
}


// Retrieve cart items
$query_cart = mysqli_query($conn, "
    SELECT 
        cart.cart_id, 
        cart.product_id, 
        product.product_name, 
        product.product_image, 
        product.product_price, 
        product.product_stock, 
        cart.amount AS total_quantity
    FROM 
        cart
    JOIN 
        product 
    ON 
        cart.product_id = product.product_id
    WHERE 
        cart.customer_id = '$customer_id'
");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="cart.css">
    <title>My Cart</title>
    <script>
        function updateQuantity(cartId, quantity) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'cart.php', true); // Ensure this path matches your PHP file
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function () {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        updateTotal();
                    }
                }
            };
            xhr.send(`action=update_quantity&cart_id=${cartId}&quantity=${quantity}`);
        }

        function updateTotal() {
            const checkboxes = document.querySelectorAll('input[name="selected_items[]"]:checked');
            let totalAmount = 0;

            checkboxes.forEach(function (checkbox) {
                const cartItem = checkbox.closest('.cart-item');
                const price = parseFloat(cartItem.querySelector('.product-price').textContent.replace('₱', ''));
                const quantity = parseInt(cartItem.querySelector('input[type="number"]').value);
                totalAmount += price * quantity;
            });

            document.getElementById('totalAmount').textContent = totalAmount.toFixed(2);
        }

        function selectAllItems() {
            const checkboxes = document.querySelectorAll('input[name="selected_items[]"]');
            const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);

            checkboxes.forEach(function (checkbox) {
                checkbox.checked = !allChecked;
            });

            updateTotal();
        }

        function toggleCheckbox(event, cartId) {
            if (event.target.tagName.toLowerCase() === 'input') {
                return;
            }

            const checkbox = document.getElementById('checkbox-' + cartId);
            checkbox.checked = !checkbox.checked;
            updateTotal();
        }
    </script>
</head>
<body>
<?php include("usertopbar.php"); ?>
<nav class="breadcrumb">
    <a href="item_listing.php" class="breadcrumb-item">Products</a>
    <span class="breadcrumb-separator">></span>
    <a href="#" class="breadcrumb-item">Cart</a>
</nav> 
<h1>My Cart</h1>

<form method="post">
    <div class="cart-items">
        <button type="button" class="select-all-btn" onclick="selectAllItems()">Select All</button>
        <?php
        $total_amount = 0;
        while ($cart_row = mysqli_fetch_assoc($query_cart)) {
            $item_total = $cart_row['product_price'] * $cart_row['total_quantity'];
            $total_amount += $item_total;
        ?>
        <div class="cart-item" onclick="toggleCheckbox(event, '<?php echo $cart_row['cart_id']; ?>')">
            <input type="checkbox" name="selected_items[]" value="<?php echo $cart_row['cart_id']; ?>" onchange="updateTotal()" id="checkbox-<?php echo $cart_row['cart_id']; ?>">
            <div class="product-details">
                <?php if ($cart_row['product_image'] == '') { ?>
                    <img src="uploads/no_img.png" class="product-image">
                <?php } else { ?>
                    <img src="<?php echo "uploads/products/" . $cart_row['product_image']; ?>" class="product-image">
                <?php } ?>
                <div class="product-info">
                    <span class="product-name"><?php echo htmlspecialchars($cart_row['product_name']); ?></span>
                </div>
                <table class="price-table">
                    <tr>
                        <td><h3>Price</h3></td>
                        <td><h3>Quantity</h3></td>
                        <td><h3>Total</h3></td>
                    </tr>
                    <tr>
                        <td><span class="product-price">₱<?php echo htmlspecialchars($cart_row['product_price']); ?></span></td>
                        <td>
                            <input type="number" name="quantities[<?php echo $cart_row['cart_id']; ?>]" value="<?php echo htmlspecialchars($cart_row['total_quantity']); ?>" min="1" max="<?php echo $cart_row['product_stock']; ?>" onchange="updateQuantity(<?php echo $cart_row['cart_id']; ?>, this.value)">
                        </td>
                        <td><span class="item-total">₱<?php echo number_format($item_total, 2); ?></span></td>
                    </tr>
                </table>
            </div>
        </div>
        <?php } ?>
    </div>
    
    <div class="cart-summary">
        <div class="total-amount">
            <span>SUB TOTAL: ₱<span id="totalAmount"><?php echo number_format($total_amount, 2); ?></span></span>
        </div>
        <div class="cart-buttons" style="margin-bottom: 20px;">
            <input type="submit" name="delete" value="Delete Selected" class="cart-btn delete-btn">
            <input type="submit" name="checkout" value="PROCEED TO CHECKOUT" class="cart-btn checkout-btn">
        </div>
    </div>
</form>
<script>
    
    <?php if(isset($_SESSION['checkout_successful']) && $_SESSION['checkout_successful'] === true): ?>
        alert('Checkout Successful');
        
        <?php unset($_SESSION['checkout_successful']); ?>
    <?php endif; ?>
</script>
<script>
    window.onload = function() {
        <?php if(isset($_SESSION['checkout_error'])): ?>
            alert('<?php echo $_SESSION['checkout_error']; ?>');
            <?php unset($_SESSION['checkout_error']); ?>
        <?php endif; ?>
    }
</script>

</body>
<?php
include 'footer.php';
?>
</html>
