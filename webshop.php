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
			
			.searchbar {
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
				scale: 1.1;
			}
			
			main {
				margin: 20px;
			}
    
			h1 {
				font-size: 24px;
			}
    
			.product {
				border: 1px solid #ccc;
				padding: 10px;
				margin-bottom: 20px;
			}
    
			.product img {
				max-width: 100%;
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
				margin: 5px;
				border: none;
				cursor: pointer;
				width: 150px;
			}
    
			.product button:hover {
				background-color: #555;
				scale: 1.1;
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
						echo "<button class=\"button\" style=\"background-color: red;\" type=\"submit\" name=\"admin-view\">Admin-Sicht</button>";
					}
				?>
				<button class="button" type="submit" name="log_out">Abmelden</button>
			</div>
		</form>
		

		<!-- search bar -->
		<form action="" method="post">
			<div class="searchbar">
				<ul id="searchbar">
				<input type="text" name="search" style="width: 35%" placeholder="Suche nach Bild">
				<button class="button" type="submit" name="searchbutton">Suchen</button>
			</div>
		</form>
			
		<main>
			<?php
				ob_start();
				//get product data
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
					$product_data[$product['ID']]['description'] = $product['description'];

					$imageFilename[$product['ID']] = "image" . $product['ID'] . ".jpg";
					file_put_contents($imageFilename[$product['ID']], $product['image']);
					$product_data[$product['ID']]['image'] = $imageFilename[$product['ID']];
				}
				
				mysqli_close($connection);
				

				// display products
				echo "<form action=\"\" method=\"post\">";
				if ($product_data !== false && !isset($_POST['searchbutton'])) {
					foreach ($result as $product) {
						echo "<div class=\"product\">";
						echo "<img src=\"" . $product_data[$product['ID']]['image'] . "\" style=\"width: 50%; height: auto;\" alt=\"Produkt " . $product_data[$product['ID']]['name'] . "\">";
						echo "<h2>" . $product_data[$product['ID']]['name'] . "</h2>";
						echo "<p>" . $product_data[$product['ID']]['description'] . "</p>";
						echo "<br>";
						echo "<p>Preis: " . $product_data[$product['ID']]['price'] . "€</p>";
						echo "<button type=\"submit\" name=\"" . $product_data[$product['ID']]['name'] . "\" value=\"" . $product_data[$product['ID']]['name'] . "\">In den Warenkorb</button>";
						if ($_SESSION['admin']) {
							echo "<button type=\"submit\" style=\"background-color: red;\" name=\"delete_" . $product_data[$product['ID']]['name'] . "\" value=\"" . $product_data[$product['ID']]['name'] . "\">Aus Angebot löschen</button>";
						}
						echo "</div>";
					}
				}

				// search bar logic
				if ($_SERVER['REQUEST_METHOD'] === 'POST' && $product_data !== false && isset($_POST['search']) && isset($_POST['searchbutton'])) {
					
					if(strlen($_POST['search']) == 0) {
						header("Location: webshop.php");
					}

					$fittingProducts = array();
					$searchStrings = explode(" ", $_POST['search']);
					foreach ($searchStrings as $searchWord) {
						foreach ($result as $product) {
							if (stripos(strtolower($product_data[$product['ID']]['name']), strtolower($searchWord)) !== false) {
								array_push($fittingProducts, $product_data[$product['ID']]);
							}
						}
					}

					if (count($fittingProducts) > 0) {	
						foreach ($fittingProducts as $searchProduct) {
							echo "<div class=\"product\">";
							echo "<img src=\"" . $searchProduct['image'] . "\" style=\"width: 50%; height: auto;\" alt=\"Produkt " . $searchProduct['name'] . "\">";
							echo "<h2>" . $searchProduct['name'] . "</h2>";
							echo "<p>" . $searchProduct['description'] . "</p>";
							echo "<br>";
							echo "<p>Preis: " . $searchProduct['price'] . "€</p>";
							echo "<button type=\"submit\" name=\"" . $searchProduct['name'] . "\" value=\"" . $searchProduct['name'] . "\">In den Warenkorb</button>";
							if ($_SESSION['admin']) {
								echo "<button type=\"submit\" style=\"background-color: red;\" name=\"delete_" . $searchProduct['name'] . "\" value=\"" . $searchProduct['name'] . "\">Aus Angebot löschen</button>";
							}
							echo "</div>";
						}
					} else {
						echo "Es wurden keine passenden Bilder zur Suche gefunden.";
					}
				
				} elseif (count($product_data) <= 0) {
				
					echo "Es sind derzeit leider keine Bilder verfügbar.";
				}

				echo "</form>";
				

				// button logic
				if ($_SERVER['REQUEST_METHOD'] === 'POST') {
					
					foreach ($result as $product) {
						if (isset($_POST["" . $product_data[$product['ID']]['name'] . ""])) { 
							addToCart($product['ID']);
						}

						if (isset($_POST["delete_" . $product_data[$product['ID']]['name'] . ""])) {
							$host = "localhost";
							$username = "localhost";
							$password = "admin";
							$database = "luis_galerie";

							$connection = mysqli_connect($host, $username, $password, $database);
							if(!$connection){
								die("Connection failed: " . mysqli_connect_error());
							}
							
							$query = "DELETE FROM product_data WHERE `product_data`.`ID` = " . $product['ID'];
							
							mysqli_query($connection, $query);
							
							mysqli_close($connection);

							header("Location: webshop.php");
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

				echo "</main>";
				

				// defined functions to use
				function addToCart($item) {
					if (!isset($_SESSION['cartItems'])) {
						$_SESSION['cartItems'] = array();
					}
				
					if (!in_array($item, $_SESSION['cartItems'])) {
						array_push($_SESSION['cartItems'], $item);
					}
				}
			
				function clearCart() {
					if (isset($_SESSION['cartItems'])) {
						$_SESSION['cartItems'] = array();
					}
					header("Location: webshop.php");
				}

				function contains($product_data, $search) {
					$fittingProducts = array();

					$searchStrings = explode(" ", $search);
					foreach ($searchStrings as $searchWord) {
						foreach ($product_data as $product) {
							if (stripos(strtolower($product_data[$product['ID']]['name']), strtolower($searchWord)) !== false) {
								array_push($fittingProducts, $product_data[$product['ID']]);
							}
						}
					}
					return $fittingProducts;
				}
				ob_end_flush();
			?>
	</body>
</html>