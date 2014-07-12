<?
session_start(); 
include("index.php");
include("database.php");

/**
 * Delete cookies
 */
if(isset($_COOKIE['cookname']) && isset($_COOKIE['cookpass'])){
   setcookie("cookname", "", time()-60*60*24*100, "/");
   setcookie("cookpass", "", time()-60*60*24*100, "/");
}

if(!$logged_in){
}
else{

	$conn = mysql_connect("h50mysql91.secureserver.net", "redlinkts", "Redlink123") or die(mysql_error());
	$db=mysql_select_db('redlinkts', $conn) or die(mysql_error());

	$user_name = "".$_SESSION['username']."";	
	$status = "0";

	$sql = "UPDATE users 
		SET
			logged_in='$status'
		WHERE username='$user_name'";

	mysql_query($sql);


   /* Kill session variables */
   unset($_SESSION['username']);
   unset($_SESSION['password']);
   $_SESSION = array(); // reset session array
   session_destroy();   // destroy session.
}

echo "<META http-equiv=\"refresh\" content=\"1;URL=index.php\">";


?>
