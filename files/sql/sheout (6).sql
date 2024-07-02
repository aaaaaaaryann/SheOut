-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 01, 2024 at 02:14 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sheout`
--

-- --------------------------------------------------------

--
-- Table structure for table `admininfo`
--

CREATE TABLE `admininfo` (
  `adminID` int(11) NOT NULL,
  `admin_name` varchar(30) NOT NULL,
  `admin_email` varchar(30) NOT NULL,
  `admin_password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admininfo`
--

INSERT INTO `admininfo` (`adminID`, `admin_name`, `admin_email`, `admin_password`) VALUES
(1, 'Mochas', 'b@gmail.com', '$2y$10$YTORvSpSLcUqsK1CWn.ks.9gEk5CC5yFwRRl67ivIt2aoInPei3CS');

-- --------------------------------------------------------

--
-- Table structure for table `cancel_order`
--

CREATE TABLE `cancel_order` (
  `cancel_order_id` int(50) NOT NULL,
  `order_id` varchar(50) NOT NULL,
  `cancellation_date` date NOT NULL DEFAULT current_timestamp(),
  `cancellation_reason` text NOT NULL,
  `cancelled_by` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cancel_order`
--

INSERT INTO `cancel_order` (`cancel_order_id`, `order_id`, `cancellation_date`, `cancellation_reason`, `cancelled_by`) VALUES
(1, '20240618-6670d6d56afc7', '2024-06-18', 'sorry out of stock', 'Admin'),
(2, '20240620-6673e630dacc2', '2024-06-20', 'bkt pinto huhuhu', 'Admin'),
(3, '20240621-667501a819e99', '2024-06-21', 'option2', 'User'),
(4, '20240621-6675026a2e743', '2024-06-21', 'option1', 'User'),
(5, '20240621-6675029a8e751', '2024-06-21', 'ih', 'User');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(30) NOT NULL,
  `customer_id` int(30) NOT NULL,
  `product_id` int(30) NOT NULL,
  `amount` int(50) NOT NULL DEFAULT 1,
  `date_added` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `customer_id`, `product_id`, `amount`, `date_added`) VALUES
(9, 1, 6, 1, '2024-06-21'),
(10, 1, 3, 1, '2024-06-21');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(50) NOT NULL,
  `category_name` varchar(50) NOT NULL,
  `Visibility` varchar(11) NOT NULL DEFAULT 'Visible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `Visibility`) VALUES
(1, 'Fruity', 'Visible'),
(2, 'Floral', 'Visible'),
(3, 'Woody', 'Visible'),
(4, 'Sweet', 'Visible'),
(5, 'Vanilla', 'Visible'),
(6, 'Spicy', 'Visible'),
(7, 'Herbal', 'Visible'),
(8, 'Fresh', 'Visible');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(50) NOT NULL,
  `user_name` varchar(30) NOT NULL,
  `customer_address` varchar(100) NOT NULL,
  `customer_contact` varchar(11) NOT NULL,
  `customer_email` varchar(50) NOT NULL,
  `customer_password` varchar(255) NOT NULL,
  `token` int(4) NOT NULL,
  `account_status` varchar(25) NOT NULL DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `customer_name`, `user_name`, `customer_address`, `customer_contact`, `customer_email`, `customer_password`, `token`, `account_status`) VALUES
(1, 'aRYAN', ' Aryarns', 'imus, aae', '09123456789', 'arianvillaluz@gmail.com', '$2y$10$FV7QKKZCUF5i61ozOi3CA.BTC9Eu.BX7YjtmPvRKn8QrvJJdPB5/O', 5056, 'Active'),
(4, 'Jose Rizal', 'pepe', '', '', 'arianvillaluza@gmail.com', 'Rizal!12345', 2886, 'Active'),
(5, 'Maria Santos', 'mariaa', '123 Rizal Avenue, Barangay Malate, Manila, 1004 Metro Manila', '09993478290', 'arianvillaluzb@gmail.com', 'Maria!123', 0, 'Active'),
(6, 'babyamag22', 'babyamag22', '', '', 'babyamag22@gmail.com', '$2y$10$j5Fesbwj6e47OuBWrdZQXeOAAKwT7uBVE9mhc0O8BuNK5GFgzXeim', 0, 'Active');

-- --------------------------------------------------------

--
-- Table structure for table `customer_order`
--

CREATE TABLE `customer_order` (
  `order_id` varchar(25) NOT NULL,
  `customer_id` int(30) NOT NULL,
  `total_amount` int(50) NOT NULL,
  `order_name` varchar(50) NOT NULL,
  `order_address` varchar(64) NOT NULL,
  `order_contact` varchar(30) NOT NULL,
  `order_date` date NOT NULL DEFAULT current_timestamp(),
  `admin_confirmation` varchar(25) NOT NULL DEFAULT 'Pending',
  `customer_confirmation` varchar(25) NOT NULL DEFAULT 'Pending',
  `order_status` varchar(30) NOT NULL,
  `mode_of_delivery` varchar(30) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `shipping_fee` int(50) NOT NULL,
  `tracking_id` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `customer_order`
--

INSERT INTO `customer_order` (`order_id`, `customer_id`, `total_amount`, `order_name`, `order_address`, `order_contact`, `order_date`, `admin_confirmation`, `customer_confirmation`, `order_status`, `mode_of_delivery`, `payment_method`, `shipping_fee`, `tracking_id`) VALUES
('20240618-6670d6d56afc7', 1, 450, 'Aryan', 'imus, aa', '09123456789', '2024-06-18', 'Pending', 'Pending', 'Cancelled', 'Pickup', 'online', 0, ''),
('20240619-667236ae739f6', 1, 200, 'Aryan', 'imus, aa', '09123456789', '2024-06-19', 'Pending', 'Pending', 'Completed', 'Delivery', 'cod', 50, 'jdfh397420'),
('20240620-6673e630dacc2', 1, 200, 'Aryan', 'imus, aa', '09123456789', '2024-06-20', 'Pending', 'Pending', 'Cancelled', 'Delivery', 'online', 50, ''),
('20240621-6675039ec5bdc', 4, 350, 'Jose Rizal', '143 Emilio St. Calamba, Laguna', '09171256372', '2024-05-21', 'Pending', 'Pending', 'Completed', 'Delivery', 'cod', 50, '3451353'),
('20240621-66757c79a31a9', 5, 700, 'Maria Santos', '123 Rizal Avenue, Barangay Malate, Manila, 1004 Metro Manila', '09993478290', '2024-05-21', 'Pending', 'Pending', 'Completed', 'Pickup', 'cod', 0, ''),
('20240621-6675878d0b0e4', 5, 150, 'Maria Santos', '123 Rizal Avenue, Barangay Malate, Manila, 1004 Metro Manila', '09993478290', '2024-06-21', 'Pending', 'Pending', 'Completed', 'Pickup', 'cod', 0, ''),
('20240621-6675882c0236e', 1, 200, 'Aryan', 'imus, aa', '09123456789', '2024-06-21', 'Pending', 'Pending', 'Completed', 'Delivery', 'cod', 50, 'Abd-394-32'),
('20240623-66780286f2b1d', 1, 300, 'Aryan', 'imus, aa', '09123456789', '2024-06-23', 'Confirmed', 'Confirmed', 'Completed', 'Delivery', 'cod', 50, '23846203');

-- --------------------------------------------------------

--
-- Table structure for table `customer_order_product`
--

CREATE TABLE `customer_order_product` (
  `order_product_id` int(30) NOT NULL,
  `order_id` varchar(30) NOT NULL,
  `product_id` int(30) NOT NULL,
  `product_quantity` int(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `customer_order_product`
--

INSERT INTO `customer_order_product` (`order_product_id`, `order_id`, `product_id`, `product_quantity`) VALUES
(1, '20240618-6670d6d56afc7', 1, 3),
(2, '20240619-667236ae739f6', 1, 1),
(3, '20240620-6673e630dacc2', 1, 1),
(4, '20240621-667501a819e99', 1, 4),
(5, '20240621-6675026a2e743', 1, 3),
(6, '20240621-6675029a8e751', 2, 1),
(7, '20240621-6675039ec5bdc', 1, 2),
(8, '20240621-66757c79a31a9', 5, 1),
(9, '20240621-66757c79a31a9', 6, 1),
(10, '20240621-66757c79a31a9', 8, 1),
(11, '20240621-6675878d0b0e4', 1, 1),
(12, '20240621-6675882c0236e', 1, 1),
(13, '20240623-66780286f2b1d', 8, 1);

-- --------------------------------------------------------

--
-- Table structure for table `footer`
--

CREATE TABLE `footer` (
  `ID` int(25) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Phone` varchar(13) NOT NULL,
  `Address` varchar(60) NOT NULL,
  `Facebook` varchar(150) NOT NULL,
  `Twitter` varchar(150) NOT NULL,
  `Instagram` varchar(150) NOT NULL,
  `Logo` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `footer`
--

INSERT INTO `footer` (`ID`, `Email`, `Phone`, `Address`, `Facebook`, `Twitter`, `Instagram`, `Logo`) VALUES
(1, 'sheout@gmail.com', '09993478290', 'imus, cavite', '', 'link denxx', 'link ulet', 'Logo.png');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_id` varchar(25) NOT NULL,
  `proof_image` varchar(255) DEFAULT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `customer_id`, `order_id`, `proof_image`, `status`) VALUES
(1, 1, '20240618-6670d6d56afc7', 'uploads/payment_proofs/387556427_3568971180051251_6394346514071624738_n.jpg', 'Paid'),
(2, 1, '20240619-667236ae739f6', 'N/A', 'Paid'),
(3, 1, '20240620-6673e630dacc2', 'uploads/payment_proofs/door-vector-icon.jpg', 'Cancelled'),
(4, 4, '20240621-667501a819e99', 'N/A', 'Cancelled'),
(5, 4, '20240621-6675026a2e743', 'N/A', 'Cancelled'),
(6, 4, '20240621-6675029a8e751', 'N/A', 'Cancelled'),
(7, 4, '20240621-6675039ec5bdc', 'N/A', 'Paid'),
(8, 5, '20240621-66757c79a31a9', 'N/A', 'Paid'),
(9, 5, '20240621-6675878d0b0e4', 'N/A', 'Paid'),
(10, 1, '20240621-6675882c0236e', 'N/A', 'Paid'),
(11, 1, '20240623-66780286f2b1d', 'N/A', 'Paid');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(50) NOT NULL,
  `product_name` varchar(50) NOT NULL,
  `product_description` varchar(3000) NOT NULL,
  `product_image` varchar(50) NOT NULL,
  `product_price` int(50) NOT NULL,
  `product_stock` int(50) NOT NULL,
  `date_added` date NOT NULL DEFAULT current_timestamp(),
  `product_visibility` varchar(50) NOT NULL DEFAULT 'Visible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `product_description`, `product_image`, `product_price`, `product_stock`, `date_added`, `product_visibility`) VALUES
(1, 'Nordic Bubble Candles', 'Elevate your space with the Nordic Bubble Candle, a masterpiece that blends minimalist Scandinavian design with unparalleled craftsmanship. This candle not only illuminates with a warm, ambient glow but also serves as a striking decorative accent, enhancing the aesthetic of any room.&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\n&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\nKey Features:&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\n&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\nDistinctive Design: Inspired by Nordic aesthetics, each candle is meticulously handcrafted to embody elegance and modern simplicity. The bubble design creates a captivating visual appeal, making it a focal point in any setting.&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\n&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\nPremium Materials: Crafted from eco-friendly soy wax, our candles offer a clean and sustainable burn, ensuring both environmental responsibility and long-lasting enjoyment.&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\n&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\nArtisan Excellence: Hand-poured with precision, every Nordic Bubble Candle is a unique piece, reflecting the artisan&amp;amp;#039;s dedication to quality and beauty.&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\n&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\nAmbiance and Fragrance: Emitting a soft, soothing glow, our candles create a calming atmosphere ideal for relaxation and meditation. Choose from subtle scents like Lavender, Vanilla, or Eucalyptus to further enhance your sensory experience.&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\n&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\nSpecifications:&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\n&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\nDimensions: 3.5 x 3.5 x 3.5 inches&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\nBurn Time: Approximately 40 hours&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\nWax Type: 100% natural soy wax&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\nWick: Lead-free cotton wick&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\nIdeal For:&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\n&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\nPerfect for any living space, including living rooms, bedrooms, bathrooms, and home offices. Whether it&amp;amp;#039;s for everyday use, special occasions, or as a thoughtful gift, the Nordic Bubble Candle adds a touch of tranquility and sophistication to every environment.&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\n&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\nCare Instructions:&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\n&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\nTo ensure optimal performance and safety:&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\n&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\nTrim the wick to 1/4 inch before each burn.&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\nAllow the wax to melt evenly to prevent tunneling.&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\nKeep away from drafts, pets, and children.&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\nNever leave a burning candle unattended.&amp;lt;br /&amp;gt;&lt;br /&gt;<br />\r\nWhy Choose Nordic Bubble Candle?&amp;lt;br', 'ph-11134207-7r98y-lmyfwrjnrn5d14.jpeg', 150, 11, '2024-06-18', 'Visible'),
(2, ' Ribbed U shaped Mini Candle', 'Colourful and fun! Fabulous accessory for your table. One of a kind!&lt;br /&gt;&lt;br /&gt;<br />\r\n&lt;br /&gt;&lt;br /&gt;<br />\r\nThese centre piece candles are lovingly created in our home studio, made with eco-friendly (non-toxic, biodegradable, clean burning and completely free of animal derived substances) natural wax.&lt;br /&gt;&lt;br /&gt;<br />\r\n&lt;br /&gt;&lt;br /&gt;<br />\r\nThis candle is intended for decorative purposes but if you choose to burn the candle please place it on a heat proof dish and do not leave unattended.&lt;br /&gt;&lt;br /&gt;<br />\r\n&lt;br /&gt;&lt;br /&gt;<br />\r\nCandles are unscented. Each candle is handmade and unique, no two candles are the same!&lt;br /&gt;&lt;br /&gt;<br />\r\nBurn time is 5-8 hours approximately.&lt;br /&gt;&lt;br /&gt;<br />\r\n&lt;br /&gt;&lt;br /&gt;<br />\r\nDIMENSION:&lt;br /&gt;&lt;br /&gt;<br />\r\nL- 8cm&lt;br /&gt;&lt;br /&gt;<br />\r\nW - 3.5cm&lt;br /&gt;&lt;br /&gt;<br />\r\nL - 8cm', '8bf35a6f538271f31e149367dbff56bf.jpeg', 180, 20, '2024-06-21', 'Visible'),
(3, ' Aroma therapy Candle Set', 'Material: Soy mixed wax&lt;br /&gt;&lt;br /&gt;<br />\r\nEffect: Help sleep, increase fragrance&lt;br /&gt;&lt;br /&gt;<br />\r\nOptain A:Rose,Fragrance：Top notes: mango, peach, honey；Middle notes: caramel, raspberry, sweet orange；Base notes: grapefruit, lemon&lt;br /&gt;&lt;br /&gt;<br />\r\nOptain B:Orange,Fragrance：Top notes: citrus, grapefruit, round grapefruit；Middle notes: rose, patchouli, geranium；Base notes: pepper, benzoin&lt;br /&gt;&lt;br /&gt;<br />\r\nOptain C:Fresh spring,Fragrance：Top notes: lemon, citrus, grape；Middle notes: lavender, lily of the valley, rose, jasmine and other multi-flower compound fragrance；Base notes: pollen fragrance, ocean scent&lt;br /&gt;&lt;br /&gt;<br />\r\nOptain D:Lemon,Fragrance：Top notes: verbena, rosewood；Middle notes: Lemon, orange blossom；Base note: Bergamot&lt;br /&gt;&lt;br /&gt;<br />\r\n&lt;br /&gt;&lt;br /&gt;<br />\r\n***Note: It is recommended to remove the decorations before use to prevent the decorations from igniting and causing fire***&lt;br /&gt;&lt;br /&gt;<br />\r\n&lt;br /&gt;&lt;br /&gt;<br />\r\nNEW &amp; IMPROVED FORMULATION, DESIGN AND PACKAGING AS OF 2021&lt;br /&gt;&lt;br /&gt;<br />\r\n✔️ Made with premium 100% soy wax and fragrance oil. &lt;br /&gt;&lt;br /&gt;<br />\r\n✔️ Topped with dried flowers/real coffee beans&lt;br /&gt;&lt;br /&gt;<br />\r\nNOTE: Some scents are naturally mild and may not be to everyone’s liking esp if you are after a very strong hot throw. Others prefer it like that (esp with sensitive nose) and we aim to cater as many as possible. Whatever you are looking for, just hit us up on the chat box and we would love to assist you. &lt;br /&gt;&lt;br /&gt;<br />\r\nCAUTION: All embellishments on top are purely for aesthetic purposes only. May we kindly remind our clients to remove or keep all flowers far from the wick before lighting the candle as they are fire hazards. Keep safe everyone!', '907d36dd35c3184f0506fbe9793ef401.jpeg', 150, 10, '2024-06-21', 'Visible'),
(4, 'Illuminara Candle Lamp', 'The Illuminara Candle Lamp is meticulously crafted from high-quality wax, designed to mimic the graceful curves and sophisticated style of a vintage lamp. It features intricate detailing on the &quot;lamp&quot; base and a smooth, polished finish, giving it a realistic and luxurious appearance.&lt;br /&gt;<br />\r\n&lt;br /&gt;<br />\r\nWhen lit, the candle emits a soft, flickering light that radiates from the &quot;lamp shade,&quot; casting a cozy, ambient glow perfect for relaxing evenings, intimate gatherings, or adding a touch of elegance to your home decor. The subtle scent of vanilla infuses the air, enhancing the calming atmosphere with its sweet and soothing fragrance.', 'ph-11134201-7r98o-lkkqobcrvtj5d5.jpeg', 200, 20, '2024-06-21', 'Visible'),
(5, 'Tulip Candle', 'Bring the delicate beauty of a spring garden into your home with our Tulip Candle. Expertly crafted to resemble a blooming tulip, this enchanting candle captures the essence of nature’s elegance, offering both aesthetic charm and a soothing ambiance to any space.<br />\r\n<br />\r\nThe Tulip Candle is made from premium-quality wax, shaped meticulously to mimic the graceful petals and natural curves of a real tulip flower. Each petal is intricately detailed, showcasing subtle textures and variations in color that enhance its lifelike appearance. Available in a variety of soft, pastel hues—such as blush pink, lavender, and creamy white—this candle is a perfect fit for any decor style.', 'ph-11134201-7r98o-lvybxyd3lo02aa.jpeg', 100, 19, '2024-06-21', 'Visible'),
(6, 'Dessert Berry Frappe Soy Candle', 'Indulge your senses with the delightful aroma and charming look of the Berry Frappe Candle. This unique candle brings the essence of your favorite berry-flavored frappe into your home, offering both a visually appealing and aromatic experience that’s sure to delight.&lt;br /&gt;<br />\r\n&lt;br /&gt;<br />\r\nThe Berry Frappe Candle is crafted from high-quality, eco-friendly soy wax, designed to resemble a refreshing berry frappe drink. The candle features a vibrant blend of colors, mimicking the rich hues of mixed berries swirled into creamy perfection. Topped with a layer of &quot;whipped cream&quot; and a sprinkling of &quot;berry&quot; accents, this candle looks good enough to drink!', 'ph-11134207-7qul8-lhrm6y5qyvih4a.jpeg', 350, 9, '2024-06-21', 'Visible'),
(7, 'Creamy Vanilla ', 'Creamy vanilla scent just makes your home smelling like a house of candies! <br />\r\nMade with pure wax and high quality fragrance oil blend that is not too over powering for total mood relaxation.<br />\r\nDesigned with cute strawberries for a unique and adorable touch.', '212ac302d7c31faead3ec188e7187470.jpeg', 200, 10, '2024-06-21', 'Visible'),
(8, 'Pawsome Pup Candle', 'Celebrate your love for furry friends with the Pawsome Pup Candle, a delightful candle meticulously crafted to resemble an adorable dog. This charming candle not only adds a touch of whimsy to your decor but also brings warmth and light to any room.', 'sg-11134201-7rd45-lwh0tqryn3q59b.jpeg', 250, 28, '2024-06-21', 'Visible'),
(9, 'Twirl Candle', 'dimensions:<br />\r\nh:<br />\r\nw:<br />\r\nl:', 'ph-11134207-7r990-lla2c1kcswvm0c.jpeg', 159, 15, '2024-06-21', 'Visible'),
(10, 'Abstract Face Candle', 'Dimensions<br />\r\nh:<br />\r\nl:<br />\r\nw:', 'ph-11134207-7qul6-ljgfw7ytlu8s1c.jpeg', 100, 24, '2024-06-21', 'Visible');

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `product_category_id` int(50) NOT NULL,
  `product_id` int(50) NOT NULL,
  `category_id` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `product_categories`
--

INSERT INTO `product_categories` (`product_category_id`, `product_id`, `category_id`) VALUES
(11, 5, 1),
(12, 5, 2),
(13, 5, 4),
(15, 7, 1),
(16, 7, 4),
(24, 4, 2),
(25, 4, 8),
(26, 6, 1),
(27, 6, 4),
(28, 6, 5),
(29, 8, 7),
(30, 8, 8),
(31, 9, 3),
(32, 9, 7),
(33, 10, 3),
(34, 10, 6),
(35, 2, 4),
(36, 2, 8),
(37, 3, 2),
(38, 3, 3),
(39, 3, 6),
(40, 3, 7),
(51, 1, 1),
(52, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `product_image`
--

CREATE TABLE `product_image` (
  `image_id` int(100) NOT NULL,
  `product_id` int(50) NOT NULL,
  `image_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `product_image`
--

INSERT INTO `product_image` (`image_id`, `product_id`, `image_name`) VALUES
(1, 1, '4f9278d455a0da5d4a998a2cb94fd106.jpeg'),
(2, 1, '875f9dd670b774ef9bf401ea8c5b044b.jpeg'),
(3, 2, '586c9b5d0c91c450f3d2aed5bff92ef2.jpeg'),
(4, 2, '177dd558a28a3d97b916d29fa87546e1.jpeg'),
(5, 2, '28ba7168e36c0909bad7fd9a5fbd14e3.jpeg'),
(6, 2, '59504131771141a3707199331b7a14fc.jpeg'),
(7, 3, 'f0dee16c46c3c69f90ee562545fb9f12.jpeg'),
(8, 3, '2e30429f8313741918296cc2cbb0801d.jpeg'),
(9, 3, '0c10c341761c0ee3cd7bd946d81856b9.jpeg'),
(10, 4, 'ph-11134201-7r98o-lkkqo8p1rwom7b.jpeg'),
(11, 4, 'ph-11134201-7r98o-lkkqo7sv303le4.jpeg'),
(12, 4, 'ph-11134201-7r98o-lkkqo6kqvk8x3f.jpeg'),
(13, 5, 'ph-11134201-7r98r-lvybxwz5mqen8b.jpeg'),
(14, 5, 'ph-11134201-7r98y-lvybxxuscg1gd5.jpeg'),
(15, 5, 'ph-11134201-7r98x-lvybxxe50tek7e.jpeg'),
(16, 6, 'ph-11134207-7qul1-lhrm6y5qyvafcf.jpeg'),
(17, 7, '7ac6490ec30ba233ee4cec5239e7a5ec.jpeg'),
(18, 7, '71cd91bfa6a635328c05cba483a8bb88.jpeg'),
(19, 8, 'sg-11134201-7rd55-lwh0tp9kuozt66.jpeg'),
(20, 8, 'sg-11134201-7rd5b-lwh0tqex66n1a1.jpeg'),
(21, 8, 'sg-11134201-7rd75-lwh0toklv89p62.jpeg'),
(22, 9, 'ph-11134207-7r98y-lm8wlxw9su7lb2.jpeg'),
(23, 10, 'ph-11134207-7r98q-lp6e73k3y86h8b.jpeg'),
(24, 10, 'ph-11134207-7qukw-lj2a6r3foirqa0.jpeg'),
(25, 1, 'ph-11134207-7r98q-lp6e73k3y86h8b.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `rating_id` int(11) NOT NULL,
  `customer_id` int(30) NOT NULL,
  `user_name` varchar(30) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` varchar(30) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `review` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`rating_id`, `customer_id`, `user_name`, `product_id`, `order_id`, `rating`, `review`, `created_at`) VALUES
(1, 1, 'Anonymous', 1, '20240619-667236ae739f6', 4, 'Mabango gagi bili na kayo hehehehe 4 star kase binato ni driver sa gate', '2024-06-20 08:24:03'),
(3, 4, 'Anonymous', 1, '20240621-6675039ec5bdc', 5, ' fills my home with a soothing, calming aroma. Its long-lasting burn and elegant design make it perfect for relaxing evenings. A must-have for anyone looking to unwind and create a cozy atmosphere', '2024-06-21 13:28:23'),
(4, 5, 'mariaa', 5, '20240621-66757c79a31a9', 4, 'cute', '2024-06-21 13:48:40'),
(5, 5, 'Anonymous', 6, '20240621-66757c79a31a9', 5, '', '2024-06-21 13:48:46'),
(6, 5, 'mariaa', 8, '20240621-66757c79a31a9', 4, '', '2024-06-21 13:48:55'),
(7, 5, 'Anonymous', 1, '20240621-6675878d0b0e4', 1, 'Low quality product', '2024-06-21 14:02:14'),
(8, 1, 'Anonymous', 1, '20240621-6675882c0236e', 2, 'Di ko pa nattry pero 2 stars kase masungit yung driver', '2024-06-21 14:06:28');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admininfo`
--
ALTER TABLE `admininfo`
  ADD PRIMARY KEY (`adminID`);

--
-- Indexes for table `cancel_order`
--
ALTER TABLE `cancel_order`
  ADD PRIMARY KEY (`cancel_order_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `customer_order`
--
ALTER TABLE `customer_order`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `customer_order_product`
--
ALTER TABLE `customer_order_product`
  ADD PRIMARY KEY (`order_product_id`);

--
-- Indexes for table `footer`
--
ALTER TABLE `footer`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`product_category_id`);

--
-- Indexes for table `product_image`
--
ALTER TABLE `product_image`
  ADD PRIMARY KEY (`image_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`rating_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admininfo`
--
ALTER TABLE `admininfo`
  MODIFY `adminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `cancel_order`
--
ALTER TABLE `cancel_order`
  MODIFY `cancel_order_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `customer_order_product`
--
ALTER TABLE `customer_order_product`
  MODIFY `order_product_id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `footer`
--
ALTER TABLE `footer`
  MODIFY `ID` int(25) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `product_category_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `product_image`
--
ALTER TABLE `product_image`
  MODIFY `image_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
