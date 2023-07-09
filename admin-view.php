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

		<main>

        <h2>Produkte hinzufügen</h2>
        <br>
        <table>
            <form action="" method="post" enctype="multipart/form-data">
                <tr>
                    <td>Titel</td>
                    <td>
                        <input type="text" name="title" value="" size="30" maxlength="50">
                    </td>
                </tr>
                <tr>
                    <td>Preis</td>
                    <td>
                        <input type="text" name="price" value="" size="30" maxlength="50">
                    </td>
                </tr>
                <tr>
                    <td>Bild</td>
                    <td>
                        <input type="file" name="file">
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td><button class="button" type="submit" name="addProduct">Hinzufügen</button></td>
                </tr>
            </form>
        </table>

        <?php
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

				$imageFilename[$product['ID']] = "image" . $product['ID'] . ".jpg";
				file_put_contents($imageFilename[$product['ID']], $product['image']);
				$product_data[$product['ID']]['image'] = $imageFilename[$product['ID']];
			}


			// check input fields
			$flag = False;
			echo "<br>";

			if (isset($_POST['title']) && isset($_POST['price']) && isset($_FILES['file'])) {
				if (!(strlen($_POST['title'] > 0))) {
					$flag = True;
					echo "Geben Sie bitte einen Produktnamen ein.";
					echo "<br>";
				}

				if (!(is_numeric($_POST['price']))) {
					$flag = True;
					echo "Geben Sie für den Preis bitte eine ganze Zahl oder eine Dezimalzahl ein.";
					echo "<br>";
				}

				$validImageExtensions = ['jpg' , 'png' , 'jpeg'];
				$imageExtension = explode('.', $_FILES['file']['name']);
				$imageExtension = end($imageExtension);
				if (!(in_array($imageExtension, $validImageExtensions))) {
					$flag = True;
					echo "Fügen Sie bitte ein gültiges Bildformat ein";
					echo "<br>";
				}
			}

			if (!$flag && isset($_POST['addProduct'])) {
				$title = $_POST['title'];
				$price = $_POST['price'];

				$image = "image" . $imageExtension;
				file_put_contents($image, $_FILES['file']['tmp_name']);

				$sql = "INSERT INTO product_data(ID, name, price, image) VALUES('', '$title', '$price', '$image')";
				mysqli_query($connection, $sql);
			}


			mysqli_close($connection);

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
        ?>

		</main>
    </body>
</html>