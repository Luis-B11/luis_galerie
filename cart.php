<!DOCTYPE html>
<html>
	<head>
		<title>Luis Galerie</title>
		<style>
			body {
				font-family: Arial, sans-serif;
				margin: 0;
				padding: 0;
			}
    
			header {
				background-color: #333;
				color: #fff;
				padding: 10px;
				text-align: center;
			}
			
			.navbar {
				background-color: gray;
				color: #fff;
				padding: 10px;
				text-align: center;
				position: sticky;
			}
			
			.button {
				background-color: #333;
				color: #fff;
				padding: 5px 10px;
				border: none;
				cursor: pointer;
				width: 150px;
			}
    
			.button:hover {
				background-color: #555;
			}
    
			main {
				margin: 20px;
			}
    
			h1 {
				font-size: 24px;
			}
			
			h2 {
				font-size: 18px;
			}
			
			.container {
				display: flex;
				flex-direction: row;
				flex-wrap: wrap;
				justify-content: flex-start;
			}

			.product {
				min-width: 300px;
				width: calc(33.33% - 22px);
				min-height: 200px;
				height: auto;
				border: 1px solid #ccc;
				display: flex;
				flex-direction: column;
				align-items: center;
				text-align: center;
				padding: 5px;
				margin: 5px;
				margin-bottom: 20px;
				justify-content: end;
			}
    
			.product img {
				max-width: 25%;
			}
    
			.product h2 {
				font-size: 18px;
				margin-top: 0;
			}
    
			.product p {
				margin-bottom: 0;
			}
			
			.product button {
				background-color: #333;
				color: #fff;
				padding: 5px 10px;
				border: none;
				cursor: pointer;
			}
    
			.product button:hover {
				background-color: #555;
			}
			
			hr {
				border: none;
				border-top: 1px solid black;
				width: 100%;
			}
			
			.cart {
				background-color: #f9f9f9;
				padding: 10px;
			}
    
			.cart h2 {
				font-size: 18px;
				margin-top: 0;
			}
			
			.cart ul {
				list-style-type: none;
				padding: 0;
			}
    
			.cart li {
				margin-bottom: 10px;
			}
    
			.cart button {
				background-color: #333;
				color: #fff;
				padding: 5px 10px;
				border: none;
				cursor: pointer;
			}
    
			.cart button:hover {
				background-color: #555;
			}


		</style>
		<meta charset="utf-8">
		<link rel="stylesheet" href="css/style.css" />
	</head>
	
	<body>
		<header>
			<h1>Luis Galerie</h1>
		</header>
		

		<!-- navigation bar -->
		<form method="post" action="">
			<div class="navbar">
				<ul id="cart-items"></ul>
				<button class="button" type="submit" name="checkout">Zur Kasse</button>
				<button class="button" type="submit" name="clear">Warenkorb leeren</button>
				<button class="button" type="submit" name="webshop">Startseite</button>
				<?php
					if (!(isset($_SESSION))) {
						session_start();
					}

					if ($_SESSION['admin']) {
						echo "<button class=\"button\" type=\"submit\" style=\"background-color: red;\" name=\"admin-view\">Admin-Sicht</button>";
					}
				?>
				<button class="button" type="submit" name="log_out">Abmelden</button>
			</div>
		
		
		<?php
			if (!(isset($_SESSION))) {
				session_start();
			}


			// get product data
			$host = "localhost";
			$username = "localhost";
			$password = "admin";
			$database = "luis_galerie";
			
			$connection = mysqli_connect($host, $username, $password, $database);
			if(!$connection){
				die("Connection failed: " . mysqli_connect_error());
			}
			
			$product_data = array();
			$product_data[] = array();
			
			$query = "SELECT * FROM product_data";
			$result = mysqli_query($connection, $query);
			
			foreach ($result as $product) {			
				$product_data[$product['ID']]['name'] = $product['name'];
				$product_data[$product['ID']]['price'] = $product['price'];

				$imageFilename[$product['ID']] = "image" . $product['ID'] . ".jpg";
				file_put_contents($imageFilename[$product['ID']], $product['image']);
				$product_data[$product['ID']]['image'] = $imageFilename[$product['ID']];
			}
				
			mysqli_close($connection);
			
			echo "<main>";
			echo "<h1>Warenkorb</h1>";
			
			$cart_price = 0;
			

			// display products in cart
			echo "<div class=\"container\">";
			if (isset($_SESSION['cartItems']) && count($_SESSION['cartItems']) > 0 && $product_data !== false) {
				foreach ($_SESSION['cartItems'] as $itemID) {
					foreach ($result as $product) {
						if ($itemID == $product['ID']) {
							echo "<div class=\"product\">";
							echo "<img src=\"" . $product_data[$product['ID']]['image'] . "\" style=\"width: 100%; height: auto;\" alt=\"Produkt " . $product_data[$product['ID']]['name'] . "\">";
							echo "<h2>" . $product_data[$product['ID']]['name'] . "</h2>";
							echo "<p>Preis: " . $product_data[$product['ID']]['price'] . "€</p>";
							echo "<button type=\"submit\" name=\"" . $product_data[$product['ID']]['name'] . "\" value=\"" . $product_data[$product['ID']]['name'] . "\">Aus Warenkorb löschen</button>";
							echo "</div>";
							$cart_price += $product_data[$product['ID']]['price'];
						}
					}
				}
			}
			echo "</div>";
			
			

			// display cart price
			echo "<hr>";
			
			echo "<h2>Zu zahlender Betrag</h2>";
			echo $cart_price . "€";
			echo "<br></br>";
			echo "<button class=\"button\" type=\"submit\" name=\"buy_cart\">Warenkorb kaufen</button>";
			
			echo "</main>";
			echo "</form>";
			


			// button logic
			if ($_SERVER['REQUEST_METHOD'] === 'POST') {
				
				foreach ($result as $product) {
					if (isset($_POST[$product_data[$product['ID']]['name']])) { 
						deleteFromCart($product['ID']);
					}
				}

				if (isset($_POST['buy_cart'])) {
					if ($cart_price > 0) {
						clearCart();
					}
					
				}
				
				if (isset($_POST['clear'])) {
					clearCart();
				}
				
				elseif (isset($_POST['checkout'])) {
					header("Location: cart.php");
				}
				
				elseif (isset($_POST['webshop'])) {
					header("Location: webshop.php");
				}

				elseif (isset($_POST['admin-view'])) {
					header("Location: admin-view.php");
				}
				
				elseif (isset($_POST['log_out'])) {
					unset($_SESSION);
					unset($_POST);
					session_destroy();
					header("Location: index.php");
					exit;
				}
			}
			

			// defined functions to use
			function deleteFromCart($item) {		
				for ($i=0; $i<count($_SESSION['cartItems']); $i++) {
					if ($_SESSION['cartItems'][$i] == $item) {
						unset($_SESSION['cartItems'][$i]);
					}
				}
				header("Location: cart.php");
			}
			
			function clearCart() {
				if (isset($_SESSION['cartItems'])) {
					$_SESSION['cartItems'] = array();
				}
				header("Location: cart.php");
			}
			
			function checkout() {
				if (isset($_SESSION['cartItems']) && count($_SESSION['cartItems']) > 0) {
					header("Location: cart.php");
				}
			}
		?>
	</body>
</html>