<?php
ob_start();
session_start();
include("conn.php");
include "usertopbar.php";

// Ensure user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: userlogin.php");
    exit();
}

function generateOrderId() {
    $currentDate = date("Ymd");
    $uniqueId = uniqid();
    $orderId = $currentDate . '-' . $uniqueId;
    return $orderId;
}

$order_id = generateOrderId();
$customer_id = $_SESSION['customer_id'];

// Retrieve selected cart items from session
$checkout_items = isset($_SESSION['checkout_items']) ? $_SESSION['checkout_items'] : array();

if (empty($checkout_items)) {
    header("Location: cart.php");
    exit();
}

// Retrieve customer information from database based on customer_id
$query_customer = mysqli_query($conn, "SELECT * FROM customer WHERE customer_id = '$customer_id'");
$customer_info = mysqli_fetch_assoc($query_customer);

// Calculate total amount for the order based on selected items
$total_amount = 0;
$total_quantity = 0;
foreach ($checkout_items as $item) {
    $product_id = $item['product_id'];
    $query_product = mysqli_query($conn, "SELECT * FROM product WHERE product_id = '$product_id'");
    $product_info = mysqli_fetch_assoc($query_product);
    $subtotal = $product_info['product_price'] * $item['total_quantity'];
    $total_amount += $subtotal;
    $total_quantity += $item['total_quantity'];
}

// Process form submission when user clicks checkout button
if (isset($_POST['checkout'])) {
    // Check stock for each item
    $stock_issue = false;
    foreach ($checkout_items as $item) {
        $product_id = $item['product_id'];
        $query_stock = mysqli_query($conn, "SELECT product_stock, product_name, product_price FROM product WHERE product_id = '$product_id'");
        $product_info = mysqli_fetch_assoc($query_stock);
        $product_stock = $product_info['product_stock'];
        $product_name = $product_info['product_name'];

        if ($item['total_quantity'] > $product_stock) {
            $stock_issue = true;
            echo "<script>alert('The quantity of \'{$product_name}\' exceeds the available stock.'); window.history.back();</script>";
            break;
        }
    }

    if (!$stock_issue) {
        // Retrieve delivery mode and updated customer details from form submission
        $delivery_mode = $_POST['delivery_mode'];
        $payment_method = $_POST['payment_method'];
        $updated_name = $_POST['customer_name'];
        $updated_address = $_POST['customer_address'];
        $updated_contact = $_POST['customer_contact'];
        $shipping_fee = isset($_POST['shipping_fee']) ? $_POST['shipping_fee'] : 0;

        

        // Update total amount to include shipping fee
        $total_amount += $shipping_fee;

        // Insert order details into customer_order table
        $order_date = date('Y-m-d H:i:s');
        $insert_order_query = "INSERT INTO customer_order (order_id, customer_id, order_address, order_contact, order_date, order_status, mode_of_delivery, total_amount, payment_method, shipping_fee, order_name) VALUES ('$order_id', '$customer_id', '$updated_address', '$updated_contact', '$order_date', 'Pending', '$delivery_mode', '$total_amount', '$payment_method', '$shipping_fee', '$updated_name')";
        mysqli_query($conn, $insert_order_query);

        if ($payment_method == 'cod') {
            // Insert payment record for COD
            $insert_payment_query = "INSERT INTO payments (customer_id, order_id, proof_image, status) VALUES ('$customer_id', '$order_id', 'N/A', 'Pending')";
            mysqli_query($conn, $insert_payment_query);
        }

        // Insert order items into customer_order_product table
        foreach ($checkout_items as $item) {
            $product_id = $item['product_id'];
            $quantity = $item['total_quantity'];
            $insert_order_item_query = "INSERT INTO customer_order_product (order_id, product_id, product_quantity) VALUES ('$order_id', '$product_id', '$quantity')";
            mysqli_query($conn, $insert_order_item_query);
        }

        // Handle payment proof submission if online payment is selected
        if ($payment_method == 'online') {
            if (empty($_FILES['proof_image']['name'])) {
                echo "<script>alert('Please upload a proof of payment image before checking out.');</script>";
            } else {
                $target_dir = "uploads/payment_proofs/";
                $target_file = $target_dir . basename($_FILES["proof_image"]["name"]);
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                // Check if image file is an actual image or fake image
                $check = getimagesize($_FILES["proof_image"]["tmp_name"]);
                if ($check !== false) {
                    // Check file size (limit to 5MB)
                    if ($_FILES["proof_image"]["size"] <= 5000000) {
                        // Allow certain file formats
                        if ($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg") {
                            if (move_uploaded_file($_FILES["proof_image"]["tmp_name"], $target_file)) {
                                // Insert payment proof into database
                                $query = "INSERT INTO payments (customer_id, order_id, proof_image, status) VALUES ('$customer_id', '$order_id', '$target_file', 'Pending')";
                                mysqli_query($conn, $query);
                            }
                        }
                    }
                }
            }
        }

        // Delete checked out items from cart
        foreach ($checkout_items as $item) {
            $query_delete_cart = mysqli_query($conn, "DELETE FROM cart WHERE product_id = '{$item['product_id']}' AND customer_id = '{$item['customer_id']}'");
        }

        // Clear cart items from session after checkout
        unset($_SESSION['checkout_items']);

        // Redirect user to order confirmation page
        header("Location: user_order_details.php?id=" . urlencode($order_id));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="checkout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script>
        function makeEditable(id) {
            const input = document.getElementById(id);
            if (input.hasAttribute('readonly')) {
                input.removeAttribute('readonly');
            } else {
                input.setAttribute('readonly', 'readonly');
            }
        }

        function updateTotalAmount(shippingFee) {
            const totalAmount = <?php echo $total_amount; ?>;
            const finalTotalElement = document.getElementById('final-total');
            finalTotalElement.textContent = totalAmount + shippingFee;
        }

        function toggleShippingOptions() {
            const deliveryMode = document.querySelector('input[name="delivery_mode"]:checked').value;
            const shippingOptions = document.getElementById('shipping-options');
            const shippingFeeInput = document.querySelector('input[name="shipping_fee"][value="50"]');
            if (deliveryMode === 'Pickup') {
                shippingOptions.style.display = 'none';
                updateTotalAmount(0);
                shippingFeeInput.checked = false;
            } else {
                shippingOptions.style.display = 'block';
                updateTotalAmount(50);
                shippingFeeInput.checked = true;
            }
        }

        function toggleProofForm() {
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            const proofForm = document.getElementById('proofForm');
            if (paymentMethod === 'online') {
                proofForm.style.display = 'block';
            } else {
                proofForm.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleShippingOptions();
            toggleProofForm();
        });

        function validateForm() {
            const deliveryMode = document.querySelector('input[name="delivery_mode"]:checked').value;
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

            // Check if delivery mode is pickup and skip address and contact validation
            if (deliveryMode === 'Pickup') {
                return true;
            }

            // Validate customer address
            const address = document.getElementById('customer_address').value.trim();
            if (address === '') {
                alert("Please enter a valid address.");
                return false;
            }

            // Validate customer contact
            const contact = document.getElementById('customer_contact').value.trim();
            if (contact === '') {
                alert("Please enter a valid contact number.");
                return false;
            }

            // Validate proof of payment if online payment is selected
            if (paymentMethod === 'online') {
                const proofImage = document.querySelector('input[name="proof_image"]').files[0];
                if (!proofImage) {
                    alert("Please upload a proof of payment image before checking out.");
                    return false;
                }
            }

            return true;
        }
    </script>
</head>
<body>
    <div class="checkouts"> 
        <nav class="breadcrumb">
            <a href="cart.php" class="breadcrumb-item">Cart</a>
            <span class="breadcrumb-separator">></span>
            <a href="#" class="breadcrumb-item">CheckOut</a>
        </nav>              
        <form method="post" enctype="multipart/form-data" onsubmit="return validateForm()">
            <div class="right_area">
                <div class="rightmode">
                    <h2>Delivery Mode</h2>
                    <label>
                        <input type="radio" name="delivery_mode" value="Delivery" checked onclick="toggleShippingOptions()"><i class="fa fa-fa fa-truck"></i> Delivery(J&T)
                    </label>
                    <label>
                        <input type="radio" name="delivery_mode" value="Pickup" onclick="toggleShippingOptions()"> <i class="fas fa-store"></i> Pickup
                    </label><hr>
                    <div id="shipping-options" style="display:none;">
                        <h2>Shipping Fee</h2>
                        <label>
                            <input type="radio" name="shipping_fee" value="50" onclick="updateTotalAmount(50)"> ₱50 
                        </label>
                        <hr>
                    </div>
                    <h2>Payment Method</h2>
                    <label>
                        <input type="radio" name="payment_method" value="cod" checked onclick="toggleProofForm()"> Cash on Delivery (COD)
                    </label>
                    <label>
                        <input type="radio" name="payment_method" value="online" onclick="toggleProofForm()"> Online Payment (G-Cash payment)
                    </label>
                    <div id="proofForm" style="display:none;">
                        <h2>Upload Proof of Payment</h2>
                        <div class="image-container">
                            <img src="uploads/qr.jpg" alt="QR Code" class="clickable-image" onclick="openModal()" style="height:300px;">
                        </div>
                        <div id="imageModal" class="modal">
                            <span class="close" onclick="closeModal()">&times;</span>
                            <img class="modal-content" id="modalImage">
                            <div id="caption"></div>
                        </div>
                        <input type="file" name="proof_image" accept="image/*"> 
                    </div>
                    <table>
                        <tr>
                            <td>Quantity</td>
                            <td>x <?php echo htmlspecialchars($total_quantity); ?></td>
                        </tr>
                        <tr>
                            <td>Total Price</td>
                            <td>₱<span id="final-total"><?php echo $total_amount; ?></span></td>
                        </tr>
                    </table>
                    <input type="submit" name="checkout" value="Checkout">
                </div>               
            </div>
            <h2 style="margin-left:20px;">CheckOut</h2>
            <?php foreach ($checkout_items as $item) { ?>
            <div class="product-container">
                <div class="product-image">
                    <?php
                        if ($item['product_image'] == '') { ?>
                            <img src="uploads/no_img.png" style="width: 100px; height: 100px;">
                        <?php } else { ?>
                            <img src="uploads/products/<?php echo $item['product_image']; ?>" style="width: 100px; height: 100px;" alt="<?php echo htmlspecialchars($item['product_name']); ?>" style="max-width: 100px;">
                        <?php }
                    ?>
                </div>
                <div class="product-details">
                    <h2 class="product-title"> <?php echo htmlspecialchars($item['product_name']); ?></h2>
                    <td> <input class="input" type="number" value="<?php echo htmlspecialchars($item['total_quantity']); ?>" readonly></td>
                    <p class="product-price"><h4>Sub Total: ₱<span id="total-amount"><?php echo $item['total_quantity'] * $item['product_price'] ; ?></span></h4></p>
                </div>
            </div>
            <?php } ?>
            <div class="left_area">
                <h2>Customer Information</h2>
                <div class="user_info">
                    <div class="tb_info">
                        <table>
                            <thead>
                                <tr>
                                    <th>Recipient's Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <i class="fa fa-pencil" onclick="makeEditable('customer_name')"></i>
                                        <input class="input" type="text" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($customer_info['customer_name']); ?>" readonly><br>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table>
                            <thead>
                                <tr>
                                    <th>Address</th>
                                    <th>Phone</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <i class="fa fa-pencil" onclick="makeEditable('customer_address')"></i>
                                        <input class="input" type="text" id="customer_address" name="customer_address" value="<?php echo htmlspecialchars($customer_info['customer_address']); ?>" readonly>
                                    </td>
                                    <td>
                                        <i class="fa fa-pencil" onclick="makeEditable('customer_contact')"></i>
                                        <input class="input" type="text" id="customer_contact" name="customer_contact" value="<?php echo htmlspecialchars($customer_info['customer_contact']); ?>" readonly><br>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </form>
    </div>
</body>
<!-- Image popup-->
<script>
    function openModal() {
        var modal = document.getElementById('imageModal');
        var modalImg = document.getElementById('modalImage');
        var img = document.querySelector('.clickable-image');
        modal.style.display = "block";
        modalImg.src = img.src;
        var captionText = document.getElementById('caption');
        captionText.innerHTML = img.alt;
    }

    function closeModal() {
        var modal = document.getElementById('imageModal');
        modal.style.display = "none";
    }
</script>
<?php
include 'footer.php';
?>
</html>
