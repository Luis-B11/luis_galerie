<!DOCTYPE HTML>
<html>
	<head>
		<meta charset="utf-8">
		<title>Luis Galerie</title>
		<link rel="stylesheet" href="css/style.css" />
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
    
			main {
				margin: 20px;
			}
    
			h1 {
				font-size: 24px;
			}
			
			.submit_button {
				background-color: #333;
				color: #fff;
				padding: 5px 10px;
				border: none;
				cursor: pointer;
			}
			
			.submit_button:hover {
				background-color: #555;
			}
		</style>
	</head>
	
	<body>
		<header>
			<h1>Luis Galerie</h1>
		</header>
		<main>
			<h2>Registrieren</h2>
			
			<!-- Register -->
			<form method="post" action="">
				<table>
					<tr>
						<td>Benutzername:</td>
						<td>
							<input type="text" name="username" value="" size="30" maxlength="50">
						</td>
					</tr>
					<tr>
						<td>Passwort:</td>
						<td>
							<input type="password" name="password" value="" size="30" maxlength="50">
						</td>
					</tr>
					<tr>
						<td>E-Mail:</td>
						<td>
							<input type="text" name="email" value="" size="30" maxlength="50">
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>
							<button type="submit" class="submit_button">Senden</button>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td>
							<a href="http://localhost:8080/index.php">Anmelden</a>
						</td>
					</tr>
				</table>
			</form>
			
			<br></br>
			
			<?php
				if (!(isset($_SESSION))) {
					session_start();
				}

				// get user data
				$host = "localhost";
				$username = "localhost";
				$password = "admin";
				$database = "luis_galerie";
				
				$connection = mysqli_connect($host, $username, $password, $database);
				if(!$connection){
					die("Connection failed: " . mysqli_connect_error());
				}

				$user_data = array();
				$user_data[] = array();
				
				$query = "SELECT * FROM users";
				$result = mysqli_query($connection, $query);
				

				foreach ($result as $user) {
					$user_data[$user['ID']]['username'] = $user['username'];
					$user_data[$user['ID']]['password'] = $user['password'];
					$user_data[$user['ID']]['email'] = $user['email'];
				}
				
				mysqli_close($connection);



				// check if values are OK
				$flag = False;

				if (isset($_POST['username']) && isset($_POST['password'])) {
					if (strlen(trim($_POST['username'])) == 0 || strlen(trim($_POST['password'])) == 0 || strlen(trim($_POST['email'])) == 0) {
						$flag = True;
					}

					foreach ($result as $user) {
						if ($user_data[$user['ID']]['username'] == $_POST['username']) {
							echo "Dieser Username ist bereits vergeben.\nBitte wählen Sie einen anderen!";
							$flag = True;
							break;
						}

						if ($user_data[$user['ID']]['email'] == $_POST['email']) {
							echo "Diese E-Mail wird bereits verwendet.\nBitte wählen Sie eine andere!";
							$flag = True;
							break;
						}
					}

					if (!$flag && strlen($_POST['password']) < 6) {
						echo "Das Passwort muss mindestens 6 Zeichen lang sein.";
						$flag = True;
					}


					// insert new user into database
					if (!$flag) {
						$_SESSION['username'] = $_POST['username'];
						$_SESSION['password'] = $_POST['password'];
						$_SESSION['email'] = $_POST['email'];
						$_SESSION['admin'] = False;

						$host = "localhost";
						$username = "localhost";
						$password = "admin";
						$database = "luis_galerie";


						$connection = mysqli_connect($host, $username, $password, $database);
						if(!$connection){
							die("Connection failed: " . mysqli_connect_error());
						}

						$in_username = $_SESSION['username'];
						$in_password = $_SESSION['password'];
						$in_email = $_SESSION['email'];

						$query = "INSERT INTO users VALUES ('', '$in_username', '$in_password', '$in_email')";

						mysqli_query($connection, $query);

						mysqli_close($connection);

						header("Location: webshop.php");
					}
				}
			?>
		</main>
	</body>
</html>