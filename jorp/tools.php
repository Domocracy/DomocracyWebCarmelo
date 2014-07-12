<?php

include('database.php');

if (getUserLevel()=="1") {

	echo "<div id=\"contentContainer\">
	    <div class=\"leftContainer\">

	    <div class=\"header\">
		    Tools
	    </div>

	    <div class=\"content\">";

	if(isset($_POST['submitClient'])){
	 
		$client_id=sanitize("".$_POST['client_id']."");

		$sql="DELETE FROM clients
		WHERE client_id='$client_id'";

		mysql_query($sql);

		echo "Client has been removed from the database.";
	}
	else if(isset($_POST['submitUser'])){
	 
		$id=sanitize("".$_POST['id']."");
		$user_id=getUserId();

		if ($user_id!=$id) {

			$sql="DELETE FROM users
			WHERE id='$id'";

			mysql_query($sql);

			echo "User has been removed from the database.";
		}
		else {
			echo "Error deleting user: User is currently logged in";
		}

	}

	else {

		echo "<center>
		<table border=\"0\" width=\"400\"><tr><td>";

	if (getUserLevel()=="1") {

		echo "<h2>Current Clients:</h2>";
		$sql="SELECT * FROM clients";
		$result = mysql_query($sql, $conn) or die('Could not connect: ' . mysql_error());
		echo "<form id=\"deleteClient\" enctype=\"multipart/form-data\" action=\"\" method=\"post\">\n";
		echo "<select name=\"client_id\">";
		while ($row=mysql_fetch_assoc($result)) {
			echo "<option value=\"".$row['client_id']."\">".$row['first_name']." ".$row['last_name']."</option>";
		}
		echo "</select><br/><br/> \n";
		echo "<input type=\"submit\" name=\"submitClient\" value=\"Delete Client\" class=\"send\"/>";
	  	echo "</form>";

		echo "<br/><br/>";

		echo "<h2>Current Users:</h2>";
		$sql2="SELECT * FROM users";
		$result2 = mysql_query($sql2, $conn) or die('Could not connect: ' . mysql_error());
		echo "<form id=\"deleteUser\" enctype=\"multipart/form-data\" action=\"\" method=\"post\">\n";
		echo "<select name=\"id\">";
		while ($row=mysql_fetch_assoc($result2)) {
			echo "<option value=\"".$row['id']."\">".$row['first_name']." ".$row['last_name']."</option>";
		}
		echo "</select><br/><br/> \n";
		echo "<input type=\"submit\" name=\"submitUser\" value=\"Delete User\" class=\"send\"/>";
	  	echo "</form>";

		echo "<br/><br/>";
	}
		echo "<h2>Send E-mail: </h2>";
		include("send_mail.php");

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

