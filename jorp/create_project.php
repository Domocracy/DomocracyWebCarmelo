<?php

include('database.php');

if (getUserLevel()=="1") {

	echo "<div id=\"contentContainer\">
	    <div class=\"leftContainer\">

	    <div class=\"header\">
		    create new project
	    </div>

	    <div class=\"content\">";
	    
	if(isset($_POST['submit'])){

		$project_name=sanitize($_POST['project_name']); 
		$deadline=sanitize($_POST['deadline']);
	    
		if($project_name=="" || $deadline==""){
			echo "You didn't fill in a required field.";  
		}
		else if (preg_match("/([\<])([^\>]{1,})*([\>])/i", $project_name) || preg_match("/([\>])([^\>]{1,})*([\<])/i", $project_desc) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $deadline) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $ftp_host) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $ftp_username) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $ftp_pass) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $hours1) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $hours2) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $minutes1) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $minutes2)) {
			echo "Data cannot contain HTML.";
		}
		else if ((strlen($project_name) != strlen(strip_tags($project_name))) || (strlen($project_desc) != strlen(strip_tags($project_desc))) || (strlen($deadline) != strlen(strip_tags($deadline))) || (strlen($ftp_host) != strlen(strip_tags($ftp_host))) || (strlen($ftp_username) != strlen(strip_tags($ftp_username))) || (strlen($ftp_pass) != strlen(strip_tags($ftp_pass)))) {
			echo "Data cannot contain HTML.";
		}
		else if (!(preg_match("/^[0-9]{4,4}[-]{1,1}[0-9]{2,2}[-]{1,1}[0-9]{2,2}$/", $deadline))) {
			echo "Please enter a valid deadline";
		}
		else {
			echo "Project $project_name successfully created.";
			createProject();
		}
	}
	else {
	    
		    echo "<center>";
		    echo "<table border=\"0\" width=\"400\"><tr><td>";
		    echo "<font class=\"required\">*</font> = Required Field<br/><br/>";
		    echo "<form id=\"createProject\" enctype=\"multipart/form-data\" action=\"\" method=\"post\">\n";
		    echo "<input type=\"hidden\" value=\"\" maxlength=\"64\" name=\"project_id\" size=\"25\" />";

		    echo "<label for=\"id\">Manager: </label>\n";
		    $sql="SELECT * FROM users WHERE role='2'";
		    $result = mysql_query($sql, $conn) or die('Could not connect: ' . mysql_error());
		    echo "<select name=\"id\">";
		    while ($row=mysql_fetch_assoc($result)) {
			    echo "<option value=\"".$row['id']."\">".$row['first_name']." ".$row['last_name']."</option>";
		    }
		    echo "</select><br/> \n";

		    echo "<label for=\"client_id\">Client: </label>\n";
		    $sql2="SELECT * FROM clients";
		    $result2 = mysql_query($sql2, $conn) or die('Could not connect: ' . mysql_error());
		    echo "<select name=\"client_id\">";
		    while ($row2=mysql_fetch_assoc($result2)) {
			    echo "<option value=\"".$row2['client_id']."\">".$row2['first_name']." ".$row2['last_name']."</option>";
		    }
		    echo "</select><br/> \n";

		    echo "<label for=\"project_name\"><font class=\"required\">*</font> Project Name: </label><input type=\"text\" value=\"\" maxlength=\"25\" name=\"project_name\" size=\"64\" /><br />\n";
		    echo "<label for=\"project_desc\">Project Description: </label><textarea cols=\"80\" maxlength=\"150\" rows=\"2\" wrap=\"ON\" name=\"project_desc\"></textarea><br />\n";
		    echo "<label for=\"deadline\"><font class=\"required\">*</font> Deadline (YYYY-MM-DD): </label><input type=\"text\" value=\"\" maxlength=\"10\" name=\"deadline\" size=\"25\" /><br />\n";
		    echo "<label for=\"ftp_host\">FTP Host: </label><input type=\"text\" value=\"\" maxlength=\"30\" name=\"ftp_host\" size=\"25\" /><br />\n";
		    echo "<label for=\"ftp_username\">FTP Username: </label><input type=\"text\" value=\"\" maxlength=\"25\" name=\"ftp_username\" size=\"25\" /><br />\n";
		    echo "<label for=\"ftp_pass\">FTP Password: </label><input type=\"password\" value=\"\" maxlength=\"25\" name=\"ftp_pass\" size=\"25\" /><br /><br />\n";
		    echo "<input type=\"submit\" name=\"submit\" value=\"Create Project\" class=\"send\">";
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

