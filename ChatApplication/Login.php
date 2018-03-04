<!DOCTYPE html>
<html>
<head>
<meta charset="${encoding}">
<title>Chat Application</title>
</head>
<body>
	<?php 
		
		session_start();
		include 'DatabaseConnection.php';
		
	?>
	<div align="center">
		<font size="50"><b>Chat Application</b></font>
	</div>
	<br>
	<hr>
	<br>
	<div>
		<form action="Login.php" method="GET">
			<label><b>Enter The Credentials</b></label><br/><br/>
			<input type="text" name="userName" placeholder="Username"><br/>
			<input type="password" name="passWord" placeholder="Password"><br/><br/>
			<input type="submit" value="Submit">
		</form>
	</div>
	<?php 
		if(isset($_GET['userName']) && isset($_GET['passWord'])) {
			
			$_userName = $_GET['userName'];
			$_password = $_GET['passWord'];
			
			$_SESSION['userName'] = $_userName;
			
			$_instance = DatabaseConnection::getInstance();
			$_dsnObject = $_instance->getConnection();
			
			$_query = $_dsnObject->prepare('select * from users where username = ? and password = ?');
			$_query->execute([$_userName,md5($_password)]);
			$_result = $_query->fetch(PDO::FETCH_ASSOC);
			
			if($_result) {
				header('Location:Board.php');
			}
			else {
				echo '<br/>';
				echo 'Please enter a valid Username and Password';
			}
		}
	?>
</body>
</html>
