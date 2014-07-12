<?php
 
if( isset( $_POST["y"] ) || isset( $_GET["y"] ))
{
	$task_id = isset($_GET["y"]) ? $_GET["y"] : $_POST["y"];
}

//define the query
$sql="SELECT * FROM tasks WHERE task_id='$task_id'";
$result = mysql_query($sql, $conn) or die('Could not connect: ' . mysql_error());

$row = mysql_fetch_row($result);

$task_id=$row[0];
$project_id=$row[1];
$dev_id=$row[2];
$task_name=$row[3];
$complete_time=$row[4];
$spent_time=$row[5];
$complete=$row[6];
$task_desc=$row[7];		
$notes=$row[8];		

$minutes1 = $complete_time % 60;
$hours1 = ($complete_time-$minutes1)/60;	

$minutes2 = $spent_time % 60;
$hours2 = ($spent_time-$minutes2)/60;	

if (getUserLevel()=="1" || taskAuth($task_id)) {

	echo "<div id=\"contentContainer\">
		<div class=\"leftContainer\">
		<div class=\"header\">
			editing task: $task_name
		</div>

		<div class=\"content\">";
	
	    if(isset($_POST['submit'])){
		$task_name=$_POST['task_name']; 
		$task_desc=$_POST['task_desc'];
		$hours1=$_POST['hours1'];
		$hours2=$_POST['hours2'];
		$minutes1=$_POST['minutes1'];
		$minutes2=$_POST['minutes2'];
		$notes=$_POST['notes'];

		if($task_name==""){
		    echo "You didn't fill in a required field.";  
		}
		else if (preg_match("/([\<])([^\>]{1,})*([\>])/i", $task_name) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $task_desc) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $notes) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $hours1) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $hours2) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $minutes1) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $minutes2)) {
			echo "Data cannot contain HTML.";
		}
		else if ((strlen($task_name) != strlen(strip_tags($task_name))) || (strlen($task_desc) != strlen(strip_tags($task_desc))) || (strlen($hours1) != strlen(strip_tags($hours1))) || (strlen($hours2) != strlen(strip_tags($hours2))) || (strlen($minutes1) != strlen(strip_tags($minutes1))) || (strlen($minutes2) != strlen(strip_tags($minutes2))) || (strlen($notes) != strlen(strip_tags($notes)))) {
			echo "Data cannot contain HTML.";
		}
		else {
		    echo "Task $task_name successfully edited.";
		    editTask();
		}
	    }
	    else {

		echo "<center>";
		echo "<table border=\"0\" width=\"400\"><tr><td>";
		echo "<font class=\"required\">*</font> = Required Field<br/><br/>";

		echo "<form enctype=\"multipart/form-data\" action=\"\" method=\"post\">\n";
		echo "<input type=\"hidden\" value=\"$task_id\" maxlength=\"3\" name=\"task_id\" size=\"25\" />";
		echo "<input type=\"hidden\" value=\"$project_id\" maxlength=\"3\" name=\"project_id\" size=\"25\" />";
		echo "<label for=\"dev_id\">Developer: </label>";

		$sql="SELECT * FROM users WHERE role='3'";
		$result = mysql_query($sql, $conn) or die('Could not connect: ' . mysql_error());
		echo "<select name=\"dev_id\">";
		while ($row=mysql_fetch_assoc($result)) {
		if ($row['id']==$dev_id) {
		echo "<option value=\"".$row['id']."\" selected>".$row['first_name']." ".$row['last_name']."</option>";
		}
		else {
		echo "<option value=\"".$row['id']."\">".$row['first_name']." ".$row['last_name']."</option>";
		}
		}
		echo "</select><br/> \n";

		echo "<label for=\"task_name\"><font class=\"required\">*</font> Task Name: </label><input type=\"text\" value=\"$task_name\" maxlength=\"25\" name=\"task_name\" size=\"64\" /><br />\n";
		echo "<label for=\"task_desc\">Task Description: </label><textarea cols=\"80\" maxlength=\"150\" rows=\"2\" wrap=\"ON\" name=\"task_desc\">$task_desc</textarea><br /><br/>\n";
		echo "<label for=\"complete_time\">Time Until Completion: </label>
		&nbsp;&nbsp;&nbsp; Hours: <input type=\"text\" value=\"$hours1\" maxlength=\"3\" name=\"hours1\" size=\"25\" />
		&nbsp;&nbsp;&nbsp; Minutes: <input type=\"text\" value=\"$minutes1\" maxlength=\"2\" name=\"minutes1\" size=\"25\" /><br /><br />\n";
		echo "<label for=\"spent_time\">Time Worked: </label>
		&nbsp;&nbsp;&nbsp; Hours: <input type=\"text\" value=\"$hours2\" maxlength=\"3\" name=\"hours2\" size=\"25\" /> 
		&nbsp;&nbsp;&nbsp; Minutes: <input type=\"text\" value=\"$minutes2\" maxlength=\"2\" name=\"minutes2\" size=\"25\" /><br /><br />\n";


		if ($complete=='1') {
		echo "<label class=\"complete\" for=\"complete\">Complete: </label><input type=\"checkbox\" name=\"complete\" class=\"complete\" checked/><br /><br />\n";
		}
		else {
		echo "<label class=\"complete\" for=\"complete\">Complete: </label><input type=\"checkbox\" name=\"complete\" class=\"complete\" /><br /><br />\n";
		}

		echo "<label for=\"notes\">Notes: </label><textarea cols=\"80\" rows=\"6\" wrap=\"ON\" name=\"notes\">$notes</textarea><br /><br />\n";
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
