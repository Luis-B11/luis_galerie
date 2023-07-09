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
			
			<!-- Login -->
			<h2 margin="20">Anmelden</h2>
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
						<td>&nbsp;</td>
						<td>&nbsp;</td>
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
							<a href="http://localhost:8080/register.php">Registrieren</a>
						</td>
					</tr>
				</table>
			</form>
			
			<br></br>
		
			<?php
				if (!(isset($_SESSION))) {
					session_start();	
				}
				
				if (isset($_SESSION['username']) && isset($_SESSION['password']) && isset($_SESSION['email'])) {
					header("Location: webshop.php");
				}
				

				//get user data
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
			
				
				// check for user in database
				$flag;
			
				if (isset($_POST['username']) && isset($_POST['password'])) {
					foreach ($result as $user) {
						if ($user_data[$user['ID']]['username'] == $_POST['username'] && $user_data[$user['ID']]['password'] == $_POST['password']) {
							$_SESSION['username'] = $user_data[$user['ID']]['username'];
							$_SESSION['password'] = $user_data[$user['ID']]['password'];
							$_SESSION['email'] = $user_data[$user['ID']]['email'];

							if ($_SESSION['username'] == "admin") {
								$_SESSION['admin'] = True;
							} else {
								$_SESSION['admin'] = False;	
							}
							
							header("Location: webshop.php");
						}
					}
					echo "Username oder Passwort ist falsch.";
				}	
			?>
		</main>
	</body>
</html>