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
		<font size="50"><b>Welcome <?php echo $_SESSION['userName'] ?></b></font>
	</div>
	<br>
	<hr>
	<br>
	<div>
		<form action="Board.php" method="GET">
			<div>
				<label><b>Enter The Post Below</b></label><br/><br/>
				<textarea rows="5" cols="50" name="postData" id="postDataId"></textarea><br/><br/>
				<input type="submit" value = "Submit Post">
			</div>
		</form>
		<?php 
		
			if(isset($_GET['postData']) && !isset($_GET['messageId'])) {
				if($_GET['postData'] != null) {
					
					$_postId = uniqid();
					$_userName = $_SESSION['userName'];
					$_postDescription = $_GET['postData'];
					
					$_instance = DatabaseConnection::getInstance();
					$_dsnObject = $_instance->getConnection();
					
					$_query = $_dsnObject->prepare('INSERT INTO POSTS VALUES (?,null,?,NOW(),?)');
					$_query->execute([$_postId,$_userName,$_postDescription]);
				}
			}
			
			if(isset($_GET['messageId']) && isset($_GET['tempPostData'])) {
				
				if($_GET['tempPostData'] != null) {
					
					$_postId = uniqid();
					$_messageId = $_GET['messageId'];
					$_userName = $_SESSION['userName'];
					$_postDescription = $_GET['tempPostData'];
					
					$_instance = DatabaseConnection::getInstance();
					$_dsnObject = $_instance->getConnection();
					
					$_query = $_dsnObject->prepare('INSERT INTO POSTS VALUES (?,?,?,NOW(),?)');
					$_query->execute([$_postId,$_messageId,$_userName,$_postDescription]);
					
				}
			}
			
		?>
		<br/>
		<hr>
		<?php
		
			$_instance = DatabaseConnection::getInstance();
			$_dsnObject = $_instance->getConnection();
			
			$_result = $_dsnObject->query('SELECT id, postedby, fullname, datetime, replyto, message FROM POSTS, USERS WHERE POSTEDBY = USERNAME order by datetime desc;');
			$_resultSet = $_result->fetch();
			
			if($_resultSet) {
				echo '<div>';
				while($_resultSet) {
					
					echo '<form name = "form_'.$_resultSet['id'].'" action = "Board.php" method = "GET">';
					echo '<div>';
					echo '<label>Message Id : '.$_resultSet['id'].'</label><br/>';
					echo '<label>Username : '.$_resultSet['postedby'].'</label><br/>';
					echo '<label>Full Name : '.$_resultSet['fullname'].'</label><br/>';
					echo '<label>Date and Time : '.$_resultSet['datetime'].'</label><br/>';
					echo '<label>Reply to Message Id : '.$_resultSet['replyto'].'</label><br/>';
					echo '<label>Message :- </label><br/>';
					echo '<p>'.$_resultSet['message'].'</p>';
					echo '<input type = "hidden" name = "messageId" value = "'.$_resultSet['id'].'">';
					echo '<input type = "hidden" id = "post_'.$_resultSet['id'].'" name = "tempPostData">';
					?>
					<br/>
					<input type = "submit" value = "Reply" onclick ="getPostTextArea('<?php echo $_resultSet['id'] ?>')">
					<br/>
					<?php
					echo '<br/>';
					echo '</div>';
					echo '</form>';
					$_resultSet = $_result->fetch();
				}
				echo '</div>';
			}
			else {
				echo '<br/><br/>';
				echo '<label>No posts available</label>';
			}
			
		?>
		<br/>
		<hr>
		<br/>
		
		<form action="Login.php" method="GET">
			<input type="hidden" name="call" value='logout'>
			<input type="submit" value="Logout">
		</form>
		
		<?php 
			
			if(isset($_GET['call'])) {
				$_SESSION['userName'] = null;
				header('Location:Login.php');
			}
			
		?>
		
		<script type="text/javascript">
		
			function getPostTextArea(messageId) {
				var message  = document.getElementById("postDataId").value;
				document.getElementById("post_"+messageId).value = message;
			}
			
		</script>
	</div>
</body>
</html>