<?php

if( isset( $_POST["y"] ) || isset( $_GET["y"] ))
{
	$project_id = isset($_GET["y"]) ? $_GET["y"] : $_POST["y"];
}

//define the query
$sql="SELECT * FROM projects WHERE project_id='$project_id'";
$result = mysql_query($sql, $conn) or die('Could not connect: ' . mysql_error());

$row = mysql_fetch_row($result);

$project_id=$row[0];
$client_id=$row[1];
$id=$row[2];
$project_name=$row[3];
$project_desc=$row[4];
$complete_time=$row[5];
$spent_time=$row[6];
$deadline=$row[7];
$complete=$row[9];
$ftp_host=$row[10];
$ftp_username=$row[11];
$ftp_pass=$row[12];

$minutes1 = $complete_time % 60;
$hours1 = ($complete_time-$minutes1)/60;	

$minutes2 = $spent_time % 60;
$hours2 = ($spent_time-$minutes2)/60;	


if (getUserLevel()=="1" || projectAuth($project_id)) {

	echo "<div id=\"contentContainer\">
		<div class=\"leftContainer\">
		<div class=\"header\">
			editing project: $project_name
		</div>

		<div class=\"content\">";
	
    if(isset($_POST['submit'])){
	$project_name=$_POST['project_name']; 
        $deadline=$_POST['deadline'];
        $project_desc=$_POST['project_desc'];
        $hours1=$_POST['hours1'];
	$hours2=$_POST['hours2'];
        $minutes1=$_POST['minutes1'];
	$minutes2=$_POST['minutes2'];
	$ftp_host=$_POST['ftp_host'];
	$ftp_username=$_POST['ftp_username'];
	$ftp_pass=$_POST['ftp_pass'];
        
	if($project_name=="" || $deadline==""){
		echo "You didn't fill in a required field.";  
	}
	else if (preg_match("/([\<])([^\>]{1,})*([\>])/i", $project_name) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $project_desc) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $deadline) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $ftp_host) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $ftp_username) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $ftp_pass) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $hours1) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $hours2) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $minutes1) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $minutes2)) {
		echo "Data cannot contain HTML.";
	}
	else if ((strlen($project_name) != strlen(strip_tags($project_name))) || (strlen($project_desc) != strlen(strip_tags($project_desc))) || (strlen($deadline) != strlen(strip_tags($deadline))) || (strlen($ftp_host) != strlen(strip_tags($ftp_host))) || (strlen($ftp_username) != strlen(strip_tags($ftp_username))) || (strlen($ftp_pass) != strlen(strip_tags($ftp_pass)))) {
		echo "Data cannot contain HTML.";
	}
	else if (!(preg_match("/^[0-9]{4,4}[-]{1,1}[0-9]{2,2}[-]{1,1}[0-9]{2,2}$/", $deadline))) {
		echo "Please enter a valid deadline";
	}
        else {
            echo "Project $project_name successfully edited.";
            editProject();
        }
    }
    else {
	
        echo "<center>";
        echo "<table border=\"0\" width=\"400\"><tr><td>";
        echo "<font class=\"required\">*</font> = Required Field<br/><br/>";

        echo "<form enctype=\"multipart/form-data\" action=\"\" method=\"post\">\n";

        echo "<input type=\"hidden\" value=\"$project_id\" maxlength=\"64\" name=\"project_id\" size=\"25\" />";

        echo "<label for=\"client_id\">Client: </label>";

        $sql2="SELECT * FROM clients";
        $result2 = mysql_query($sql2, $conn) or die('Could not connect: ' . mysql_error());
        echo "<select name=\"client_id\">";
        while ($row2=mysql_fetch_assoc($result2)) {
	        if ($row2['client_id']==$client_id) {
		        echo "<option value=\"".$row2['client_id']."\" selected>".$row2['first_name']." ".$row2['last_name']."</option>";
	        }
	        else {
		        echo "<option value=\"".$row2['client_id']."\">".$row2['first_name']." ".$row2['last_name']."</option>";
	        }
        }
        echo "</select><br/> \n";

        echo "<label for=\"id\">Manager: </label>";

        $sql="SELECT * FROM users WHERE role='2'";
        $result = mysql_query($sql, $conn) or die('Could not connect: ' . mysql_error());
        echo "<select name=\"id\">";
        while ($row=mysql_fetch_assoc($result)) {
	        if ($row['id']==$id) {
		        echo "<option value=\"".$row['id']."\" selected>".$row['first_name']." ".$row['last_name']."</option>";
	        }
	        else {
		        echo "<option value=\"".$row['id']."\">".$row['first_name']." ".$row['last_name']."</option>";
	        }
        }
        echo "</select><br/> \n";


        echo "<label for=\"project_name\"><font class=\"required\">*</font> Project Name: </label><input type=\"text\" value=\"$project_name\" maxlength=\"25\" name=\"project_name\" size=\"64\" /><br />\n";

        echo "<label for=\"project_desc\">Project Description: </label><textarea cols=\"80\" maxlength=\"150\" rows=\"2\" wrap=\"ON\" name=\"project_desc\">$project_desc</textarea><br />\n";

        echo "<label for=\"deadline\"><font class=\"required\">*</font> Deadline (YYYY-MM-DD) </label><input type=\"text\" value=\"$deadline\" maxlength=\"10\" name=\"deadline\" size=\"25\" /><br /><br />\n";

        if ($complete=='1') {
	        echo "<input type=\"checkbox\" name=\"complete\" class=\"complete\" checked/> Complete<br /><br />\n";
        }
        else {
	        echo "<label class=\"complete\" for=\"complete\">Complete: </label><input type=\"checkbox\" name=\"complete\" class=\"complete\" /><br /><br />\n";
        }

        echo "<label for=\"complete_time\">Time Until Completion: </label>
        &nbsp;&nbsp;&nbsp; Hours: <input type=\"text\" value=\"$hours1\" maxlength=\"3\" name=\"hours1\" size=\"25\" />
        &nbsp;&nbsp;&nbsp; Minutes: <input type=\"text\" value=\"$minutes1\" maxlength=\"2\" name=\"minutes1\" size=\"25\" /><br /><br/>\n";

        echo "<label for=\"spent_time\">Time Worked: </label>
        &nbsp;&nbsp;&nbsp; Hours: <input type=\"text\" value=\"$hours2\" maxlength=\"3\" name=\"hours2\" size=\"25\" /> 
        &nbsp;&nbsp;&nbsp; Minutes: <input type=\"text\" value=\"$minutes2\" maxlength=\"2\" name=\"minutes2\" size=\"25\" /><br /><br/>\n";

        echo "<label for=\"ftp_host\">FTP Host: </label><input type=\"text\" value=\"$ftp_host\" maxlength=\"30\" name=\"ftp_host\" size=\"25\" /><br />\n";

        echo "<label for=\"ftp_username\">FTP Username: </label><input type=\"text\" value=\"$ftp_username\" maxlength=\"25\" name=\"ftp_username\" size=\"25\" /><br />\n";

        echo "<label for=\"ftp_pass\">FTP Password: </label><input type=\"password\" value=\"$ftp_pass\" maxlength=\"25\" name=\"ftp_pass\" size=\"25\" /><br /><br />\n";
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
