<?php

//define the query
	if( isset( $_POST["y"] ) || isset( $_GET["y"] ))
	{
		$order = isset($_GET["y"]) ? $_GET["y"] : $_POST["y"];
	}

	if ($order=="last_name_a")
	{
		$sql="SELECT * FROM users ORDER BY last_name ASC";
	}
	else if ($order=="last_name_d") {
		$sql="SELECT * FROM users ORDER BY last_name DESC ";
	}
	else {
		$sql="SELECT * FROM users";
	}

$result = mysql_query($sql, $conn) or die('Could not connect: ' . mysql_error());

$num = mysql_numrows($result);

echo "

	<div id=\"contentContainer\">
	<div class=\"leftContainer\">

	<div class=\"header\">
		user list
	</div>

	<div class=\"content\">

	<div class=\"sort_by\">SORT BY: Last Name [ <a href=\"?x=listUsers&y=last_name_a\">Ascending</a> | <a href=\"?x=listUsers&y=last_name_d\">Descending</a> ]</div>
";

while ($row=mysql_fetch_assoc($result)) {

	echo "
		<center>

		<script type=\"text/javascript\">
		$(document).ready(function() {
			$('#".$row['id']."_bottom').hide();
			$('#".$row['id']."_top').click(function() {
				$(\"#".$row['id']."_bottom\").slideToggle(\"slow\");
			});
		});
		</script>

		<center>
		<div id=\"".$row['id']."_top\" class=\"user_top\">
			".$row['first_name']." ".$row['last_name']."
		</div>
		<div id=\"".$row['id']."_bottom\" class=\"user_bottom\">


		<table border=\"0\" width=\"90%\" cellspacing=\"0\" cellpadding=\"5\" class=\"userTable\">
		<tr>
		<td valign=\"left\" align=\"left\" class=\"email_addr\">
		<b>E-mail Address:</b> <a href=\"mailto:".$row['email_addr']."\">".$row['email_addr']."</a>
		</td>
		<td valign=\"left\" align=\"left\" class=\"ph_num\">
		<b>Phone Number:</b> ".$row['ph_num']."
		</td>
		</tr>
		<tr>
		<td valign=\"middle\" align=\"center\" class=\"userNumHeader\" width=\"50%\"><b>User ID</b></td>
		<td valign=\"middle\" align=\"center\" class=\"roleHeader\"><b>Role</b></td>
		</tr>
	";

	$user_role = "".$row['role']."";
	$role_query = "SELECT role_name FROM roles WHERE role='$user_role'";
	$role_list = mysql_query($role_query, $conn) or die('Could not connect: ' . mysql_error());
	$role_array = mysql_fetch_assoc($role_list);

	echo "
		<tr>
		<td valign=\"middle\" align=\"center\" class=\"userNum\">".$row['id']."</td>
		<td valign=\"middle\" align=\"center\" class=\"role\">".$role_array['role_name']."</td>
		</tr>
		</table>
		</div>
		</center>
	";
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
