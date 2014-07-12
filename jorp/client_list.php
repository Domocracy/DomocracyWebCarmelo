<?php

//define the query

	if( isset( $_POST["y"] ) || isset( $_GET["y"] ))
	{
		$order = isset($_GET["y"]) ? $_GET["y"] : $_POST["y"];
	}

	if ($order=="last_name_a")
	{
		$sql="SELECT * FROM clients ORDER BY last_name ASC";
	}
	else if ($order=="last_name_d") {
		$sql="SELECT * FROM clients ORDER BY last_name DESC ";
	}
	else {
		$sql="SELECT * FROM clients";
	}

$result = mysql_query($sql, $conn) or die('Could not connect: ' . mysql_error());

$num = mysql_numrows($result);

echo "
	<div id=\"contentContainer\">
	<div class=\"leftContainer\">
	<div class=\"header\">
		client list
	</div>

	<div class=\"content\">

	<div class=\"sort_by\">SORT BY: Last Name [ <a href=\"?x=listClients&y=last_name_a\">Ascending</a> | <a href=\"?x=listClients&y=last_name_d\">Descending</a> ]</div>

";

if ($num==0) {
	echo "There are no clients in the database.";
}

while ($row=mysql_fetch_assoc($result)) {

	$client_id = "".$row['client_id']."";
	$sql2="SELECT * FROM projects WHERE client_id='$client_id'";
	$project_list = mysql_query($sql2, $conn) or die('Could not connect: ' . mysql_error());

	echo "

		<script type=\"text/javascript\">
		$(document).ready(function() {
			$('#".$row['client_id']."_bottom').hide();
			$('#".$row['client_id']."_top').click(function() {
				$(\"#".$row['client_id']."_bottom\").slideToggle(\"slow\");
			});
		});
		</script>

		<center>
		<div id=\"".$row['client_id']."_top\" class=\"client_top\">
			".$row['first_name']." ".$row['last_name']."
		</div>
		<div id=\"".$row['client_id']."_bottom\" class=\"client_bottom\">

		<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"5\" class=\"clientTable\">
		<tr>
		<td valign=\"left\" align=\"left\" class=\"email_addr\">
		<b>E-mail Address:</b> <a href=\"mailto:".$row['email_addr']."\">".$row['email_addr']."</a>
		</td>
		<td valign=\"left\" align=\"left\" class=\"ph_num\">
		<b>Phone Number:</b> ".$row['ph_num']."
		</td>
		</tr>
	";

	echo "
		<tr>
		<td valign=\"middle\" align=\"center\" class=\"projectNumHeader\" width=\"50%\"><b>Project ID</b></td>
		<td valign=\"middle\" align=\"center\" class=\"projectNameHeader\"><b>Project Name</b></td>
		</tr>
	";

	while ($row2=mysql_fetch_assoc($project_list)) {
		echo "
			<tr>
			<td valign=\"middle\" align=\"center\" class=\"projectNum\">".$row2['project_id']."</td>
			<td valign=\"middle\" align=\"center\" class=\"projectName\">".$row2['project_name']."</td>
			</tr>
		";
	};

	echo "
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
