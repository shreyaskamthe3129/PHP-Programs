<?php
/** 
 * DropPHP sample
 *
 * http://fabi.me/en/php-projects/dropphp-dropbox-api-client/
 *
 * @author     Fabian Schlieper <fabian@fabi.me>
 * @copyright  Fabian Schlieper 2012
 * @version    1.1
 * @license    See license.txt
 *
 */
 

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
	echo "loaded access token:";
	print_r($access_token);
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

echo "<pre>";
echo "<b>Account:</b>\r\n";
print_r($dropbox->GetAccountInfo());

$files = $dropbox->GetFiles("",false);

if(empty($files)) {
   $dropbox->UploadFile("leonidas.jpg");
   $files = $dropbox->GetFiles("",false);
 }

echo "\r\n\r\n<b>Files:</b>\r\n";
print_r($files);

if(!empty($files)) {
        $file = reset($files);
	$test_file = "test_download_".basename($file->path);
	echo "The file is as follows<br/>";
	print_r($test_file);
	echo "<br/>";
	print_r($file);
	echo "Ends Here <br/>";
	
	echo "<img src='".$dropbox->GetLink($file,false)."'/></br>";
	echo "\r\n\r\n<b>Meta data of <a href='".$dropbox->GetLink($file)."'>$file->path</a>:</b>\r\n";
	echo "The data is as follows";
	echo "<br/>";
	print_r($dropbox->GetLink($file));
	echo "<br/>";
	echo "first parameter ends here";
	echo "<br/>";
	print_r($dropbox->GetMetadata($file->path));
	echo "<br/>";
	
	echo "$test_file</br>";
	echo "\r\n\r\n<b>Downloading $file->path:</b>\r\n";
	echo "<br/>";
	print_r($dropbox->DownloadFile($file, $test_file));
	echo "The info of download is as follows";
	echo "<br/>";
	print_r($file);
	echo "<br/>";
	echo "The info of first parameter is over";
	echo "<br/>";
	print_r($test_file);
	echo "<br/>";
	echo "The info of second parameter is over";
	echo "<br/>";
	echo "The info ended here";
		
	echo "\r\n\r\n<b>Uploading $test_file:</b>\r\n";
	print_r($dropbox->UploadFile($test_file));
	echo "\r\n done!";	
	
	echo "\r\n\r\n<b>Revisions of $test_file:</b>\r\n";	
	print_r($dropbox->GetRevisions($test_file));
}
	
echo "\r\n\r\n<b>Searching for JPG files:</b>\r\n";	
$jpg_files = $dropbox->Search("/", ".jpg", 5);
if(empty($jpg_files))
	echo "Nothing found.";
else {
	print_r($jpg_files);
	$jpg_file = reset($jpg_files);

	echo "\r\n\r\n<b>Thumbnail of $jpg_file->path:</b>\r\n";	
	$img_data = base64_encode($dropbox->GetThumbnail($jpg_file->path));
	echo "<img src=\"data:image/jpeg;base64,$img_data\" alt=\"Generating PDF thumbnail failed!\" style=\"border: 1px solid black;\" />";
}


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