<?php

if (getUserLevel()=="1") {

	echo "<div id=\"contentContainer\">
		<div class=\"leftContainer\">
		<div class=\"header\">
			add new client
		</div>

		<div class=\"content\">";

	if(isset($_POST['submit'])){

		$first_name=$_POST['first_name']; 
		$last_name=$_POST['last_name']; 
		$ph_num=$_POST['ph_num'];
		$email_addr=$_POST['email_addr'];
	    
		if($first_name=="" || $last_name=="" || $ph_num=="" || $email_addr==""){
			echo "You didn't fill in a required field.";  
		}
		else if (preg_match("/([\<])([^\>]{1,})*([\>])/i", $first_name) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $last_name) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $ph_num) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $email_addr)) {
			echo "Data cannot contain html";
		}
		else if ((strlen($first_name) != strlen(strip_tags($first_name))) || (strlen($last_name) != strlen(strip_tags($last_name))) || (strlen($ph_num) != strlen(strip_tags($ph_num))) || (strlen($email_addr) != strlen(strip_tags($email_addr)))){
			echo "Data cannot contain HTML";
		}
		else if (!preg_match('/^[a-z0-9&\'\.\-_\+]+@[a-z0-9\-]+\.([a-z0-9\-]+\.)*+[a-z]{2}/is', $email_addr)) {
			echo "Please enter a valid email address.";
		}
		else if (!(preg_match("/^[0-9]{3,3}[-]{1,1}[0-9]{3,3}[-]{1,1}[0-9]{4,4}$/", $ph_num))) {
			echo "Please enter a valid phone number.";
		}
 		else {
			echo "$first_name $last_name successfully added to database.";
			addClient();
		}
	}
	else {

		echo "<center>";
		echo "<table border=\"0\" width=\"400\"><tr><td>";
	    	echo "<font class=\"required\">*</font> = Required Field<br/><br/>";

		echo "<form enctype=\"multipart/form-data\" action=\"\" method=\"post\">\n";

		echo "<input type=\"hidden\" value=\"\" maxlength=\"64\" name=\"client_id\" size=\"25\" />";

		echo "<label for=\"first_name\"><font class=\"required\">*</font> First Name: </label><input type=\"text\" value=\"$first_name\" maxlength=\"30\" name=\"first_name\" size=\"64\" /><br />\n";

		echo "<label for=\"last_name\"><font class=\"required\">*</font> Last Name: </label><input type=\"text\" value=\"$last_name\" maxlength=\"30\" name=\"last_name\" size=\"64\" /><br />\n";

		echo "<label for=\"ph_num\"><font class=\"required\">*</font> Phone Number (123-456-7890): </label><input type=\"text\" value=\"$ph_num\" maxlength=\"12\" name=\"ph_num\" size=\"64\" /><br />\n";

		echo "<label for=\"email_addr\"><font class=\"required\">*</font> E-mail Address: </label><input type=\"text\" value=\"$email_addr\" maxlength=\"200\" name=\"email_addr\" size=\"64\" /><br /><br />\n";

		echo "<input type=\"submit\" name=\"submit\" value=\"Add New Client\" class=\"send\">";
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

