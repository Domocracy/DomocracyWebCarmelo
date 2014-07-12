<?php

global $conn;

if( isset( $_POST["y"] ) || isset( $_GET["y"] ))
{
	$id = isset($_GET["y"]) ? $_GET["y"] : $_POST["y"];
}

if( isset( $_POST["z"] ) || isset( $_GET["z"] ))
{
	$error = isset($_GET["z"]) ? $_GET["z"] : $_POST["z"];
}

//define the query
$sql="SELECT * FROM users WHERE id='$id'";
$result = mysql_query($sql, $conn) or die('Could not connect: ' . mysql_error());

$row = mysql_fetch_row($result);
$id=$row[0];
$username=$row[1];
$password=$row[2];
$first_name=$row[3];
$last_name=$row[4];
$role=$row[5];
$email_addr=$row[6];
$ph_num=$row[7];	

if (userAuth($id)) {

	echo "<div id=\"contentContainer\">
		<div class=\"leftContainer\">
		<div class=\"header\">
			editing user: $username
		</div>

		<div class=\"content\">";

	if(isset($_POST['submit'])){

		$id=$_POST['id'];
		$first_name=$_POST['first_name']; 
		$last_name=$_POST['last_name']; 
		$ph_num=$_POST['ph_num'];
		$email_addr=$_POST['email_addr'];

		//define the query
		$sqlx="SELECT * FROM users WHERE id='$id'";
		$resultx = mysql_query($sqlx, $conn) or die('Could not connect: ' . mysql_error());
		$row=mysql_fetch_assoc($resultx);

		$currentpw = $row['password'];
		$currentpwentered = md5("".$_POST['password_current']."");
		$newpw = md5("".$_POST['password_new']."");

		if($first_name=="" || $last_name=="" || $ph_num=="" || $email_addr==""){
			echo "You didn't fill in a required field.";  
		}
		else if (preg_match("/([\<])([^\>]{1,})*([\>])/i", $first_name) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $last_name) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $ph_num) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $email_addr) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $password_current) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $password_new)) {
			echo "Data cannot contain html";
		}
		else if ((strlen($first_name) != strlen(strip_tags($first_name))) || (strlen($last_name) != strlen(strip_tags($last_name))) || (strlen($ph_num) != strlen(strip_tags($ph_num))) || (strlen($email_addr) != strlen(strip_tags($email_addr))) || (strlen($password_current) != strlen(strip_tags($password_current))) || (strlen($password_new) != strlen(strip_tags($password_new)))){
			echo "Data cannot contain HTML";
		}
		else if (!preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*+[a-z]{2}/is', $email_addr)) {
			echo "Please enter a valid email address.";
		}
		else if (!(preg_match("/^[0-9]{3,3}[-]{1,1}[0-9]{3,3}[-]{1,1}[0-9]{4,4}$/", $ph_num))) {
			echo "Please enter a valid phone number.";
		}
		else if (!$_POST['password_new']=="" && !$_POST['password_current']=="") {
			if ($currentpw!=$currentpwentered) {
				echo "Current password is incorrect.";
			}
			else {
				echo "Your profile was successfully edited!";
				editUser();
			}
		}
		else if (!$_POST['password_new']=="" && $_POST['password_current']=="") {
			echo "Please fill out both password fields.";
		}
		else if ($_POST['password_new']=="" && !$_POST['password_current']=="") {
			echo "Please fill out both password fields.";
		}
		else {
			echo "Your profile was successfully edited.";
			editUser();
		}

	}
	else {

		echo "<center>";
		echo "<table border=\"0\" width=\"400\"><tr><td>";
		echo "<font class=\"required\">*</font> = Required Field<br/><br/>";

		echo "<form enctype=\"multipart/form-data\" action=\"\" method=\"post\">\n";
		echo "<input type=\"hidden\" value=\"$id\" maxlength=\"30\" name=\"id\" size=\"25\" />";
		echo "<label for=\"username\">Username: </label><input disabled type=\"text\" value=\"$username\" maxlength=\"30\" name=\"username\" size=\"64\" /><br />\n";
		echo "<label for=\"first_name\"><font class=\"required\">*</font> First Name: </label><input type=\"text\" value=\"$first_name\" maxlength=\"30\" name=\"first_name\" size=\"64\" /><br />\n";
		echo "<label for=\"last_name\"><font class=\"required\">*</font> Last Name: </label><input type=\"text\" value=\"$last_name\" maxlength=\"30\" name=\"last_name\" size=\"64\" /><br />\n";
		echo "<label for=\"email_addr\"><font class=\"required\">*</font> E-mail Address: </label><input type=\"text\" value=\"$email_addr\" maxlength=\"200\" name=\"email_addr\" size=\"64\" /><br />\n";
		echo "<label for=\"ph_num\"><font class=\"required\">*</font> Phone Number (123-456-7890): </label><input type=\"text\" value=\"$ph_num\" maxlength=\"12\" name=\"ph_num\" size=\"64\" /><br /><br />\n";
		echo "<b>Change Password:</b><br/><label for=\"password_current\">Current Password: </label><input type=\"password\" value=\"\" maxlength=\"32\" name=\"password_current\" size=\"64\" /><br />\n";
		echo "<label for=\"password_new\">New Password: </label><input type=\"password\" value=\"\" maxlength=\"32\" name=\"password_new\" size=\"64\" /><br/><br />\n";
		echo "<input type=\"submit\" name=\"submit\" value=\"Submit Changes\" class=\"send\">";
		echo "</form>";
		echo "</td></tr></table>";
		echo "</center>";

	}
}

else {
	echo "<div id=\"contentContainer\">
		<div class=\"leftContainer\">
		<div class=\"header\">
			Access Denied
		</div>

		<div class=\"content\">";

	echo "You do not have permission to perform this function.";
}

echo "
	</div> <!--end content-->

	</div> <!--end left container-->

	<div class=\"rightContainer\">

		<div class=\"header\">
			options
		</div>

		<div class=\"content\"> 
";

include("sidebar.php"); 

echo "
		</div>

	</div> <!--end right container-->

	</div>
";

?>
