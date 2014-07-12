<?php

	include("database.php");

function sanitize($inputvariable)
{
	$inputvariable = trim($inputvariable);
	$inputvariable = strip_tags($inputvariable);
	$inputvariable = mysql_real_escape_string($inputvariable);
	$inputvariable = rtrim($inputvariable);
	if (strpos($inputvariable,">"))
		$inputvariable = preg_replace(">","",$inputvariable);
	if (!get_magic_quotes_gpc()) { 
		$inputvariable=addslashes($inputvariable); 
	}
	return $inputvariable;
}

function table_exists($table, $db) {
	$tables = mysql_list_tables($db);
	while (list($temp)=mysql_fetch_array($tables)) {
		if ($temp==$table) {
			return true;
		}
	}
	return false;
}

function getUserLevel() {

	global $conn;

	$user_name = sanitize("".$_SESSION['username']."");

	$users = "SELECT role FROM users WHERE username='$user_name'";
	$user_list = mysql_query($users, $conn) or die('Could not connect: ' . mysql_error());
	$user_array = mysql_fetch_assoc($user_list);

	$user_role = "".$user_array['role']."";

	return $user_role;
}

function getUserId() {

	global $conn;

	$user_name = sanitize("".$_SESSION['username']."");

	$users = "SELECT id FROM users WHERE username='$user_name'";
	$user_list = mysql_query($users, $conn) or die('Could not connect: ' . mysql_error());
	$user_array = mysql_fetch_assoc($user_list);

	$user_id = "".$user_array['id']."";

	return $user_id;
}

function userAuth($id) {

	global $conn;

	$user_name = sanitize("".$_SESSION['username']."");

	$users = "SELECT id FROM users WHERE username='$user_name'";
	$user_list = mysql_query($users, $conn) or die('Could not connect: ' . mysql_error());
	$user_array = mysql_fetch_assoc($user_list);

	$user_id = $user_array['id'];

	if ($id==$user_id) {
		return true;
	}
	else {
		return false;
	}
}

function projectAuth($project_id) {

	global $conn;

	$user_name = sanitize("".$_SESSION['username']."");

	$users = "SELECT id FROM users WHERE username='$user_name'";
	$user_list = mysql_query($users, $conn) or die('Could not connect: ' . mysql_error());
	$user_array = mysql_fetch_assoc($user_list);

	$user_id = $user_array['id'];

	$project_id = sanitize($project_id);

	$projects = "SELECT id FROM projects WHERE project_id='$project_id'";
	$project_list = mysql_query($projects, $conn) or die('Could not connect: ' . mysql_error());
	$project_array = mysql_fetch_assoc($project_list);

	if ($project_array['id']!=$user_id) {
		return false;
	}
	else {
		return true;
	}
}

function taskAuth($task_id) {

	global $conn;

	$user_name = sanitize("".$_SESSION['username']."");

	$users = "SELECT id FROM users WHERE username='$user_name'";
	$user_list = mysql_query($users, $conn) or die('Could not connect: ' . mysql_error());
	$user_array = mysql_fetch_assoc($user_list);

	$user_id = $user_array['id'];

	$task_id = sanitize($task_id);
	
	$tasks = "SELECT * FROM tasks WHERE task_id='$task_id'";
	$task_list = mysql_query($tasks, $conn) or die('Could not connect: ' . mysql_error());
	$task_array = mysql_fetch_assoc($task_list);

	$project_id = $task_array['project_id']."";

	if ($task_array['dev_id']==$user_id || projectAuth($project_id)) {
		return true;
	}
	else {
		return false;
	}
}

function editProject() {

	global $conn;

	$client_id=sanitize($_POST['client_id']);
	$project_id=sanitize($_POST['project_id']);
	$id=sanitize($_POST['id']); 
	$project_name=sanitize($_POST['project_name']); 
	$project_desc=sanitize($_POST['project_desc']); 
	$deadline=sanitize($_POST['deadline']); 

	if (isset($_POST['complete'])) {
		$complete = 1;
	}
	else {
		$complete = 0;
	}

	$hours1=sanitize($_POST['hours1']);
	$minutes1=sanitize($_POST['minutes1']);
	$complete_time = ($hours1*60)+$minutes1;

	$hours2=sanitize($_POST['hours2']);
	$minutes2=sanitize($_POST['minutes2']);
	$spent_time = ($hours2*60)+$minutes2;

	$ftp_host=sanitize($_POST['ftp_host']); 
	$ftp_username=sanitize($_POST['ftp_username']); 
	$ftp_pass=sanitize($_POST['ftp_pass']); 

	if (isset($_POST['submit']))
	{
		$sql = "UPDATE projects
			SET 
				client_id='$client_id',
				id='$id',
				project_name='$project_name',
				project_desc='$project_desc',
				deadline='$deadline',
				complete_time='$complete_time',
				spent_time='$spent_time',
				complete='$complete',
				ftp_host='$ftp_host',
				ftp_username='$ftp_username',
				ftp_pass='$ftp_pass'
			WHERE
				project_id='$project_id'";
		mysql_query($sql);
	}

}

function isOnTime($id) {

	global $conn;

	$id = sanitize($id);

	//define the query
	$sql="SELECT * FROM projects WHERE project_id='$id'";
	$result = mysql_query($sql, $conn) or die('Could not connect: ' . mysql_error());
	$array = mysql_fetch_assoc($result);

	//convert deadline to date format
	$deadline = "".$array['deadline']."";
	$complete = $array['complete'];

	//get today's date
	$date = Date('Y-m-d');

	//subtract today's date from deadline & get time remaining in minutes
	$minutes = (strtotime($deadline) - strtotime($date)) / (60);

	//get time until completion
	$complete_time = "".$array['complete_time']."";

	//subtract time to complete from remaining time to get difference in minutes
	$difference = $minutes - $complete_time;

	if ($complete=='0') {
		if ($difference > 10080) {
			$onTime = "<span class=\"onTime\">ON SCHEDULE</span>";
		}
		else if (($difference < 10080) && ($difference > 0)) {
			$onTime = "<span class=\"due\">DUE SOON</span>";
		}
		else {
			if ($deadline=="0000-00-00") {
				$onTime = "<span class=\"attn\">NEEDS DEADLINE</span>";
			}
			else {
				$onTime = "<span class=\"late\">BEHIND SCHEDULE</span>";
			}
		}
	}
	else {
		$onTime = "<span class=\"complete\">COMPLETE</span>";
	}
	
	return $onTime;
}


function createProject() {

	global $conn;

	if (isset($_POST['submit']))
	{
		$project_id=sanitize($_POST['project_id']); 
		$id=sanitize($_POST['id']); 
		$client_id=sanitize($_POST['client_id']); 
		$project_name=sanitize($_POST['project_name']); 
		$project_desc=sanitize($_POST['project_desc']); 
		$deadline=sanitize($_POST['deadline']); 
		$ftp_host=sanitize($_POST['ftp_host']); 
		$ftp_username=sanitize($_POST['ftp_username']); 
		$ftp_pass=sanitize($_POST['ftp_pass']); 
		
		$sql = 	"INSERT INTO projects (project_id, id, client_id, project_name, project_desc, deadline, ftp_host, ftp_username, ftp_pass)
			VALUES ('$project_id', '$id', '$client_id', '$project_name', '$project_desc', '$deadline', '$ftp_host', '$ftp_username', '$ftp_pass')";
		mysql_query($sql);
	}
		
}

function editTask() {

	global $conn;

	if (isset($_POST['submit']))
	{
		$task_id=sanitize($_POST['task_id']);
		$project_id=sanitize($_POST['project_id']);
		$dev_id=sanitize($_POST['dev_id']); 
		$task_name=sanitize($_POST['task_name']); 
		$task_desc=sanitize($_POST['task_desc']); 
		$notes=sanitize($_POST['notes']); 
	
		$hours1 = sanitize($_POST['hours1']);
		$minutes1 = sanitize($_POST['minutes1']);
		$complete_time = ($hours1*60)+$minutes1;

		$hours2 = sanitize($_POST['hours2']);
		$minutes2 = sanitize($_POST['minutes2']);
		$spent_time = ($hours2*60)+$minutes2;
		
		if (isset($_POST['complete'])) {
			$complete = 1;
		}
		else {
			$complete = 0;
		}

		$sql = "UPDATE tasks 
			SET 
				dev_id='$dev_id',
				task_name='$task_name',
				task_desc='$task_desc',
				complete_time='$complete_time',
				spent_time='$spent_time',
				complete='$complete',
				notes='$notes'
			WHERE
				task_id='$task_id'";

		mysql_query($sql);

		$sql2="SELECT * FROM tasks WHERE project_id='$project_id'";
		$task_list = mysql_query($sql2, $conn) or die('Could not connect: ' . mysql_error());
		while ($row=mysql_fetch_assoc($task_list)) {
			$total_time += "".$row['complete_time']."";
			$total_time2 += "".$row['spent_time']."";
		};

		$sql3 = "UPDATE projects 
			SET 
				spent_time='$total_time2'
			WHERE
				project_id='$project_id'";
		mysql_query($sql3);

		$sql4 = "UPDATE projects 
			SET 
				complete_time='$total_time'
			WHERE
				project_id='$project_id'";
		mysql_query($sql4);

	}

}

function createTask() {

	global $conn;

	if (isset($_POST['submit']))
	{
		$task_id=sanitize($_POST['task_id']); 
		$project_id=sanitize($_POST['project_id']); 
		$dev_id=sanitize($_POST['dev_id']); 
		$task_name=sanitize($_POST['task_name']);
		$task_desc=sanitize($_POST['task_desc']);
		$notes=sanitize($_POST['notes']);

		$sql = 	"INSERT INTO tasks (task_id, project_id, dev_id, task_name, task_desc, notes)
			VALUES ('$task_id', '$project_id', '$dev_id', '$task_name', '$task_desc', '$notes')";

		mysql_query($sql);
	}

}

function addClient() {

	global $conn;

	if (isset($_POST['submit']))
	{
		$first_name=sanitize($_POST['first_name']); 
		$last_name=sanitize($_POST['last_name']); 
		$email_addr=sanitize($_POST['email_addr']); 
		$ph_num=sanitize($_POST['ph_num']); 

		$sql = 	"INSERT INTO clients (first_name,last_name,ph_num,email_addr)
			VALUES ('$first_name', '$last_name', '$ph_num', '$email_addr')";

		mysql_query($sql);
	}
}

function editUser() {

	global $conn;

	$id=sanitize($_POST['id']);
	$username=sanitize($_POST['username']);
	$first_name=sanitize($_POST['first_name']);
	$last_name=sanitize($_POST['last_name']); 
	$email_addr=sanitize($_POST['email_addr']); 
	$ph_num=sanitize($_POST['ph_num']); 

	if (!$_POST['password_new']=="" && !$_POST['password_current']=="") {
		$password=md5("".$_POST['password_new'].""); 
		$sql = "UPDATE users 
			SET 
				first_name='$first_name',
				last_name='$last_name',
				email_addr='$email_addr',
				ph_num='$ph_num',
				password='$password'
			WHERE
				id='$id'";
	}
	else {
		$sql = "UPDATE users 
			SET 
				first_name='$first_name',
				last_name='$last_name',
				email_addr='$email_addr',
				ph_num='$ph_num'
			WHERE
				id='$id'";
	}

	mysql_query($sql);
}

function deleteUser($y) {

	global $conn;
	if (getUserLevel()=="1") {

		$id = sanitize($y);
		 
		$sql="DELETE FROM users
			WHERE id='$id'";

		mysql_query($sql);
	}
	else {
		echo "You do not have permission to perform this function.";
	}
}

function deleteProject($y) {

	global $conn;

	$project_id = sanitize($y);

	$sql="DELETE FROM projects
		WHERE project_id='$project_id'";

	mysql_query($sql);

	$sql2="DELETE FROM tasks
		WHERE project_id='$project_id'";

	mysql_query($sql2);

	echo "<META http-equiv=\"refresh\" content=\"0;URL=index.php\">";
}

function deleteTask($y) {

	global $conn;

	$task_id = sanitize($y);
	 	 
	//define the query
	$sql="SELECT * FROM tasks WHERE task_id='$task_id'";
	$result = mysql_query($sql, $conn) or die('Could not connect: ' . mysql_error());
	$row = mysql_fetch_assoc($result);

	$project_id = "".$row['project_id']."";

	$sql2="SELECT * FROM projects WHERE project_id='$project_id'";
	$result2 = mysql_query($sql2, $conn) or die('Could not connect: ' . mysql_error());
	$row2 = mysql_fetch_assoc($result2);
	$project_complete_time = "".$row2['complete_time']."";
	$project_spent_time = "".$row2['spent_time']."";

	$task_complete_time = "".$row['complete_time']."";
	$project_complete_time -= $task_complete_time;

	$task_spent_time = "".$row['spent_time']."";
	$project_spent_time -= $task_spent_time;

	$sql3="DELETE FROM tasks
		WHERE task_id='$task_id'";

	mysql_query($sql3);

	$sql4 = "UPDATE projects 
		SET 
			spent_time='$project_spent_time',
			complete_time='$project_complete_time'
		WHERE
			project_id='$project_id'";

	mysql_query($sql4);

	echo "<META http-equiv=\"refresh\" content=\"0;URL=index.php\">";

}

function activeUsers() {

	global $conn;

	$status = "1";
	$sql = "SELECT * FROM users WHERE logged_in='$status'";
	$results = mysql_query($sql, $conn) or die('Could not connect: ' . mysql_error());
	$num = mysql_numrows($results);	
	$x = 1;
	while ($row=mysql_fetch_assoc($results)) {

		$username="".$row['username']."";
		echo $username;
		while ($x < $num) {
			echo ", ";
			$x++;
		}
	}
}

?>
