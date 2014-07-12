<?php

	
if (getUserLevel()=="1" || getUserLevel()=="2") {

	echo "<div id=\"contentContainer\">
		<div class=\"leftContainer\">
		<div class=\"header\">
			create new task
		</div>

		<div class=\"content\">";
	
	    if(isset($_POST['submit'])){
			$task_name=$_POST['task_name']; 
		
		if($task_name==""){
		    echo "You didn't fill in a required field.";  
		}
		else if (preg_match("/([\<])([^\>]{1,})*([\>])/i", $task_name) || preg_match("/([\<])([^\>]{1,})*([\>])/i", $task_desc)) {
			echo "Data cannot contain HTML.";
		}
		else if ((strlen($task_name) != strlen(strip_tags($task_name))) || (strlen($task_desc) != strlen(strip_tags($task_desc)))) {
			echo "Data cannot contain HTML.";
		}
		else {
		    echo "Task $task_name successfully created.";
		    createTask();
		}
	    }
	    else {

		echo "<center>";
		echo "<table border=\"0\" width=\"400\"><tr><td>";
		echo "<font class=\"required\">*</font> = Required Field<br/><br/>";

		echo "<form enctype=\"multipart/form-data\" action=\"\" method=\"post\">\n";
		echo "<input type=\"hidden\" value=\"\" maxlength=\"64\" name=\"client_id\" size=\"25\" />";
		echo "<label for=\"project_id\">Project: </label>";

		if (isset($_GET['y'])) {
			$project_id="".$_GET['y']."";
		}
		else {
			$project_id="";
		}

			$sql2="SELECT * FROM projects";
			$result2 = mysql_query($sql2, $conn) or die('Could not connect: ' . mysql_error());
			echo "<select name=\"project_id\">";
			while ($row2=mysql_fetch_assoc($result2)) {

				if ($row2['project_id']==$project_id) 
					echo "<option value=\"".$row2['project_id']."\" selected>".$row2['project_name']."</option>";
				else
					echo "<option value=\"".$row2['project_id']."\">".$row2['project_name']."</option>";
			}

		echo "</select><br/> \n";

		echo "<label for=\"dev_id\">Developer: </label>";

		$sql="SELECT * FROM users WHERE role='3'";
		$result = mysql_query($sql, $conn) or die('Could not connect: ' . mysql_error());
		echo "<select name=\"dev_id\">";
		while ($row=mysql_fetch_assoc($result)) {
			echo "<option value=\"".$row['id']."\">".$row['first_name']." ".$row['last_name']."</option>";
		}
		echo "</select><br/> \n";

		echo "<label for=\"task_name\"><font class=\"required\">*</font> Task Name: </label><input type=\"text\" value=\"\" maxlength=\"25\" name=\"task_name\" size=\"64\" /><br />\n";
		echo "<label for=\"task_desc\">Task Description: </label><textarea cols=\"80\" maxlength=\"150\" rows=\"2\" wrap=\"ON\" name=\"task_desc\"></textarea><br /><br />\n";
		echo "<label for=\"notes\">Notes: </label><textarea cols=\"80\" rows=\"6\" wrap=\"ON\" name=\"notes\"></textarea><br /><br />\n";
		echo "<input type=\"submit\" name=\"submit\" value=\"Create Task\" class=\"send\">";
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

