
<html>
<head>
	<title>DropBox Application</title>
</head>
<body>
	<div style="height: 75px; text-align: center;">
		<font size="10"><b>DropBox Application</b></font>
	</div>
	
	<hr>
	
	<?php
	
	error_reporting(E_ALL);
	enable_implicit_flush();
	
	set_time_limit(0);
	
	require_once("DropboxClient.php");
	
	// you have to create an app at https://www.dropbox.com/developers/apps and enter details below:
	
	$dropbox = new DropboxClient(array(
			'app_key' => "ymq0c2moga24wib",      // Put your Dropbox API key here
			'app_secret' => "243xw1anmef1ybp",   // Put your Dropbox API secret here
			'app_full_access' => false,
	),'en');
	
	
	// first try to load existing access token
	$access_token = load_token("access");
	if(!empty($access_token)) {
		$dropbox->SetAccessToken($access_token);
		// echo "loaded access token:";
		// print_r($access_token);
	}
	elseif(!empty($_GET['auth_callback'])) // are we coming from dropbox's auth page?
	{
		// then load our previosly created request token
		$request_token = load_token($_GET['oauth_token']);
		if(empty($request_token)) die('Request token not found!');
	
		// get & store access token, the request token is not needed anymore
		$access_token = $dropbox->GetAccessToken($request_token);
		store_token($access_token, "access");
		delete_token($_GET['oauth_token']);
	}
	
	// checks if access token is required
	if(!$dropbox->IsAuthorized())
	{
		// redirect user to dropbox auth page
		$return_url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']."?auth_callback=1";
		$auth_url = $dropbox->BuildAuthorizeUrl($return_url);
		$request_token = $dropbox->GetRequestToken();
		store_token($request_token, $request_token['t']);
		die("Authentication required. <a href='$auth_url'>Click here.</a>");
	}
	
	?>
	
	<div>
		<h5>Upload the file to Dropbox</h5><br/>
		<form action="album.php" method="POST" enctype="multipart/form-data">
			Select the file here : <input type="file" name="uploadFile"><br/>
			<input type="submit" value="Upload File">
		</form>
	</div>
	
	<?php 
	
		if(isset($_GET['objectPath']) && isset($_GET['perform']) && $_GET['perform'] == "download") {
			$filePath = $_GET['objectPath'];
			$fileArray = $dropbox->GetFiles("",false);
			foreach ($fileArray as $key=>$value) {
				if((string)$filePath == (string)$value->path) {
					$download_dest = "download_".basename($value->path);
					$dropbox->DownloadFile($value,$download_dest);
					$imageLink = $dropbox->GetLink($value,false);
				}
			}
		}
		
		if(isset($_GET['objectPath']) && isset($_GET['perform']) && $_GET['perform'] == "delete") {
			$filePath = $_GET['objectPath'];
			$fileArray = $dropbox->GetFiles("",false);
			foreach ($fileArray as $key=>$value) {
				if((string)$filePath == (string)$value->path) {
					$dropbox->Delete($value->path);
				}
			}
		}
		
		$files = $dropbox->GetFiles("",false);
		
		if(isset($_FILES['uploadFile'])) {
			if($_FILES['uploadFile']['tmp_name'] != ""){
				$filePath = $_FILES['uploadFile']['tmp_name'];
				$fileName = $_FILES['uploadFile']['name'];
				$dropbox->UploadFile($filePath,$fileName);
			}
			else {
				echo "<br/>";
				echo "<br/>";
				echo "Please Select A File";
				echo "<br/>";
			}
			
		}
		
	?>
	
	<hr>
	
	<div>
		<div>
			<h3>Following is the list of the images</h3>
			<form action="album.php" method="GET">
				<table style="border:1px solid black ;">
					<thead>
						<tr>
							<th style="border:1px solid black ;">List of the Files</th>
							<th style="border:1px solid black ;">DownLoad Link</th>
							<th style="border:1px solid black ;">Delete File</th>
						</tr>
					</thead>
					<tbody>
					<?php
						$jsonObjectFiles = $dropbox->GetFiles("",false);
						foreach($jsonObjectFiles as $key => $value) {
						$fileImagePath = $dropbox->GetLink($value,false);
					?>
						<tr>
							<td style="border:1px solid black ;">
								<a href="#" onclick="getImageOfFunction('<?php echo $fileImagePath ?>');"><?php echo (string)$key; ?></a>
							</td>
							<td style="border:1px solid black ;">
								<input type="submit" value="Download" onclick="downloadFunction('<?php echo $value->path; ?>')">
							</td>
							<td style="border:1px solid black ;">
								<input type="submit" value="Delete" onclick="deleteFunction('<?php echo $value->path; ?>')">
							</td>
						</tr>
					<?php 
							}
					?>
					</tbody>
				</table>
				<input type="hidden" name="objectPath" id="objectPathId">
				<input type="hidden" name="perform" id="performId">
			</form>
		</div>
		<div>
			<img id="imageId" height="200" width="200">
		</div>
		<script type="text/javascript">
			function getImageOfFunction(filePathImage) {
				document.getElementById("imageId").src = filePathImage;
			}
			function downloadFunction(pathOfFile) {
				document.getElementById("objectPathId").value = pathOfFile;
				document.getElementById("performId").value = "download";
			}
			function deleteFunction(pathOfFile) {
				document.getElementById("objectPathId").value = pathOfFile;
				document.getElementById("performId").value = "delete";
			}
				
		</script>
	</div>
	
	
	<?php 
	
		function store_token($token, $name)
		{
			if(!file_put_contents("tokens/$name.token", serialize($token)))
				die('<br />Could not store token! <b>Make sure that the directory `tokens` exists and is writable!</b>');
		}
	
		function load_token($name)
		{
			if(!file_exists("tokens/$name.token")) return null;
			return @unserialize(@file_get_contents("tokens/$name.token"));
		}
	
		function delete_token($name)
		{
			@unlink("tokens/$name.token");
		}
	
	
		function enable_implicit_flush()
		{
			@apache_setenv('no-gzip', 1);
			@ini_set('zlib.output_compression', 0);
			@ini_set('implicit_flush', 1);
			for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
			ob_implicit_flush(1);
			echo "<!-- ".str_repeat(' ', 2000)." -->";
		}
	?>
</body>
</html>